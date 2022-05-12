<?php

namespace App\Http\Controllers\Api;

use App\Enums\PlanEnum;
use App\Exceptions\Api\BadRequestException;
use App\Exceptions\Api\ErrorException;
use App\Exceptions\Api\NotFoundException;
use App\Http\Requests\Api\CreateTripPlanRequest;
use App\Http\Requests\Api\CreateTripRequest;
use App\Http\Requests\Api\UpdateTripRequest;
use App\Models\ActivityPlan;
use App\Models\FlightPlan;
use App\Models\LodgingPlan;
use App\Models\Plan;
use App\Models\TransportPlan;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TripController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $trips = Trip::where('user_id', $user->id)
            ->orderBy('date_from', 'asc')
            ->get();

        return $this->responseSuccess('Success', [
            'trips' => $trips
        ]);
    }

    public function store(CreateTripRequest $request)
    {
        $user = Auth::user();
        $now = Carbon::now();
        $dateFrom = Carbon::createFromDate($request->post('date_from'));
        $dateTo = Carbon::createFromDate($request->post('date_to'));

        $savedTrips = Trip::where('user_id', $user->id)
            ->where('date_to', '>=', $dateFrom)
            ->get();

        $newTrip = new Trip([
            'id' => Str::uuid(),
            'title' => $request->post('title'),
            'description' => $request->post('description'),
            'origin' => $request->post('origin'),
            'destination' => $request->post('destination'),
            'date_from' => $dateFrom->format('Y-m-d H:i:s'),
            'date_to' => $dateTo->format('Y-m-d H:i:s'),
            'user_id' => $user->id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $savedTrips->each(function ($trip) use ($newTrip) {
            if ($trip->interfere($newTrip)) {
                throw new ErrorException("Trip \"{$newTrip->title}\" schedule interfering with Trip \"{$trip->title}\"", 400);
            }
        });

        $newTrip->save();

        return $this->responseSuccess('Success', [
            'trip' => $newTrip
        ]);
    }

    public function update(UpdateTripRequest $request, string $id)
    {
        $user = Auth::user();
        $now = Carbon::now();
        $dateFrom = Carbon::createFromDate($request->post('date_from'));
        $dateTo = Carbon::createFromDate($request->post('date_to'));

        $savedTrip = Trip::where('id', $id)->first();

        if (!$savedTrip) {
            throw new NotFoundException("Trip Not Found");
        }

        $savedTrips = Trip::where('user_id', $user->id)
            ->where('date_to', '>=', $dateFrom)
            ->where('id', '!=', $id)
            ->get();

        $savedTrip->fill([
            'title' => $request->post('title'),
            'description' => $request->post('description'),
            'origin' => $request->post('origin'),
            'destination' => $request->post('destination'),
            'date_from' => $dateFrom->format('Y-m-d H:i:s'),
            'date_to' => $dateTo->format('Y-m-d H:i:s'),
            'user_id' => $user->id,
            'updated_at' => $now,
        ]);

        $savedTrips->each(function ($trip) use ($savedTrip) {
            if ($trip->interfere($savedTrip)) {
                throw new ErrorException("Trip \"{$savedTrip->title}\" schedule interfering with Trip \"{$trip->title}\"", 400);
            }
        });

        $savedTrip->save();

        return $this->responseSuccess('Success', [
            'trip' => $savedTrip
        ]);
    }

    public function delete(string $id)
    {
        $savedTrip = Trip::where('id', $id)->first();

        if (!$savedTrip) {
            throw new NotFoundException("Trip Not Found");
        }

        $savedTrip->delete();

        return $this->responseSuccess('Success', [
            'trip' => $savedTrip
        ]);
    }

    public function show(string $id)
    {
        $savedTrip = Trip::where('id', $id)->first();

        if (!$savedTrip) {
            throw new NotFoundException("Trip Not Found");
        }

        $savedPlans = Plan::whereHasMorph('plannable', '*', function (Builder $query) use ($savedTrip) {
            $query->where('trip_id', $savedTrip->id);
        })->orderBy('start_date')
            ->orderBy('start_time')
            ->get();

        $flightPlannablePlanIds = $savedPlans->filter(function ($savedPlan) {
            return $savedPlan->plannable_type == FlightPlan::class;
        })->pluck('plannable_id')->all();

        $activityPlannablePlanIds = $savedPlans->filter(function ($savedPlan) {
            return $savedPlan->plannable_type == ActivityPlan::class;
        })->pluck('plannable_id')->all();

        $lodgingPlannablePlanIds = $savedPlans->filter(function ($savedPlan) {
            return $savedPlan->plannable_type == LodgingPlan::class;
        })->pluck('plannable_id')->all();

        $transportPlannablePlanIds = $savedPlans->filter(function ($savedPlan) {
            return $savedPlan->plannable_type == TransportPlan::class;
        })->pluck('plannable_id')->all();

        $flightPlans = FlightPlan::whereIn('id', $flightPlannablePlanIds)->get();
        $activityPlans = ActivityPlan::whereIn('id', $activityPlannablePlanIds)->get();
        $lodgingPlans = LodgingPlan::whereIn('id', $lodgingPlannablePlanIds)->get();
        $tranportPlans = TransportPlan::whereIn('id', $transportPlannablePlanIds)->get();

        $savedPlans = $savedPlans->transform(function ($savedPlan) use ($flightPlans, $activityPlans, $lodgingPlans, $tranportPlans) {
            switch ($savedPlan->plannable_type) {
                case FlightPlan::class:
                    $savedPlan->plannable_object = $flightPlans->where('id', $savedPlan->plannable_id)->first();
                    break;
                case ActivityPlan::class:
                    $savedPlan->plannable_object = $activityPlans->where('id', $savedPlan->plannable_id)->first();
                    break;
                case LodgingPlan::class:
                    $savedPlan->plannable_object = $lodgingPlans->where('id', $savedPlan->plannable_id)->first();
                    break;
                case TransportPlan::class:
                    $savedPlan->plannable_object = $tranportPlans->where('id', $savedPlan->plannable_id)->first();
                    break;
                default:
                    $savedPlan->plannable_object = null;
            }

            return $savedPlan;
        })
            ->groupBy('start_date')
            ->all();
        // ->mapToGroups(function ($savedPlan) {
        //     return [$savedPlan->start_date => $savedPlan];
        // })
        // ->all();

        return $this->responseSuccess('Success', [
            'trip' => $savedTrip,
            'plans' => $savedPlans
        ]);
    }

    public function storePlan(CreateTripPlanRequest $request, string $id)
    {
        $planType = $request->input('plan_type');

        switch ($planType) {
            case PlanEnum::FlightPlan:
                return $this->storeTripFlightPlan($request, $id);
            case PlanEnum::ActivityPlan:
                return $this->storeTripActivityPlan($request, $id);
            case PlanEnum::LodgingPlan:
                return $this->storeTripLodgingPlan($request, $id);
            case PlanEnum::TransportPlan:
                return $this->storeTripTransportPlan($request, $id);
            default:
                throw new BadRequestException("Store plan {$planType} doesn't exists");
        }
    }

    private function storeTripFlightPlan(CreateTripPlanRequest $request, string $id)
    {
        $departureAirport = $request->input('departure_airport');
        $arrivalAirport = $request->input('arrival_airport');
        $departureDate = Carbon::createFromDate($request->input('departure_date'));
        $arrivalDate = Carbon::createFromDate($request->input('arrival_date'));

        $savedTrip = Trip::where('id', $id)->first();

        if (!$savedTrip) {
            throw new NotFoundException("Trip Not Found");
        }

        $isDepartureDateBetweenTripSchedule = $departureDate->betweenIncluded($savedTrip->date_from, $savedTrip->date_to);
        $isArrivalDateBetweenTripSchedule = $arrivalDate->betweenIncluded($savedTrip->date_from, $savedTrip->date_to);

        if (!$isDepartureDateBetweenTripSchedule || !$isArrivalDateBetweenTripSchedule) {
            throw new BadRequestException("Flight schedule must be between Trip schedule");
        }

        $flightPlan = FlightPlan::create([
            'id' => Str::uuid(),
            'departure_airport' => $departureAirport,
            'arrival_airport' => $arrivalAirport,
            'departure_date' => $departureDate->format('Y-m-d H:i:s'),
            'arrival_date' => $arrivalDate->format('Y-m-d H:i:s'),
        ]);

        $plan = Plan::create([
            'id' => Str::uuid(),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'plannable_type' => FlightPlan::class,
            'plannable_id' => $flightPlan->id,
            'start_date' => $departureDate->format('Y-m-d'),
            'end_date' => $departureDate->format('Y-m-d'),
            'start_time' => $departureDate->format('H:i:s'),
            'end_time' => $departureDate->format('H:i:s'),
            'trip_id' => $savedTrip->id
        ]);

        return $this->responseSuccess('Success', [
            'plan' => array_merge($plan->toArray(), [
                'plannable_object' => $flightPlan
            ]),
        ]);
    }

    private function storeTripActivityPlan(CreateTripPlanRequest $request, string $id)
    {
        $dateFrom = $request->input('activity_date_from', null);
        $dateTo = $request->input('activity_date_to', null);
        $hasDateFrom = $dateFrom != null;
        $hasDateTo = $dateTo != null;

        $savedTrip = Trip::where('id', $id)->first();

        if (!$savedTrip) {
            throw new NotFoundException("Trip Not Found");
        }

        $isDateFromBetweenTripSchedule = !$hasDateFrom || Carbon::createFromDate($dateFrom)->betweenIncluded($savedTrip->date_from, $savedTrip->date_to);
        $isDateToBetweenTripSchedule = !$hasDateTo || Carbon::createFromDate($dateTo)->betweenIncluded($savedTrip->date_from, $savedTrip->date_to);

        if (!$isDateFromBetweenTripSchedule || !$isDateToBetweenTripSchedule) {
            throw new BadRequestException("Activity schedule must be between Trip schedule");
        }

        $activityPlan = ActivityPlan::create([
            'id' => Str::uuid(),
            'location_lat' => $request->input('location_lat'),
            'location_lng' => $request->input('location_lng'),
            'location_name' => $request->input('location_name'),
            'location_address' => $request->input('location_address')
        ]);

        $plan = Plan::create([
            'id' => Str::uuid(),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'plannable_type' => ActivityPlan::class,
            'plannable_id' => $activityPlan->id,
            'start_date' => $hasDateFrom ? Carbon::createFromDate($dateFrom)->format('Y-m-d') : null,
            'end_date' => $hasDateTo ? Carbon::createFromDate($dateTo)->format('Y-m-d') : null,
            'start_time' => $hasDateFrom ? Carbon::createFromDate($dateFrom)->format('H:i:s') : null,
            'end_time' => $hasDateTo ? Carbon::createFromDate($dateTo)->format('H:i:s') : null,
            'trip_id' => $savedTrip->id
        ]);

        return $this->responseSuccess('Success', [
            'plan' => array_merge($plan->toArray(), [
                'plannable_object' => $activityPlan
            ]),
        ]);
    }

    private function storeTripLodgingPlan(CreateTripPlanRequest $request, string $id)
    {
        $checkInDate = Carbon::createFromDate($request->input('check_in_date'));
        $checkOutDate = Carbon::createFromDate($request->input('check_out_date'));

        $savedTrip = Trip::where('id', $id)->first();

        if (!$savedTrip) {
            throw new NotFoundException("Trip Not Found");
        }

        $isCheckInDateBetweenTripSchedule = $checkInDate->betweenIncluded($savedTrip->date_from, $savedTrip->date_to);
        $isCheckOutDateBetweenTripSchedule = $checkOutDate->betweenIncluded($savedTrip->date_from, $savedTrip->date_to);

        if (!$isCheckInDateBetweenTripSchedule || !$isCheckOutDateBetweenTripSchedule) {
            throw new BadRequestException("Lodging schedule must be between Trip schedule");
        }

        $lodgingPlan = LodgingPlan::create([
            'id' => Str::uuid(),
            'location_lat' => $request->input('location_lat'),
            'location_lng' => $request->input('location_lng'),
            'location_name' => $request->input('location_name'),
            'location_address' => $request->input('location_address'),
            'check_in_date' => $checkInDate->format('Y-m-d H:i:s'),
            'check_out_date' => $checkOutDate->format('Y-m-d H:i:s')
        ]);

        $plan = Plan::create([
            'id' => Str::uuid(),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'plannable_type' => FlightPlan::class,
            'plannable_id' => $lodgingPlan->id,
            'start_date' => $checkInDate->format('Y-m-d'),
            'end_date' => $checkOutDate->format('Y-m-d'),
            'start_time' => $checkInDate->format('H:i:s'),
            'end_time' => $checkOutDate->format('H:i:s'),
            'trip_id' => $savedTrip->id
        ]);

        return $this->responseSuccess('Success', [
            'plan' => array_merge($plan->toArray(), [
                'plannable_object' => $lodgingPlan
            ]),
        ]);
    }

    private function storeTripTransportPlan(CreateTripPlanRequest $request, string $id)
    {
        $transportDate = Carbon::createFromDate($request->input('transport_date'));
        // Maybe date end is calculated from predicted time taken to get from lat,lng A to lat,lng B by transportation,
        // But as of now it is hardcoded
        $transportDateEnd = $transportDate->clone()->addMinutes(30);

        $savedTrip = Trip::where('id', $id)->first();

        if (!$savedTrip) {
            throw new NotFoundException("Trip Not Found");
        }

        $isTransportDateBetweenTripSchedule = $transportDate->betweenIncluded($savedTrip->date_from, $savedTrip->date_to);

        if (!$isTransportDateBetweenTripSchedule) {
            throw new BadRequestException('Transport date must be between Trip schedule');
        }

        $transportPlan = TransportPlan::create([
            'id' => Str::uuid(),
            'lat_from' => $request->input('lat_from'),
            'lng_from' => $request->input('lng_from'),
            'lat_to' => $request->input('lat_to'),
            'lng_to' => $request->input('lng_to'),
            'address_from' => $request->input('address_from'),
            'address_to' => $request->input('address_to'),
            'transportation' => $request->input('transportation')
        ]);

        $plan = Plan::create([
            'id' => Str::uuid(),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'plannable_type' => FlightPlan::class,
            'plannable_id' => $transportPlan->id,
            'start_date' => $transportDate->format('Y-m-d'),
            'end_date' => $transportDateEnd->format('Y-m-d'),
            'start_time' => $transportDate->format('H:i:s'),
            'end_time' => $transportDateEnd->format('H:i:s'),
            'trip_id' => $savedTrip->id
        ]);

        return $this->responseSuccess('Success', [
            'plan' => array_merge($plan->toArray(), [
                'plannable_object' => $transportPlan
            ]),
        ]);
    }
}

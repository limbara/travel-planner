<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Api\ErrorException;
use App\Http\Requests\Api\CreateTripRequest;
use App\Models\Trip;
use Carbon\Carbon;
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
}

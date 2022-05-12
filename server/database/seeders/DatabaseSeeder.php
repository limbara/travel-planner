<?php

namespace Database\Seeders;

use App\Models\ActivityPlan;
use App\Models\FlightPlan;
use App\Models\LodgingPlan;
use App\Models\Plan;
use App\Models\TransportPlan;
use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'nico',
                'email' => 'nico@example.com'
            ],
            [
                'name' => 'erwin',
                'email' => 'erwin@example.com'
            ]
        ];

        foreach ($users as $user) {
            $savedUser = User::factory()->state($user)->create();

            $savedTrip = Trip::factory()->state([
                'user_id' => $savedUser->id
            ])->create();

            $tripDateFrom = Carbon::createFromDate($savedTrip->date_from);
            $tripDateTo = Carbon::createFromDate($savedTrip->date_to);

            // Flight Plan to destination
            $toDestinationDepartureDate = $tripDateFrom->clone()->addHour()->startOfHour();
            $toDestinationArrivalDate = $toDestinationDepartureDate->clone()->addHours(2)->startOfHour();

            Plan::factory()->setSchedule(
                $toDestinationDepartureDate,
                $toDestinationArrivalDate,
                $toDestinationDepartureDate,
                $toDestinationArrivalDate
            )->state([
                'title' => 'Plane Flight to destination',
                'trip_id' => $savedTrip->id
            ])->for(
                FlightPlan::factory()
                    ->setSchedule($toDestinationDepartureDate, $toDestinationArrivalDate),
                'plannable'
            )->create();

            // Transport to lodging by Car
            $lodgingName = 'JW Marriot Hotel';
            $lodgingAddress = "{$lodgingName} @ 30 Beach Road, Nicoll Hwy";
            $transportDepartureDate = $toDestinationArrivalDate->clone()->addHour();
            $transportArrivalDate = $transportDepartureDate->clone()->addMinutes(20);

            Plan::factory()->setSchedule(
                $transportDepartureDate,
                $transportArrivalDate,
                $transportDepartureDate,
                $transportArrivalDate
            )->state([
                'title' => "Transport to {$lodgingName} by Car",
                'trip_id' => $savedTrip->id
            ])->for(
                TransportPlan::factory()
                    ->state([
                        'address_to' => $lodgingAddress,
                        'transportation' => 'Car'
                    ]),
                'plannable'
            )->create();

            // Lodging Plan check in
            $checkInDate = $transportArrivalDate->clone();
            $checkOutDate = $tripDateTo->clone()->startOfDay()->addHours(12);

            Plan::factory()->setSchedule(
                $checkInDate,
                $checkInDate
            )->state([
                'title' => "Check in to ${lodgingName}",
                'trip_id' => $savedTrip->id
            ])->for(
                LodgingPlan::factory()
                    ->state([
                        'location_name' => $lodgingName,
                        'location_address' => $lodgingAddress
                    ])
                    ->setSchedule($checkInDate, $checkOutDate),
                'plannable'
            )->create();

            // Transport Plan to Meetups
            $meetupLocation = "Golang Developers Space";
            $transportDepartureDate = $tripDateFrom->clone()->addDay(1)->startOfDay()->addHours(13);
            $transportArrivalDate = $transportDepartureDate->clone()->addMinutes(30);

            Plan::factory()->setSchedule(
                $transportDepartureDate,
                $transportArrivalDate,
                $transportDepartureDate,
                $transportArrivalDate
            )->state([
                'title' => "Transport to {$meetupLocation}",
                'trip_id' => $savedTrip->id
            ])->for(
                TransportPlan::factory()
                    ->state([
                        'transportation' => 'mrt'
                    ]),
                'plannable'
            )->create();

            // Activity Plan Meetups
            $meetupStartDate = $transportArrivalDate->clone()->addMinutes(30);
            $meetupEndDate = $meetupStartDate->clone()->addHour();

            Plan::factory()->setSchedule(
                $meetupStartDate,
                $meetupEndDate,
                $meetupStartDate,
                $meetupEndDate
            )->state([
                'title' => "Meetups {$meetupLocation}",
                'description' => 'Meetups about Golang current states',
                'trip_id' => $savedTrip->id
            ])->for(
                ActivityPlan::factory()
                    ->state([
                        'location_name' => $meetupLocation,
                    ]),
                'plannable'
            )->create();

            // Lodging Plan Check out
            Plan::factory()->setSchedule(
                $checkOutDate,
                $checkOutDate
            )->state([
                'title' => "Check out from ${lodgingName}",
                'trip_id' => $savedTrip->id
            ])->for(
                LodgingPlan::factory()
                    ->state([
                        'location_name' => $lodgingName,
                        'location_address' => $lodgingAddress
                    ])
                    ->setSchedule($checkInDate, $checkOutDate),
                'plannable'
            )->create();

            // Transport to airport by Car
            $transportDepartureDate = $checkOutDate->clone()->addMinutes(15);
            $transportArrivalDate = $transportDepartureDate->clone()->addMinutes(20);

            Plan::factory()->setSchedule(
                $transportDepartureDate,
                $transportArrivalDate,
                $transportDepartureDate,
                $transportArrivalDate
            )->state([
                'title' => "Transport to Airport by Car",
                'trip_id' => $savedTrip->id
            ])->for(
                TransportPlan::factory()
                    ->state([
                        'transportation' => 'Car'
                    ]),
                'plannable'
            )->create();

            // Flight Plan back to origin
            $toOriginDepartureDate = $tripDateTo->clone()->subHours(2)->startOfHour();
            $toOriginArrivalDate = $tripDateTo->clone()->startOfHour();

            Plan::factory()->setSchedule(
                $toOriginDepartureDate,
                $toOriginArrivalDate,
                $toOriginDepartureDate,
                $toOriginArrivalDate
            )->state([
                'title' => 'Plane Flight back to origin',
                'trip_id' => $savedTrip->id
            ])->for(
                FlightPlan::factory()
                    ->setSchedule($toOriginDepartureDate, $toOriginArrivalDate),
                'plannable'
            )->create();
        }
    }
}

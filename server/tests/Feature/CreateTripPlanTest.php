<?php

namespace Tests\Feature;

use App\Models\ActivityPlan;
use App\Models\FlightPlan;
use App\Models\LodgingPlan;
use App\Models\TransportPlan;
use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;

class CreateTripPlanTest extends BaseTest
{
    public function testCreateTripPlanWithoutToken()
    {
        $user = User::factory()->create();

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $planData = [
            "title" => "Transport to Changi Airport",
            "description" => "Transport to Changi Airport",
            "plan_type" => "TRANSPORT_PLAN",
            "lat_from" => 1.4315594978779551,
            "lng_from" => 103.83497099171741,
            "lat_to" => 1.3491750070012891,
            "lng_to" => 103.98554118791908,
            "address_from" => "Yishun Singapore",
            "address_to" => "Airport Blvd., Singapore",
            "transportation" => "car",
            "transport_date" => Carbon::createFromDate($trip->date_from)->addHour()->format('Y-m-d H:i:s')
        ];

        $this->json("POST", "api/trips/{$trip->id}/plans", $planData, ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                'error_code' => 401,
                'error_message' => 'Unauthenticated.'
            ]);
    }

    public function testCreateTripPlanTransportRequiredBody()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $planData = [
            "title" => "Transport to Changi Airport",
            "description" => "Transport to Changi Airport",
            "plan_type" => "TRANSPORT_PLAN"
        ];

        $this->json("POST", "api/trips/{$trip->id}/plans", $planData, ['Accept' => 'application/json'])
            ->assertStatus(400)
            ->assertJson([
                "error_code" => 400,
                "error_message" => "The lat from field is required. (and 7 more errors)",
                "errors" => [
                    "lat_from" => [
                        "The lat from field is required."
                    ],
                    "lng_from" => [
                        "The lng from field is required."
                    ],
                    "lat_to" => [
                        "The lat to field is required."
                    ],
                    "lng_to" => [
                        "The lng to field is required."
                    ],
                    "address_from" => [
                        "The address from field is required."
                    ],
                    "address_to" => [
                        "The address to field is required."
                    ],
                    "transportation" => [
                        "The transportation field is required."
                    ],
                    "transport_date" => [
                        "The transport date field is required."
                    ]
                ]
            ]);
    }

    public function testCreateTripPlanTransportNotOnSchedule()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $planData = [
            "title" => "Transport to Changi Airport",
            "description" => "Transport to Changi Airport",
            "plan_type" => "TRANSPORT_PLAN",
            "lat_from" => 1.4315594978779551,
            "lng_from" => 103.83497099171741,
            "lat_to" => 1.3491750070012891,
            "lng_to" => 103.98554118791908,
            "address_from" => "Yishun Singapore",
            "address_to" => "Airport Blvd., Singapore",
            "transportation" => "car",
            "transport_date" => Carbon::createFromDate($trip->date_from)->addDays(100)->addHour()->format('Y-m-d H:i:s')
        ];

        $this->json("POST", "api/trips/{$trip->id}/plans", $planData, ['Accept' => 'application/json'])
            ->assertStatus(400)
            ->assertJson([
                "error_code" => 400,
                "error_message" => "Transport date must be between Trip schedule",
            ]);
    }

    public function testCreateTripPlanTransportSuccess()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $planData = [
            "title" => "Transport to Changi Airport",
            "description" => "Transport to Changi Airport",
            "plan_type" => "TRANSPORT_PLAN",
            "lat_from" => 1.4315594978779551,
            "lng_from" => 103.83497099171741,
            "lat_to" => 1.3491750070012891,
            "lng_to" => 103.98554118791908,
            "address_from" => "Yishun Singapore",
            "address_to" => "Airport Blvd., Singapore",
            "transportation" => "car",
            "transport_date" => Carbon::createFromDate($trip->date_from)->addHour()->format('Y-m-d H:i:s')
        ];

        $this->json("POST", "api/trips/{$trip->id}/plans", $planData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonFragment([
                'plannable_type' => TransportPlan::class
            ])
            ->assertJsonStructure([
                "data" => [
                    "plan" => [
                        "id",
                        "title",
                        "description",
                        "plannable_type",
                        "plannable_id",
                        "start_date",
                        "end_date",
                        "start_time",
                        "end_time",
                        "trip_id",
                        "updated_at",
                        "created_at",
                        "plannable_object" => [
                            "id",
                            "lat_from",
                            "lng_from",
                            "lat_to",
                            "lng_to",
                            "address_from",
                            "address_to",
                            "transportation",
                            "updated_at",
                            "created_at",
                        ]
                    ]
                ]
            ]);
    }

    public function testCreateTripPlanFlightRequiredBody()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $flightData = [
            "title"  => "Flight to Malaysia",
            "description" => "Flight to Malaysia",
            "plan_type" => "FLIGHT_PLAN",
        ];

        $this->json("POST", "api/trips/{$trip->id}/plans", $flightData, ['Accept' => 'application/json'])
            ->assertStatus(400)
            ->assertJson([
                "error_code" => 400,
                "error_message" => "The departure airport field is required. (and 3 more errors)",
                "errors" => [
                    "departure_airport" => [
                        "The departure airport field is required."
                    ],
                    "arrival_airport" => [
                        "The arrival airport field is required."
                    ],
                    "departure_date" => [
                        "The departure date field is required."
                    ],
                    "arrival_date" => [
                        "The arrival date field is required."
                    ]
                ]
            ]);
    }

    public function testCreateTripPlanFlightNotOnTripSchedule()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $flightData = [
            "title"  => "Flight to Malaysia",
            "description" => "Flight to Malaysia",
            "plan_type" => "FLIGHT_PLAN",
            "departure_airport" => "Changi Airport",
            "arrival_airport" => "Kuala Lumpur International Airport",
            "departure_date" => Carbon::createFromDate($trip->date_from)->addDays(100)->format('Y-m-d H:i:s'),
            "arrival_date" => Carbon::createFromDate($trip->date_from)->addDays(100)->addHour(1)->format('Y-m-d H:i:s'),
        ];

        $this->json("POST", "api/trips/{$trip->id}/plans", $flightData, ['Accept' => 'application/json'])
            ->assertStatus(400)
            ->assertJson([
                "error_code" => 400,
                "error_message" => "Flight schedule must be between Trip schedule",
            ]);
    }

    public function testCreateTripPlanFlightSuccess()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $flightData = [
            "title"  => "Flight to Malaysia",
            "description" => "Flight to Malaysia",
            "plan_type" => "FLIGHT_PLAN",
            "departure_airport" => "Changi Airport",
            "arrival_airport" => "Kuala Lumpur International Airport",
            "departure_date" => Carbon::createFromDate($trip->date_from)->addHours(3)->format('Y-m-d H:i:s'),
            "arrival_date" => Carbon::createFromDate($trip->date_from)->addHours(4)->format('Y-m-d H:i:s'),
        ];

        $this->json("POST", "api/trips/{$trip->id}/plans", $flightData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonFragment([
                'plannable_type' => FlightPlan::class
            ])
            ->assertJsonStructure([
                "data" => [
                    "plan" => [
                        "id",
                        "title",
                        "description",
                        "plannable_type",
                        "plannable_id",
                        "start_date",
                        "end_date",
                        "start_time",
                        "end_time",
                        "trip_id",
                        "updated_at",
                        "created_at",
                        "plannable_object" => [
                            "id",
                            "departure_airport",
                            "arrival_airport",
                            "departure_date",
                            "arrival_date",
                            "updated_at",
                            "created_at",
                        ]
                    ]
                ]
            ]);
    }

    public function testCreateTripPlanActivityRequiredBody()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $activityData = [
            "title"  => "Gambling at Casino De Genting",
            "description" => "Gambling at Casino De Genting",
            "plan_type" => "ACTIVITY_PLAN"
        ];

        $this->json('POST', "api/trips/{$trip->id}/plans", $activityData, ['Accept' => 'application/json'])
            ->assertStatus(400)
            ->assertJson([
                "error_code" => 400,
                "error_message" => "The location lat field is required. (and 5 more errors)",
                "errors" => [
                    "location_lat" => [
                        "The location lat field is required."
                    ],
                    "location_lng" => [
                        "The location lng field is required."
                    ],
                    "location_name" => [
                        "The location name field is required."
                    ],
                    "location_address" => [
                        "The location address field is required."
                    ],
                    "activity_date_from" => [
                        "The activity date from field is required."
                    ],
                    "activity_date_to" => [
                        "The activity date to field is required."
                    ]
                ]
            ]);
    }

    public function testCreateTripPlanActivityNotOnTripSchedule()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $activityData = [
            "title"  => "Gambling at Casino De Genting",
            "description" => "Gambling at Casino De Genting",
            "plan_type" => "ACTIVITY_PLAN",
            "location_lat" => 3.4198142610865734,
            "location_lng" => 101.79890815066175,
            "location_name" => "Casino De Genting",
            "location_address" => "Casino De Genting, Genting Grand Genting Highlands Resort, 69000 Genting Highlands, Pahang, Malaysia",
            "activity_date_from" => Carbon::createFromDate($trip->date_from)->addDays(100)->addHours(1)->format('Y-m-d H:i:s'),
            "activity_date_to" => Carbon::createFromDate($trip->date_from)->addDays(100)->addHours(2)->format('Y-m-d H:i:s')
        ];

        $this->json('POST', "api/trips/{$trip->id}/plans", $activityData, ['Accept' => 'application/json'])
            ->assertStatus(400)
            ->assertJson([
                "error_code" => 400,
                "error_message" => "Activity schedule must be between Trip schedule",
            ]);
    }

    public function testCreateTripPlanActivitySuccess()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $activityData = [
            "title"  => "Gambling at Casino De Genting",
            "description" => "Gambling at Casino De Genting",
            "plan_type" => "ACTIVITY_PLAN",
            "location_lat" => 3.4198142610865734,
            "location_lng" => 101.79890815066175,
            "location_name" => "Casino De Genting",
            "location_address" => "Casino De Genting, Genting Grand Genting Highlands Resort, 69000 Genting Highlands, Pahang, Malaysia",
            "activity_date_from" => Carbon::createFromDate($trip->date_from)->addHours(1)->format('Y-m-d H:i:s'),
            "activity_date_to" => Carbon::createFromDate($trip->date_from)->addHours(2)->format('Y-m-d H:i:s')
        ];

        $this->json('POST', "api/trips/{$trip->id}/plans", $activityData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonFragment([
                'plannable_type' => ActivityPlan::class
            ])
            ->assertJsonStructure([
                "data" => [
                    "plan" => [
                        "id",
                        "title",
                        "description",
                        "plannable_type",
                        "plannable_id",
                        "start_date",
                        "end_date",
                        "start_time",
                        "end_time",
                        "trip_id",
                        "updated_at",
                        "created_at",
                        "plannable_object" => [
                            "id",
                            "location_lat",
                            "location_lng",
                            "location_name",
                            "location_address",
                            "updated_at",
                            "created_at",
                        ]
                    ]
                ]
            ]);
    }

    public function testCreateTripPlanLodgingRequiredBody()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $lodgingData = [
            "title"  => "Stay in Grand Ion Delemen Hotel",
            "description" => "Stay in Grand Ion Delemen Hotel",
            "plan_type" => "LODGING_PLAN"
        ];

        $this->json('POST', "api/trips/{$trip->id}/plans", $lodgingData, ['Accept' => 'application/json'])
            ->assertStatus(400)
            ->assertJson([
                "error_code" => 400,
                "error_message" => "The location lat field is required. (and 5 more errors)",
                "errors" => [
                    "location_lat" => [
                        "The location lat field is required."
                    ],
                    "location_lng" => [
                        "The location lng field is required."
                    ],
                    "location_name" => [
                        "The location name field is required."
                    ],
                    "location_address" => [
                        "The location address field is required."
                    ],
                    "check_in_date" => [
                        "The check in date field is required."
                    ],
                    "check_out_date" => [
                        "The check out date field is required."
                    ]
                ]
            ]);
    }

    public function testCreateTripPlanLodgingNotOnTripSchedule()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $lodgingData = [
            "title"  => "Stay in Grand Ion Delemen Hotel",
            "description" => "Stay in Grand Ion Delemen Hotel",
            "plan_type" => "LODGING_PLAN",
            "location_lat" => 3.4330317988229626,
            "location_lng" => 101.7898954341072,
            "location_name" => "Grand Ion Delemen Hotel",
            "location_address" => "Grand Ion Delemen Hotel, Jalan Ion Delemen, 6900 Genting Highlands, Pahang, Malaysia",
            "check_in_date" => Carbon::createFromDate($trip->date_from)->addDays(100)->addHours(1)->format('Y-m-d H:i:s'),
            "check_out_date" => Carbon::createFromDate($trip->date_from)->addDays(100)->addHours(2)->format('Y-m-d H:i:s'),
        ];

        $this->json('POST', "api/trips/{$trip->id}/plans", $lodgingData, ['Accept' => 'application/json'])
            ->assertStatus(400)
            ->assertJson([
                "error_code" => 400,
                "error_message" => "Lodging schedule must be between Trip schedule",
            ]);
    }

    public function testCreateTripPlanLodgingSuccess()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $lodgingData = [
            "title"  => "Stay in Grand Ion Delemen Hotel",
            "description" => "Stay in Grand Ion Delemen Hotel",
            "plan_type" => "LODGING_PLAN",
            "location_lat" => 3.4330317988229626,
            "location_lng" => 101.7898954341072,
            "location_name" => "Grand Ion Delemen Hotel",
            "location_address" => "Grand Ion Delemen Hotel, Jalan Ion Delemen, 6900 Genting Highlands, Pahang, Malaysia",
            "check_in_date" => Carbon::createFromDate($trip->date_from)->addHours(1)->format('Y-m-d H:i:s'),
            "check_out_date" => Carbon::createFromDate($trip->date_from)->addHours(2)->format('Y-m-d H:i:s'),
        ];

        $this->json('POST', "api/trips/{$trip->id}/plans", $lodgingData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonFragment([
                'plannable_type' => LodgingPlan::class
            ])
            ->assertJsonStructure([
                "data" => [
                    "plan" => [
                        "id",
                        "title",
                        "description",
                        "plannable_type",
                        "plannable_id",
                        "start_date",
                        "end_date",
                        "start_time",
                        "end_time",
                        "trip_id",
                        "updated_at",
                        "created_at",
                        "plannable_object" => [
                            "id",
                            "location_lat",
                            "location_lng",
                            "location_name",
                            "location_address",
                            "check_in_date",
                            "check_out_date",
                            "updated_at",
                            "created_at"
                        ]
                    ]
                ]
            ]);
    }
}

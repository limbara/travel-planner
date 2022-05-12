<?php

namespace App\Http\Requests\Api;

use App\Enums\PlanEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTripPlanRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|string|alpha_num_space|max:255',
            'description' => 'string',
            'plan_type' => 'required|plan_enum',
            'departure_airport' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::FlightPlan),
                'string',
            ],
            'arrival_airport' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::FlightPlan),
                'string',
            ],
            'departure_date' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::FlightPlan),
                'date',
                'date_format:Y-m-d H:i:s',
                'after:now'
            ],
            'arrival_date' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::FlightPlan),
                'date',
                'date_format:Y-m-d H:i:s',
                'after:departure_date'
            ],
            'location_lat' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::ActivityPlan || $this->input('plan_type') == PlanEnum::LodgingPlan),
                'digits_between:-90,90'
            ],
            'location_lng' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::ActivityPlan || $this->input('plan_type') == PlanEnum::LodgingPlan),
                'digits_between:-180,180',
            ],
            'location_name' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::ActivityPlan || $this->input('plan_type') == PlanEnum::LodgingPlan),
                'string'
            ],
            'location_address' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::ActivityPlan || $this->input('plan_type') == PlanEnum::LodgingPlan),
                'string'
            ],
            'activity_date_from' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::ActivityPlan),
                'nullable',
                'date',
                'date_format:Y-m-d H:i:s',
                'after:now'
            ],
            'activity_date_to' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::ActivityPlan),
                'nullable',
                'date',
                'date_format:Y-m-d H:i:s',
                'after:activity_date_from'
            ],
            'check_in_date' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::LodgingPlan),
                'date',
                'date_format:Y-m-d H:i:s',
                'after:now'
            ],
            'check_out_date' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::LodgingPlan),
                'date',
                'date_format:Y-m-d H:i:s',
                'after:check_in_date'
            ],
            'lat_from' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::TransportPlan),
                'digits_between:-90,90'
            ],
            'lng_from' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::TransportPlan),
                'digits_between:-180,180',
            ],
            'lat_to' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::TransportPlan),
                'digits_between:-90,90'
            ],
            'lng_to' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::TransportPlan),
                'digits_between:-180,180',
            ],
            'address_from' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::TransportPlan),
                'string',
            ],
            'address_to' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::TransportPlan),
                'string',
            ],
            'transportation' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::TransportPlan),
                'string'
            ],
            'transport_date' => [
                Rule::requiredIf($this->input('plan_type') == PlanEnum::TransportPlan),
                'date',
                'date_format:Y-m-d H:i:s',
                'after:now'
            ]
        ];
    }
}

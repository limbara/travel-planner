<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
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
            'origin' => 'required|string|alpha_num_space|max:255',
            'destination' => 'required|string|alpha_num_space|max:255',
            'date_from' => 'required|date|after:now',
            'date_to' => 'required|date|after:date_from',
        ];
    }
}

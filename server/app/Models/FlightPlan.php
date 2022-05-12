<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightPlan extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'departure_airport',
        'arrival_airport',
        'departure_date',
        'arrival_date'
    ];

    public function plan()
    {
        return $this->morphOne(Plan::class, 'plannable');
    }
}

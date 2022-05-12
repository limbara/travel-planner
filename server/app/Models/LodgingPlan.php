<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LodgingPlan extends Model
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
        'location_lat',
        'location_lng',
        'location_name',
        'location_address',
        'check_in_date',
        'check_out_date'
    ];

    public function plan()
    {
        return $this->morphOne(Plan::class, 'plannable');
    }
}

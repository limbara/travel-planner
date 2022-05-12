<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportPlan extends Model
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
        'lat_from',
        'lng_from',
        'lat_to',
        'lng_to',
        'address_from',
        'address_to',
        'transportation'
    ];

    public function plan()
    {
        return $this->morphOne(Plan::class, 'plannable');
    }
}

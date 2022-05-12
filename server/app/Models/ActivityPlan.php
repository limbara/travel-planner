<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityPlan extends Model
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
    ];

    public function plan()
    {
        return $this->morphOne(Plan::class, 'plannable');
    }
}

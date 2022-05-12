<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
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
        'title',
        'description',
        'plannable_type',
        'plannable_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'trip_id'
    ];

    public function plannable()
    {
        return $this->morphTo();
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasFactory, SoftDeletes;

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
        'origin',
        'destination',
        'date_from',
        'date_to',
        'user_id'
    ];

    public function interfere(Trip $otherTrip): bool
    {
        $currentTripDateFrom = Carbon::createFromDate($this->date_from);
        $currentTripDateTo = Carbon::createFromDate($this->date_to);
        $otherTripDateFrom = Carbon::createFromDate($otherTrip->date_from);
        $otherTripDateTo = Carbon::createFromDate($otherTrip->date_to);

        $isCurrentInterfereOther = $currentTripDateFrom->betweenIncluded($otherTripDateFrom, $otherTripDateTo);
        $isOtherInterfereCurrent = $otherTripDateFrom->betweenIncluded($currentTripDateFrom, $currentTripDateTo);

        return $isCurrentInterfereOther || $isOtherInterfereCurrent;
    }

    public function user()
    {
        return $this->belongsTo(Trip::class);
    }
}

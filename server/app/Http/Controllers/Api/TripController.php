<?php

namespace App\Http\Controllers\Api;

use App\Models\Trip;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $trips = Trip::where('user_id', $user->id)
            ->orderBy('date_from', 'asc')
            ->get();

        return $this->responseSuccess('Success', [
            'trips' => $trips
        ]);
    }
}

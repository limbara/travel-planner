<?php

namespace App\Enums;

use ReflectionClass;

abstract class PlanEnum
{
    const FlightPlan = 'FLIGHT_PLAN';
    const ActivityPlan = 'ACTIVITY_PLAN';
    const TransportPlan = 'TRANSPORT_PLAN';
    const LodgingPlan = 'LODGING_PLAN';

    public static function keys()
    {
        $class = new ReflectionClass(self::class);
        $constants = $class->getConstants();

        return array_keys($constants);
    }

    public static function values()
    {
        $class = new ReflectionClass(self::class);
        $constants = $class->getConstants();

        return array_values($constants);
    }
}

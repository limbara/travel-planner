<?php

namespace App\Utils;

use App\Enums\PlanEnum;

class ValidationUtils
{
    public static function isAlphaNumericSpaceOnly($value)
    {
        return boolval(preg_match('/^[\pL\d\s]+$/u', $value));
    }

    public static function isPlanEnum($value)
    {
        return in_array($value, PlanEnum::values());
    }
}

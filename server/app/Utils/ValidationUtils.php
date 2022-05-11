<?php

namespace App\Utils;

class ValidationUtils
{
    public static function isAlphaNumericSpaceOnly($value)
    {
        return preg_match('/^[\pL\d\s]+$/u', $value);
    }
}

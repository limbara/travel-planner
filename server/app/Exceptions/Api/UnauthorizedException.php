<?php

namespace App\Exceptions\Api;

use Exception;

class UnauthorizedException extends Exception
{
    public function render()
    {
        return response()->json([
            'error_code' => $this->code ? $this->code : 401,
            'error_message' => $this->message ? $this->message : 'Unauthorized',
        ], 401);
    }
}

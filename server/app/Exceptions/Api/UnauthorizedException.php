<?php

namespace App\Exceptions\Api;

class UnauthorizedException extends CustomException
{
    public function render()
    {
        return response()->json([
            'error_code' => $this->code ? $this->code : 401,
            'error_message' => $this->message ? $this->message : 'Unauthorized',
        ], 401);
    }
}

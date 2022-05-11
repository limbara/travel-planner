<?php

namespace App\Exceptions\Api;

class InternalErrorException extends CustomException
{
    public function render()
    {
        return response()->json([
            'error_code' => $this->code ? $this->code : 500,
            'error_message' => $this->message ? $this->message : 'Internal Server Error',
        ], 500);
    }
}

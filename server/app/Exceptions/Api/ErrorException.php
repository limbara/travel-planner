<?php

namespace App\Exceptions\Api;

class ErrorException extends CustomException
{
    public function render()
    {
        return response()->json([
            'error_code' => $this->code ? $this->code : 500,
            'error_message' => $this->message ? $this->message : 'Error',
        ], 500);
    }
}

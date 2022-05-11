<?php

namespace App\Exceptions\Api;

use Exception;

class NotFoundException extends Exception
{
    public function render()
    {
        return response()->json([
            'error_code' => $this->code ? $this->code : 500,
            'error_message' => $this->message ? $this->message : 'Internal Server Error',
        ], 500);
    }
}

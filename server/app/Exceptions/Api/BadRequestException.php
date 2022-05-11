<?php

namespace App\Exceptions\Api;

class BadRequestException extends CustomException
{
    private $errors = [];

    public function setErrors(array $errors)
    {
        $this->errors = $errors;

        return $this;
    }

    public function render()
    {
        return response()->json([
            'error_code' => $this->code ? $this->code : 400,
            'error_message' => $this->message ? $this->message : 'Error. Something is missing.',
            'errors' => $this->errors
        ], 400);
    }
}

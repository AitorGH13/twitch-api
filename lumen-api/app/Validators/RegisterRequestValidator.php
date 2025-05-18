<?php // app/Validators/RegisterRequestValidator.php

namespace App\Validators;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailAddressException;
use Illuminate\Http\Request;

class RegisterRequestValidator
{
    public function validate(Request $request): string
    {
        $email = $request->input('email');

        if (empty($email)) {
            throw new EmptyEmailException();
        }

        $sanitized = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (! filter_var($sanitized, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailAddressException();
        }

        return $sanitized;
    }
}

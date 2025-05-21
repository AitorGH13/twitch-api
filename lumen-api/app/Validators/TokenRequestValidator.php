<?php

namespace App\Validators;

use Illuminate\Http\Request;
use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailAddressException;
use App\Exceptions\EmptyApiKeyException;

class TokenRequestValidator
{
    public function validate(Request $request): array
    {
        $email  = $request->input('email');
        $apiKey = $request->input('api_key');

        if (empty($email)) {
            throw new EmptyEmailException();
        }

        $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (! filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailAddressException();
        }

        if (empty($apiKey)) {
            throw new EmptyApiKeyException();
        }

        return [
            'email'   => $sanitizedEmail,
            'api_key' => $apiKey,
        ];
    }
}

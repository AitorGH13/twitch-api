<?php

namespace App\Validators;

use Illuminate\Http\Request;
use App\Exceptions\UnauthorizedException;

class StreamsRequestValidator
{
    /**
     * @return string $token
     * @throws UnauthorizedException
     */
    public function validate(Request $request): string
    {
        $token = $request->attributes->get('token');
        if (! $token) {
            throw new UnauthorizedException();
        }
        return $token;
    }
}

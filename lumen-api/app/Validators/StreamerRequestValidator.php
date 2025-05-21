<?php

namespace App\Validators;

use Illuminate\Http\Request;
use App\Exceptions\EmptyIdException;
use App\Exceptions\UnauthorizedException;

class StreamerRequestValidator
{
    /**
     * @return array{string,string}  [$userId, $token]
     * @throws EmptyIdException
     */
    public function validate(Request $request): array
    {
        $token = $request->attributes->get('token');
        if (! $token) {
            throw new UnauthorizedException();
        }

        $userId = $request->query('id', '');
        if ($userId === '' || ! ctype_digit($userId)) {
            throw new EmptyIdException();
        }

        return [$userId, $token];
    }
}

<?php

// app/Validators/EnrichedStreamsRequestValidator.php

namespace App\Validators;

use Illuminate\Http\Request;
use App\Exceptions\InvalidLimitException;
use App\Exceptions\UnauthorizedException;

class EnrichedStreamsRequestValidator
{
    /**
     * @return array{int,string}  [$limit, $token]
     * @throws InvalidLimitException
     */
    public function validate(Request $request): array
    {
        $token = $request->attributes->get('token');
        if (! $token) {
            throw new UnauthorizedException();
        }

        $limit = $request->query('limit');
        if ($limit === '' || ! ctype_digit($limit)) {
            throw new InvalidLimitException();
        }

        return [(int)$limit, $token];
    }
}

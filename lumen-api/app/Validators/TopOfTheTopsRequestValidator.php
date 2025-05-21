<?php

// app/Validators/TopOfTheTopsRequestValidator.php

namespace App\Validators;

use Illuminate\Http\Request;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\InvalidSinceException;

class TopOfTheTopsRequestValidator
{
    /**
     * @return array{string,int|null}  [$token, $since]
     * @throws InvalidSinceException
     */
    public function validate(Request $request): array
    {
        $token = $request->attributes->get('token');
        if (! $token) {
            throw new UnauthorizedException();
        }

        $queryParams = $request->query();
        foreach (array_keys($queryParams) as $key) {
            if ($key !== 'since') {
                throw new InvalidSinceException();
            }
        }

        $since = null;
        if (array_key_exists('since', $queryParams)) {
            $sinceParam = (string)$queryParams['since'];
            if (! ctype_digit($sinceParam)) {
                throw new InvalidSinceException();
            }
            $since = (int)$sinceParam;
        }

        return [$token, $since];
    }
}

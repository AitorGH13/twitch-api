<?php // app/Validators/TopOfTheTopsRequestValidator.php
namespace App\Validators;

use Illuminate\Http\Request;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\InvalidSinceException;
class TopOfTheTopsRequestValidator
{
    /**
     * @return array{string,int|null}  [$token, $since]
     * @throws UnauthorizedException
     * @throws \InvalidArgumentException
     */
    public function validate(Request $request): array
    {
        // 1) Validar cabecera Authorization: Bearer <token>
        $header = $request->header('Authorization', '');
        if (! str_starts_with($header, 'Bearer ')) {
            throw new UnauthorizedException();
        }
        $token = substr($header, 7);

        // 2) Solo se permite el parámetro 'since' en query params
        $queryParams = $request->query();
        foreach (array_keys($queryParams) as $key) {
            if ($key !== 'since') {
                throw new InvalidSinceException();
            }
        }

        // 3) Validar 'since' si está presente
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

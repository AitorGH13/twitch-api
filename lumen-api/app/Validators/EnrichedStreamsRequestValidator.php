<?php // app/Validators/EnrichedStreamsRequestValidator.php
namespace App\Validators;

use Illuminate\Http\Request;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\InvalidLimitException;

class EnrichedStreamsRequestValidator
{
    /**
     * @return array{int,string}  [$limit, $token]
     * @throws UnauthorizedException
     * @throws InvalidLimitException
     */
    public function validate(Request $request): array
    {
        // 1) Token siempre de header
        $header = $request->header('Authorization', '');
        if (! str_starts_with($header, 'Bearer ')) {
            throw new UnauthorizedException();
        }
        $token = substr($header, 7);

        // 2) Limit
        $limit = $request->query('limit');
        if ($limit === '' || !ctype_digit($limit)) {
            throw new InvalidLimitException();
        }

        return [(int)$limit, $token];
    }
}

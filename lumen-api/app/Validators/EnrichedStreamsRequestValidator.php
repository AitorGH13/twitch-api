<?php // app/Validators/EnrichedStreamsRequestValidator.php
namespace App\Validators;

use Illuminate\Http\Request;
use App\Exceptions\InvalidLimitException;

class EnrichedStreamsRequestValidator
{
    /**
     * @throws InvalidLimitException
     */
    public function validate(Request $request): array
    {
        $limit = $request->query('limit');
        if (! ctype_digit((string)$limit) || (int)$limit <= 0) {
            throw new InvalidLimitException();
        }
        $token = $request->query('token') ?? '';
        return [(int)$limit, $token];
    }
}

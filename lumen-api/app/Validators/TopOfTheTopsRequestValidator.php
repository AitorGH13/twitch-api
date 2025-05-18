<?php // app/Validators/TopOfTheTopsRequestValidator.php
namespace App\Validators;

use App\Exceptions\UnauthorizedException;
use Illuminate\Http\Request;

class TopOfTheTopsRequestValidator
{
    public function validate(Request $request): array
    {
        $token = $request->query('token') ?? '';
        if (empty($token)) {
            throw new UnauthorizedException();
        }

        $sinceParam = $request->query('since');
        $since = null;
        if ($sinceParam !== null) {
            if (!ctype_digit($sinceParam)) {
                throw new \InvalidArgumentException('Parameter since must be an integer.');
            }
            $since = (int)$sinceParam;
        }

        return [$token, $since];
    }
}

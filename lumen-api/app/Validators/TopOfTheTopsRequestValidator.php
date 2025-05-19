<?php // app/Validators/TopOfTheTopsRequestValidator.php
namespace App\Validators;

use Illuminate\Http\Request;
use App\Exceptions\UnauthorizedException;

class TopOfTheTopsRequestValidator
{
    /**
     * @return array{string,int|null}  [$token, $since]
     * @throws UnauthorizedException
     */
    public function validate(Request $request): array
    {
        $header = $request->header('Authorization', '');
        if (! str_starts_with($header, 'Bearer ')) {
            throw new UnauthorizedException();
        }
        $token = substr($header, 7);

        $sinceParam = $request->query('since');
        $since = null;
        if ($sinceParam !== null) {
            if (! ctype_digit($sinceParam)) {
                throw new \InvalidArgumentException('Parameter since must be an integer.');
            }
            $since = (int)$sinceParam;
        }

        return [$token, $since];
    }
}

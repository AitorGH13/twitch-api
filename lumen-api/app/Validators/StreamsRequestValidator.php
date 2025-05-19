<?php // app/Validators/StreamsRequestValidator.php
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
        $header = $request->header('Authorization', '');
        if (! str_starts_with($header, 'Bearer ')) {
            throw new UnauthorizedException();
        }
        return substr($header, 7);
    }
}

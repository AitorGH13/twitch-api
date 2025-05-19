<?php // app/Validators/StreamsRequestValidator.php
namespace App\Validators;

use Illuminate\Http\Request;
use App\Exceptions\UnauthorizedException;

class StreamsRequestValidator
{
    /**
     * Extrae el token y lanza Unauthorized si está vacío.
     *
     * @return string $token
     * @throws UnauthorizedException
     */
    public function validate(Request $request): string
    {
        $token = $request->query('token') ?? '';
        if ($token === '') {
            throw new UnauthorizedException();
        }
        return $token;
    }
}

<?php // app/Validators/UserRequestValidator.php
namespace App\Validators;

use App\Exceptions\EmptyIdException;
use App\Exceptions\UnauthorizedException;
use Illuminate\Http\Request;

class UserRequestValidator
{
    /**
     * @return array{string,string}  [$id, $token]
     * @throws UnauthorizedException
     * @throws EmptyIdException
     */
    public function validate(Request $request): array
    {
        // 1) Token
        $header = $request->header('Authorization', '');
        if (! str_starts_with($header, 'Bearer ')) {
            throw new UnauthorizedException();
        }
        $token = substr($header, 7);

        // 2) ID
        $id = $request->query('id', '');
        if ($id === '') {
            throw new EmptyIdException();
        }

        return [$id, $token];
    }
}

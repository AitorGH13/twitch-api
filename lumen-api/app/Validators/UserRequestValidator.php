<?php // app/Validators/UserRequestValidator.php
namespace App\Validators;

use Illuminate\Http\Request;
use App\Exceptions\EmptyIdException;
use App\Exceptions\UnauthorizedException;

class UserRequestValidator
{
    /**
     * @return array{string,string}  [$id, $token]
     * @throws EmptyIdException
     */
    public function validate(Request $request): array
    {
        $token = $request->attributes->get('token');
        if (! $token) {
            throw new UnauthorizedException();
        }

        $id = $request->query('id', '');
        if ($id === '' || ! ctype_digit($id)) {
            throw new EmptyIdException();
        }

        return [$id, $token];
    }
}

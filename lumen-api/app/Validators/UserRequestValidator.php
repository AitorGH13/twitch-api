<?php // app/Validators/UserRequestValidator.php
namespace App\Validators;

use App\Exceptions\EmptyIdException;
use Illuminate\Http\Request;

class UserRequestValidator
{
    public function validate(Request $request): array
    {
        $token = $request->query('token') ?? '';
        if (empty($token)) {
            // reusa UnauthorizedException en el controller
            return ['', ''];
        }

        $id = $request->query('id') ?? '';
        if (empty($id)) {
            throw new EmptyIdException();
        }

        return [$id, $token];
    }
}

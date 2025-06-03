<?php

namespace Unit\Validators;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailAddressException;
use App\Validators\RegisterValidator;
use Illuminate\Http\Request;
use Unit\BaseUnitTestCase;

class RegisterValidatorTest extends BaseUnitTestCase
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function makeRequest(?string $email): Request
    {
        return Request::create('/register', 'POST', $email === null ? [] : ['email' => $email]);
    }

    /** @test */
    public function missingEmailThrowsEmptyEmailException()
    {
        $validator = new RegisterValidator();

        $this->expectException(EmptyEmailException::class);
        $validator->validate($this->makeRequest(''));
    }

    /** @test */
    public function inavlidAddressesThrowsInvalidEmailException()
    {
        $validator = new RegisterValidator();

        $this->expectException(InvalidEmailAddressException::class);
        $validator->validate($this->makeRequest('notAnEmail@.com'));
    }

    /** @test */
    public function validAddressesReturnsSanitizedEmail()
    {
        $validator = new RegisterValidator();

        $email = 'test@testing.com';
        $sanitized = $validator->validate($this->makeRequest($email));

        $this->assertSame('test@testing.com', $sanitized);
    }
}

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
    private function createTestRequest(?string $email = null): Request
    {
        $endpoint = '/register';
        $method = 'POST';
        $postData = $email === null ? [] : ['email' => $email];

        return Request::create($endpoint, $method, $postData);
    }

    /** @test */
    public function emptyEmailThrowsEmptyEmailException()
    {
        $validator = new RegisterValidator();
        $reqEmptyEmail = $this->createTestRequest('');

        $this->expectException(EmptyEmailException::class);
        $validator->validate($reqEmptyEmail);
    }

    /** @test */
    public function missingEmailParameterThrowsEmptyEmailException()
    {
        $validator = new RegisterValidator();
        $reqNoEmail = $this->createTestRequest(null);

        $this->expectException(EmptyEmailException::class);
        $validator->validate($reqNoEmail);
    }

    /**
     * @test
     * @dataProvider invalidEmailsProvider
     */
    public function invalidAddressesThrowsInvalidEmailException(string $invalidEmail)
    {
        $validator = new RegisterValidator();
        $reqInvEmail = $this->createTestRequest($invalidEmail);

        $this->expectException(InvalidEmailAddressException::class);
        $validator->validate($reqInvEmail);
    }

    public static function invalidEmailsProvider(): array
    {
        return [
            'missing domain part' => ['notAnEmail@.com'],
            'missing @' => ['notemail.com'],
            'multiple @ symbols' => ['not@an@email.com'],
            'invalid TLD' => ['email@domain.'],
            'missing domain' => ['email@'],
        ];
    }

    /** @test */
    public function validAddressesReturnsSanitizedEmail()
    {
        $validator = new RegisterValidator();
        $validEmail = 'test@testing.com';
        $reqValidEmail = $this->createTestRequest($validEmail);

        $sanitizedEmail = $validator->validate($reqValidEmail);

        $this->assertSame($validEmail, $sanitizedEmail);
    }
}

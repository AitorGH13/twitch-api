<?php

namespace Unit\Validators;

use App\Exceptions\EmptyApiKeyException;
use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailAddressException;
use App\Validators\TokenValidator;
use Illuminate\Http\Request;
use Unit\BaseUnitTestCase;

class TokenValidatorTest extends BaseUnitTestCase
{
    private TokenValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new TokenValidator();
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function createTestRequest(?string $email = null, ?string $apiKey = null): Request
    {
        $endpoint = '/token';
        $method = 'POST';
        $postData = [];

        if ($email !== null) {
            $postData['email'] = $email;
        }

        if ($apiKey !== null) {
            $postData['api_key'] = $apiKey;
        }

        return Request::create($endpoint, $method, $postData);
    }

    /**
     * @test
     * @group validationExceptions
     */
    public function missingEmailThrowsEmptyEmailException()
    {
        $emptyEmail = '';
        $validKey = 'apiKey123';
        $reqEmptyEmail = $this->createTestRequest($emptyEmail, $validKey);

        $this->expectException(EmptyEmailException::class);
        $this->validator->validate($reqEmptyEmail);
    }

    /**
     * @test
     * @dataProvider invalidEmailsProvider
     * @group validationExceptions
     */
    public function badAddressesThrowsInvalidEmailException(string $invalidEmail)
    {
        $validKey = 'apiKey123';
        $reqInvEmail = $this->createTestRequest($invalidEmail, $validKey);

        $this->expectException(InvalidEmailAddressException::class);
        $this->validator->validate($reqInvEmail);
    }

    public static function invalidEmailsProvider(): array
    {
        return [
            'missing domain part' => ['notAnEmail@.com'],
            'missing @' => ['notemail.com'],
            'multiple @ symbols' => ['not@an@email.com'],
            'invalid TLD' => ['email@domain.'],
            'missing domain' => ['email@'],
            'empty spaces only' => ['  '],
            'only username' => ['username'],
            'no TLD' => ['email@server'],
            'special characters' => ['email!@#$%@domain.com'],
        ];
    }

    /**
     * @test
     * @group validationExceptions
     */
    public function missingApiKeyThrowsEmptyApiKeyException()
    {
        $validEmail = 'test@testing.com';
        $emptyKey = '';
        $reqEmptyKey = $this->createTestRequest($validEmail, $emptyKey);

        $this->expectException(EmptyApiKeyException::class);
        $this->validator->validate($reqEmptyKey);
    }

    /**
     * @test
     * @group validationExceptions
     */
    public function apiKeyParameterCompletelyMissingThrowsEmptyApiKeyException()
    {
        $validEmail = 'complete@missing.com';
        $reqNoApiKey = $this->createTestRequest($validEmail, null);

        $this->expectException(EmptyApiKeyException::class);
        $this->validator->validate($reqNoApiKey);
    }

    /**
     * @test
     * @group validationExceptions
     */
    public function bothParametersMissingThrowsEmptyEmailException()
    {
        $reqNoParams = $this->createTestRequest(null, null);

        $this->expectException(EmptyEmailException::class);
        $this->validator->validate($reqNoParams);
    }

    /**
     * @test
     * @group extraParameters
     */
    public function additionalParametersAreIgnored()
    {
        $validEmail = 'additional@params.com';
        $validKey = 'xyz789';

        $req = $this->createTestRequest($validEmail, $validKey);
        $req->merge([
            'extra_param'   => 'should be ignored',
            'another_param' => 'also ignored'
        ]);

        $result = $this->validator->validate($req);

        $this->assertSame([
            'email'   => $validEmail,
            'api_key' => $validKey,
        ], $result);
    }

    /**
     * @test
     * @group validInputs
     */
    public function validInputsReturnsSanitizedDataAndApiKey()
    {
        $rawEmail = '  test@testing.com ';
        $sanitizedEmail = 'test@testing.com';
        $validKey = 'abc123';

        $reqValid = $this->createTestRequest($rawEmail, $validKey);

        $result = $this->validator->validate($reqValid);

        $this->assertSame([
            'email'   => $sanitizedEmail,
            'api_key' => $validKey,
        ], $result);
    }
}

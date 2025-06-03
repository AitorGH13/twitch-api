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
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function makeRequest(?string $email, ?string $apiKey): Request
    {
        $post = [];
        if ($email !== null) {
            $post['email']   = $email;
        }
        if ($apiKey !== null) {
            $post['api_key'] = $apiKey;
        }

        return Request::create('/token', 'POST', $post);
    }

    /** @test */
    public function missingEmailThrowsEmptyEmailException()
    {
        $validator = new TokenValidator();

        $this->expectException(EmptyEmailException::class);
        $validator->validate($this->makeRequest('', 'apiKey123'));
    }

    /** @test */
    public function badAddressesThrowsInvalidEmailException()
    {
        $validator = new TokenValidator();

        $this->expectException(InvalidEmailAddressException::class);
        $validator->validate($this->makeRequest('test@.com', 'apiKey123'));
    }

    /** @test */
    public function missingApiKeyThrowsEmptyApiKeyException()
    {
        $validator = new TokenValidator();

        $this->expectException(EmptyApiKeyException::class);
        $validator->validate($this->makeRequest('test@testing.com', ''));
    }

    /** @test */
    public function validInputsReturnsSanitizedDataAndApiKey()
    {
        $validator = new TokenValidator();

        $result = $validator->validate(
            $this->makeRequest('  test@testing.com ', 'abc123')
        );

        $this->assertSame([
            'email'   => 'test@testing.com',
            'api_key' => 'abc123',
        ], $result);
    }
}

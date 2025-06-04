<?php

namespace Unit\Validators;

use App\Exceptions\UnauthorizedException;
use App\Validators\StreamsValidator;
use Illuminate\Http\Request;
use Unit\BaseUnitTestCase;

class StreamsValidatorTest extends BaseUnitTestCase
{
    private StreamsValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new StreamsValidator();
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function createTestRequest(array $attrs = []): Request
    {
        $endpoint = '/streams';
        $method = 'GET';

        $request = Request::create($endpoint, $method);

        foreach ($attrs as $key => $value) {
            $request->attributes->set($key, $value);
        }

        return $request;
    }

    /**
     * @test
     * @group validationExceptions
     */
    public function missingTokenThrowsUnauthorizedException()
    {
        $reqNoToken = $this->createTestRequest();

        $this->expectException(UnauthorizedException::class);
        $this->validator->validate($reqNoToken);
    }

    /**
     * @test
     * @dataProvider validTokenFormatsProvider
     * @group validTokens
     */
    public function differentTokenFormatsAreAccepted(string $format)
    {
        $reqToken = $this->createTestRequest(['token' => $format]);

        $returned = $this->validator->validate($reqToken);

        $this->assertSame($format, $returned);
    }

    public static function validTokenFormatsProvider(): array
    {
        return [
            'alphanumeric'       => ['abc123def456'],
            'with dashes'       => ['abc123-def456-ghi789'],
            'with underscores'  => ['abc_123_def_456'],
            'long token'        => ['abcdef1234567890abcdef1234567890abcdef1234567890'],
            'with special chars' => [
                'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.'
                . 'eyJpc3MiOiJPbmxpbmUgSldUIEJ1aWxkZXIiLCJpYXQiOjE2MTcwNzE3Nzcs'
                . 'ImV4cCI6MTY0ODYwNzc3NywiYXVkIjoid3d3LmV4YW1wbGUuY29tIiwic3ViIjo'
                . 'ianJvY2tldEBleGFtcGxlLmNvbSJ9.'
                . '4JMwKCKs4qgPmgiRVgOUEA-m__qLX8a7gJmqrA'
            ],
        ];
    }

    /**
     * @test
     * @group validationExceptions
     */
    public function emptyTokenThrowsUnauthorizedException()
    {
        $reqEmptyToken = $this->createTestRequest(['token' => '']);

        $this->expectException(UnauthorizedException::class);
        $this->validator->validate($reqEmptyToken);
    }

    /**
     * @test
     * @group queryParameters
     */
    public function additionalQueryParametersDoNotAffectValidation()
    {
        $validToken = 'validToken789';
        $reqParams = $this->createTestRequest([
            'token'    => $validToken,
            'game_id'  => '123',
            'language' => 'es',
            'limit'    => '10',
        ]);

        $returned = $this->validator->validate($reqParams);

        $this->assertSame($validToken, $returned);
    }

    /**
     * @test
     * @group validTokens
     */
    public function validTokenReturnsToken()
    {
        $validToken = 'token123';
        $reqTokenValid = $this->createTestRequest(['token' => $validToken]);

        $returned = $this->validator->validate($reqTokenValid);

        $this->assertSame($validToken, $returned);
    }
}

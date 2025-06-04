<?php

namespace Unit\Validators;

use App\Exceptions\EmptyIdException;
use App\Exceptions\UnauthorizedException;
use App\Validators\StreamerValidator;
use Illuminate\Http\Request;
use Unit\BaseUnitTestCase;

class StreamerValidatorTest extends BaseUnitTestCase
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function createTestRequest(array $query = [], array $attrs = []): Request
    {
        $endpoint = '/streamer';
        $method = 'GET';

        $request = Request::create($endpoint, $method, $query);

        foreach ($attrs as $attributeName => $attributeValue) {
            $request->attributes->set($attributeName, $attributeValue);
        }

        return $request;
    }

    /** @test */
    public function missingTokenThrowsUnauthorizedException()
    {
        $streamerValidator = new StreamerValidator();
        $validUserId = '123';
        $requestWithoutToken = $this->createTestRequest(['id' => $validUserId]);

        $this->expectException(UnauthorizedException::class);
        $streamerValidator->validate($requestWithoutToken);
    }

    /** @test */
    public function missingIdParameterThrowsEmptyIdException()
    {
        $streamerValidator = new StreamerValidator();
        $validAccessToken = 'validToken456';

        $requestWithoutId = $this->createTestRequest(
            [], // Sin parámetro ID
            ['token' => $validAccessToken]
        );

        $this->expectException(EmptyIdException::class);
        $streamerValidator->validate($requestWithoutId);
    }

    /**
     * @test
     * @dataProvider invalidIdProvider
     * @group validationExceptions
     */
    public function throwsEmptyIdExceptionForInvalidIds(string $rawId)
    {
        $streamerValidator = new StreamerValidator();
        $mockAccessToken = 'validToken123';

        $requestWithInvalidId = $this->createTestRequest(
            ['id' => $rawId],
            ['token' => $mockAccessToken]
        );

        $this->expectException(EmptyIdException::class);
        $streamerValidator->validate($requestWithInvalidId);
    }

    public static function invalidIdProvider(): array
    {
        return [
            'empty string'      => [''],
            'letters only'      => ['abc'],
            'mixed chars'       => ['12a'],
            'special chars'     => ['123-456'],
            'whitespace'        => [' 123 '],
            'negative number'   => ['-123'],
            'decimal number'    => ['123.45'],
            'unicode chars'     => ['123😊'],
            'sql injection'     => ["123'; DROP TABLE users;--"],
            'html tags'         => ['<script>alert(123)</script>'],
        ];
    }

    /** @test */
    public function longNumericIdsAreAccepted()
    {
        $streamerValidator = new StreamerValidator();
        $longNumericId = '123456789012345';
        $validAccessToken = 'longIdToken';

        $requestWithLongId = $this->createTestRequest(
            ['id' => $longNumericId],
            ['token' => $validAccessToken]
        );

        [$returnedId, $returnedToken] = $streamerValidator->validate($requestWithLongId);

        $this->assertSame($longNumericId, $returnedId);
        $this->assertSame($validAccessToken, $returnedToken);
    }

    /** @test */
    public function validDataReturnsIdAndToken()
    {
        $streamerValidator = new StreamerValidator();
        $validUserId = '456';
        $validAccessToken = 'token123';

        $requestWithValidData = $this->createTestRequest(
            ['id' => $validUserId],
            ['token' => $validAccessToken]
        );

        [$returnedUserId, $returnedToken] = $streamerValidator->validate($requestWithValidData);

        $this->assertSame($validUserId, $returnedUserId);
        $this->assertSame($validAccessToken, $returnedToken);
    }
}

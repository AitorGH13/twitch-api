<?php

namespace Unit\Validators;

use App\Exceptions\InvalidLimitException;
use App\Exceptions\UnauthorizedException;
use App\Validators\EnrichedStreamsValidator;
use Illuminate\Http\Request;
use Unit\BaseUnitTestCase;

class EnrichedStreamsValidatorTest extends BaseUnitTestCase
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function createTestRequest(array $query = [], array $attrs = []): Request
    {
        $testEndpoint = '/streams/enriched';
        $request = Request::create($testEndpoint, 'GET', $query);

        foreach ($attrs as $name => $value) {
            $request->attributes->set($name, $value);
        }

        return $request;
    }

    /** @test */
    public function whenTokenIsMissingThrowsUnauthorizedException()
    {
        $validator = new EnrichedStreamsValidator();
        $validLimit = '5';
        $reqNoToken = $this->createTestRequest(['limit' => $validLimit]);

        $this->expectException(UnauthorizedException::class);
        $validator->validate($reqNoToken);
    }

    /**
     * @test
     * @dataProvider invalidLimitProvider
     */
    public function throwsInvalidLimitExceptionForInvalidLimitValues(string $rawLimit)
    {
        $validator = new EnrichedStreamsValidator();
        $mockToken = 'validToken123';

        $reqInvalidLimit = $this->createTestRequest(
            ['limit' => $rawLimit],
            ['token' => $mockToken]
        );

        $this->expectException(InvalidLimitException::class);
        $validator->validate($reqInvalidLimit);
    }

    public static function invalidLimitProvider(): array
    {
        return [
            'empty string'      => [''],
            'non-numeric'       => ['abc'],
            'mixed chars'       => ['12a'],
            'negative number'   => ['-5'],
            'decimal number'    => ['3.5'],
            'zero number'       => ['0'],
        ];
    }

    /** @test */
    public function validDataReturnsLimitAndToken()
    {
        $validator = new EnrichedStreamsValidator();
        $validLimit = '10';
        $expectedLimit = 10;
        $validToken = 'token123';

        $reqValid = $this->createTestRequest(
            ['limit' => $validLimit],
            ['token' => $validToken]
        );

        [$actualLimit, $actualToken] = $validator->validate($reqValid);

        $this->assertSame($expectedLimit, $actualLimit);
        $this->assertSame($validToken, $actualToken);
    }
}

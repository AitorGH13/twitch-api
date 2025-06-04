<?php

namespace Unit\Validators;

use App\Exceptions\InvalidSinceException;
use App\Exceptions\UnauthorizedException;
use App\Validators\TopOfTheTopsValidator;
use Illuminate\Http\Request;
use Unit\BaseUnitTestCase;

class TopOfTheTopsValidatorTest extends BaseUnitTestCase
{
    private TopOfTheTopsValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new TopOfTheTopsValidator();
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function createTestRequest(array $query = [], ?string $token = 'token123'): Request
    {
        $endpoint = '/topsofthetops';
        $method = 'GET';

        $request = Request::create($endpoint, $method, $query);

        if ($token !== null) {
            $request->attributes->set('token', $token);
        }

        return $request;
    }

    /**
     * @test
     * @group validationExceptions
     */
    public function missingTokenThrowsUnauthorizedException()
    {
        $requestWithoutToken = $this->createTestRequest(
            query: [],
            token: null
        );

        $this->expectException(UnauthorizedException::class);
        $this->validator->validate($requestWithoutToken);
    }

    /**
     * @test
     * @group validationExceptions
     */
    public function invalidSinceThrowsInvalidSinceException()
    {
        $invalidSinceParam = ['notSince' => '2'];
        $reqInvalidParam = $this->createTestRequest($invalidSinceParam);

        $this->expectException(InvalidSinceException::class);
        $this->validator->validate($reqInvalidParam);
    }

    /**
     * @test
     * @dataProvider invalidSinceValuesProvider
     * @group validationExceptions
     */
    public function invalidSinceValuesThrowInvalidSinceException(string $invalidSinceValue)
    {
        $reqInvalidSince = $this->createTestRequest(['since' => $invalidSinceValue]);

        $this->expectException(InvalidSinceException::class);
        $this->validator->validate($reqInvalidSince);
    }

    public static function invalidSinceValuesProvider(): array
    {
        return [
            'alphanumeric' => ['10s'],
            'alphabetic' => ['abc'],
            'special chars' => ['$500'],
            'negative number' => ['-100'],
            'decimal number' => ['10.5'],
            'empty string' => [''],
            'whitespace' => [' '],
            'sql injection attempt' => ["1; DROP TABLE users;"],
            'html tags' => ['<script>alert(1)</script>'],
        ];
    }

    /**
     * @test
     * @group validInputs
     */
    public function missingSinceValueReturnsTokenAndNull()
    {
        $defaultToken = 'token123';
        $requestWithoutSince = $this->createTestRequest();

        [$token, $since] = $this->validator->validate($requestWithoutSince);

        $this->assertSame($defaultToken, $token);
        $this->assertNull($since);
    }

    /**
     * @test
     * @dataProvider validSinceValuesProvider
     * @group validInputs
     */
    public function boundaryAndEdgeSinceValuesAreAccepted(string $sinceValue, int $expectedValue)
    {
        $reqSinceValue = $this->createTestRequest(['since' => $sinceValue]);

        [$token, $since] = $this->validator->validate($reqSinceValue);

        $this->assertSame($expectedValue, $since);
    }

    public static function validSinceValuesProvider(): array
    {
        return [
            'zero' => ['0', 0],
            'single digit' => ['5', 5],
            'large number' => ['999999999', 999999999],
            'with leading zeros' => ['00123', 123],
        ];
    }

    /**
     * @test
     * @group returnTypes
     */
    public function validateReturnsCorrectDataTypes()
    {
        $sinceValue = '789';
        $requestWithSince = $this->createTestRequest(['since' => $sinceValue]);

        [$token, $since] = $this->validator->validate($requestWithSince);

        $this->assertIsString($token);
        $this->assertIsInt($since);
        $this->assertSame(789, $since);
    }

    /**
     * @test
     * @group validInputs
     */
    public function validSinceValueReturnsTokenAndSince()
    {
        $validSinceValue = '600';
        $expectedParsedSince = 600;
        $defaultToken = 'token123';

        $reqValidSince = $this->createTestRequest(['since' => $validSinceValue]);

        [$token, $since] = $this->validator->validate($reqValidSince);

        $this->assertSame($defaultToken, $token);
        $this->assertSame($expectedParsedSince, $since);
    }
}

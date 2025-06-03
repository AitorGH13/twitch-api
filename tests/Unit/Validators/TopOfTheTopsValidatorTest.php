<?php

namespace Unit\Validators;

use App\Exceptions\InvalidSinceException;
use App\Exceptions\UnauthorizedException;
use App\Validators\TopOfTheTopsValidator;
use Illuminate\Http\Request;
use Unit\BaseUnitTestCase;

class TopOfTheTopsValidatorTest extends BaseUnitTestCase
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function makeRequest(array $query = [], ?string $token = 'token123'): Request
    {
        $request = Request::create('/topsofthetops', 'GET', $query);

        if ($token !== null) {
            $request->attributes->set('token', $token);
        }

        return $request;
    }

    /** @test */
    public function missingTokenThrowsUnauthorizedException()
    {
        $validator = new TopOfTheTopsValidator();

        $this->expectException(UnauthorizedException::class);
        $validator->validate($this->makeRequest(query: [], token: null));
    }

    /** @test */
    public function invalidSinceThrowsInvalidSinceException()
    {
        $validator = new TopOfTheTopsValidator();

        $request = $this->makeRequest(['notSince' => '2']);

        $this->expectException(InvalidSinceException::class);
        $validator->validate($request);
    }

    /** @test */
    public function nonNumericSinceValueThrowsInvalidSinceException()
    {
        $validator = new TopOfTheTopsValidator();

        $request = $this->makeRequest(['since' => '10s']);

        $this->expectException(InvalidSinceException::class);
        $validator->validate($request);
    }

    /** @test */
    public function missingSinceValueReturnsTokenAndNull()
    {
        $validator = new TopOfTheTopsValidator();

        [$token, $since] = $validator->validate($this->makeRequest());

        $this->assertSame('token123', $token);
        $this->assertNull($since);
    }

    /** @test */
    public function validSinceValueReturnsTokenAndSince()
    {
        $validator = new TopOfTheTopsValidator();

        [$token, $since] = $validator->validate($this->makeRequest(['since' => '600']));

        $this->assertSame('token123', $token);
        $this->assertSame(600, $since);
    }
}

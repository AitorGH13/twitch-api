<?php

namespace Unit\Validators;

use App\Exceptions\UnauthorizedException;
use App\Validators\StreamsValidator;
use Illuminate\Http\Request;
use Unit\BaseUnitTestCase;

class StreamsValidatorTest extends BaseUnitTestCase
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function makeRequest(array $attrs = []): Request
    {
        $request = Request::create('/streams', 'GET');
        foreach ($attrs as $k => $v) {
            $request->attributes->set($k, $v);
        }
        return $request;
    }

    /** @test */
    public function missingTokenThrowsUnauthorizedException()
    {
        $validator = new StreamsValidator();

        $this->expectException(UnauthorizedException::class);
        $validator->validate($this->makeRequest());
    }

    /** @test */
    public function validTokenReturnsToken()
    {
        $validator = new StreamsValidator();

        $token = $validator->validate($this->makeRequest(['token' => 'token123']));

        $this->assertSame('token123', $token);
    }
}

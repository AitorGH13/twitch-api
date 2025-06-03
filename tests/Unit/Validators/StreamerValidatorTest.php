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
    private function makeRequest(array $query = [], array $attrs = []): Request
    {
        $request = Request::create('/streamer', 'GET', $query);
        foreach ($attrs as $k => $v) {
            $request->attributes->set($k, $v);
        }
        return $request;
    }

    /** @test */
    public function missingTokenThrowsUnauthorizedException()
    {
        $validator = new StreamerValidator();

        $this->expectException(UnauthorizedException::class);
        $validator->validate($this->makeRequest(['id' => '123']));
    }

    /**
     * @test
     * @dataProvider invalidIdProvider
     */
    public function throwsEmptyIdExceptionForInvalidIds(string $rawId)
    {
        $validator = new StreamerValidator();

        $request = $this->makeRequest(['id' => $rawId], ['token' => 'tok']);

        $this->expectException(EmptyIdException::class);
        $validator->validate($request);
    }

    public static function invalidIdProvider(): array
    {
        return [
            'empty string'  => [''],
            'letters only'  => ['abc'],
            'mixed chars'   => ['12a'],
        ];
    }

    /** @test */
    public function validDataReturnsIdAndToken()
    {
        $validator = new StreamerValidator();

        $request = $this->makeRequest(['id' => '456'], ['token' => 'token123']);

        [$userId, $token] = $validator->validate($request);

        $this->assertSame('456', $userId);
        $this->assertSame('token123', $token);
    }
}

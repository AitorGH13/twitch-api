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
    private function makeRequest(array $query = [], array $attrs = []): Request
    {
        $request = Request::create('/streams', 'GET', $query);
        foreach ($attrs as $k => $v) {
            $request->attributes->set($k, $v);
        }
        return $request;
    }

    /** @test */
    public function whenTokenIsMissingThrowsUnauthorizedException()
    {
        $validator = new EnrichedStreamsValidator();

        $this->expectException(UnauthorizedException::class);
        $validator->validate($this->makeRequest(['limit' => '5']));
    }

    /**
     * @test
     * @dataProvider invalidLimitProvider
     */
    public function throwsInvalidLimitExceptionForInvalidLimitValues(string $rawLimit)
    {
        $validator = new EnrichedStreamsValidator();

        $req = $this->makeRequest(['limit' => $rawLimit], ['token' => 'tok']);

        $this->expectException(InvalidLimitException::class);
        $validator->validate($req);
    }

    public static function invalidLimitProvider(): array
    {
        return [
            'empty string'  => [''],
            'non-numeric'   => ['abc'],
            'mixed chars'   => ['12a'],
        ];
    }

    /** @test */
    public function validDataReturnsLimitAndToken()
    {
        $validator = new EnrichedStreamsValidator();

        $request = $this->makeRequest(['limit' => '10'], ['token' => 'token123']);

        [$limit, $token] = $validator->validate($request);

        $this->assertSame(10, $limit);
        $this->assertSame('token123', $token);
    }
}

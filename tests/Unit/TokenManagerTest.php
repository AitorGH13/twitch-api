<?php

namespace Tests\Unit;

use Tests\BaseUnitTestCase;
use App\Manager\TokenManager;
use App\Repository\DatabaseRepository;
use Illuminate\Http\JsonResponse;

class TokenManagerTest extends BaseUnitTestCase
{
    /** @test */
    public function generateTokenReturnsJsonResponseWith32CharToken()
    {
        $repo   = $this->mock(DatabaseRepository::class);
        $tokens = new TokenManager($repo);

        $response = $tokens->generateToken();        // método real
        $data     = $response->getData(true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(32, strlen($data['token']));
        $this->assertArrayHasKey('expires_at', $data);
    }
}

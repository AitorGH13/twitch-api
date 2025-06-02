<?php

namespace Unit\Services;

use App\Services\RegisterService;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Random\RandomException;
use Unit\BaseUnitTestCase;

class RegisterServiceTest extends BaseUnitTestCase
{
    /** @test
     * @throws RandomException
     */
    public function registersANewUserWithoutUpdatingApiKey()
    {
        $email = 'new@mail.com';

        $repo = $this->mock(UserRepositoryInterface::class);

        $repo->shouldReceive('getByEmail')
            ->once()->with($email)->andReturnNull();

        $repo->shouldNotReceive('updateApiKey');

        $repo->shouldReceive('register')
            ->once()
            ->withArgs(function ($e, $apiKey) use ($email) {
                $this->assertSame($email, $e);
                $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $apiKey);
                return true;
            });

        $service  = new RegisterService($repo);
        $response = $service->registerUser($email);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $body = $response->getData(true);

        $this->assertArrayHasKey('api_key', $body);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $body['api_key']);
    }

    /** @test
     * @throws RandomException
     */
    public function updatesApiKeyWhenUserAlreadyExists()
    {
        $email = 'old@mail.com';

        $repo = $this->mock(UserRepositoryInterface::class);

        $repo->shouldReceive('getByEmail')
            ->once()->with($email)->andReturn((object) ['id' => 1]);

        $repo->shouldReceive('updateApiKey')
            ->once()
            ->withArgs(function ($e, $apiKey) use ($email, &$capturedKey) {
                $this->assertSame($email, $e);
                $capturedKey = $apiKey;
                return true;
            });

        $repo->shouldReceive('register')
            ->once()
            ->withArgs(function ($e, $apiKey) use ($email, &$capturedKey) {
                $this->assertSame($email, $e);
                $this->assertSame($capturedKey, $apiKey);
                return true;
            });

        $service = new RegisterService($repo);
        $resp    = $service->registerUser($email);

        $this->assertSame(200, $resp->getStatusCode());
        $body = $resp->getData(true);
        $this->assertSame($capturedKey, $body['api_key']);
    }
}

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
        $testUserEmail = 'new@mail.com';
        $apiKeyPattern = '/^[a-f0-9]{32}$/'; // MD5 hash pattern
        $expectedStatusCode = 200;

        $mockUserRepository = $this->mock(UserRepositoryInterface::class);

        $mockUserRepository->shouldReceive('getByEmail')
            ->once()
            ->with($testUserEmail)
            ->andReturnNull();

        $mockUserRepository->shouldNotReceive('updateApiKey');

        $mockUserRepository->shouldReceive('register')
            ->once()
            ->withArgs(function ($receivedEmail, $generatedApiKey) use ($testUserEmail, $apiKeyPattern) {
                $this->assertSame($testUserEmail, $receivedEmail);
                $this->assertMatchesRegularExpression($apiKeyPattern, $generatedApiKey);
                return true;
            });

        $registerService = new RegisterService($mockUserRepository);
        $apiResponse = $registerService->registerUser($testUserEmail);

        $this->assertInstanceOf(JsonResponse::class, $apiResponse);
        $this->assertSame($expectedStatusCode, $apiResponse->getStatusCode());

        $responseBody = $apiResponse->getData(true);
        $this->assertArrayHasKey('api_key', $responseBody);
        $this->assertMatchesRegularExpression($apiKeyPattern, $responseBody['api_key']);
    }

    /** @test
     * @throws RandomException
     */
    public function updatesApiKeyWhenUserAlreadyExists()
    {
        $existingUserEmail = 'old@mail.com';
        $existingUserId = 1;
        $expectedStatusCode = 200;
        $capturedApiKey = null;

        $mockUserRepository = $this->mock(UserRepositoryInterface::class);

        $mockUserRepository->shouldReceive('getByEmail')
            ->once()
            ->with($existingUserEmail)
            ->andReturn((object) ['id' => $existingUserId]);

        $mockUserRepository->shouldReceive('updateApiKey')
            ->once()
            ->withArgs(function ($receivedEmail, $generatedApiKey) use ($existingUserEmail, &$capturedApiKey) {
                $this->assertSame($existingUserEmail, $receivedEmail);
                $capturedApiKey = $generatedApiKey;
                return true;
            });

        $mockUserRepository->shouldReceive('register')
            ->once()
            ->withArgs(function ($receivedEmail, $apiKey) use ($existingUserEmail, &$capturedApiKey) {
                $this->assertSame($existingUserEmail, $receivedEmail);
                $this->assertSame($capturedApiKey, $apiKey);
                return true;
            });

        $registerService = new RegisterService($mockUserRepository);
        $apiResponse = $registerService->registerUser($existingUserEmail);

        $this->assertSame($expectedStatusCode, $apiResponse->getStatusCode());
        $responseBody = $apiResponse->getData(true);
        $this->assertSame($capturedApiKey, $responseBody['api_key']);
    }
}

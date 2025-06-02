<?php

declare(strict_types=1);

namespace Tests;

use Mockery\Exception\RuntimeException;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Mockery\Container;
use ReflectionException;

abstract class BaseUnitTestCase extends BaseTestCase
{
    private ?Container $mockeryContainer = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockeryContainer = new Container();
    }

    protected function tearDown(): void
    {
        if ($this->mockeryContainer instanceof Container) {
            $this->mockeryContainer->mockery_close();
        }

        parent::tearDown();
    }

    /**
     * @throws ReflectionException
     * @throws RuntimeException
     */
    protected function mock(string $aliasOrClass): mixed
    {
        return $this->mockeryContainer->mock($aliasOrClass);
    }
}

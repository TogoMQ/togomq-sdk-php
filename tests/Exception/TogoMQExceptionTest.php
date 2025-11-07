<?php

declare(strict_types=1);

namespace TogoMQ\SDK\Tests\Exception;

use PHPUnit\Framework\TestCase;
use TogoMQ\SDK\Exception\TogoMQException;

class TogoMQExceptionTest extends TestCase
{
    public function testConstructorSetsMessageAndErrorCode(): void
    {
        $exception = new TogoMQException('Test message', TogoMQException::ERR_CODE_CONNECTION);

        $this->assertSame('Test message', $exception->getMessage());
        $this->assertSame(TogoMQException::ERR_CODE_CONNECTION, $exception->getErrorCode());
    }

    public function testDefaultErrorCode(): void
    {
        $exception = new TogoMQException('Test message');

        $this->assertSame(TogoMQException::ERR_CODE_STREAM, $exception->getErrorCode());
    }

    public function testConnectionFactoryMethod(): void
    {
        $exception = TogoMQException::connection('Connection failed');

        $this->assertInstanceOf(TogoMQException::class, $exception);
        $this->assertSame('Connection failed', $exception->getMessage());
        $this->assertSame(TogoMQException::ERR_CODE_CONNECTION, $exception->getErrorCode());
    }

    public function testAuthFactoryMethod(): void
    {
        $exception = TogoMQException::auth('Authentication failed');

        $this->assertSame('Authentication failed', $exception->getMessage());
        $this->assertSame(TogoMQException::ERR_CODE_AUTH, $exception->getErrorCode());
    }

    public function testValidationFactoryMethod(): void
    {
        $exception = TogoMQException::validation('Validation failed');

        $this->assertSame('Validation failed', $exception->getMessage());
        $this->assertSame(TogoMQException::ERR_CODE_VALIDATION, $exception->getErrorCode());
    }

    public function testPublishFactoryMethod(): void
    {
        $exception = TogoMQException::publish('Publish failed');

        $this->assertSame('Publish failed', $exception->getMessage());
        $this->assertSame(TogoMQException::ERR_CODE_PUBLISH, $exception->getErrorCode());
    }

    public function testSubscribeFactoryMethod(): void
    {
        $exception = TogoMQException::subscribe('Subscribe failed');

        $this->assertSame('Subscribe failed', $exception->getMessage());
        $this->assertSame(TogoMQException::ERR_CODE_SUBSCRIBE, $exception->getErrorCode());
    }

    public function testStreamFactoryMethod(): void
    {
        $exception = TogoMQException::stream('Stream failed');

        $this->assertSame('Stream failed', $exception->getMessage());
        $this->assertSame(TogoMQException::ERR_CODE_STREAM, $exception->getErrorCode());
    }

    public function testConfigurationFactoryMethod(): void
    {
        $exception = TogoMQException::configuration('Configuration error');

        $this->assertSame('Configuration error', $exception->getMessage());
        $this->assertSame(TogoMQException::ERR_CODE_CONFIGURATION, $exception->getErrorCode());
    }

    public function testPreviousException(): void
    {
        $previous = new \RuntimeException('Previous error');
        $exception = TogoMQException::connection('Connection failed', $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testAllErrorCodeConstants(): void
    {
        $this->assertSame('CONNECTION', TogoMQException::ERR_CODE_CONNECTION);
        $this->assertSame('AUTH', TogoMQException::ERR_CODE_AUTH);
        $this->assertSame('VALIDATION', TogoMQException::ERR_CODE_VALIDATION);
        $this->assertSame('PUBLISH', TogoMQException::ERR_CODE_PUBLISH);
        $this->assertSame('SUBSCRIBE', TogoMQException::ERR_CODE_SUBSCRIBE);
        $this->assertSame('STREAM', TogoMQException::ERR_CODE_STREAM);
        $this->assertSame('CONFIGURATION', TogoMQException::ERR_CODE_CONFIGURATION);
    }
}

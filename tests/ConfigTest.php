<?php

declare(strict_types=1);

namespace TogoMQ\SDK\Tests;

use PHPUnit\Framework\TestCase;
use TogoMQ\SDK\Config;

class ConfigTest extends TestCase
{
    public function testConstructorSetsToken(): void
    {
        $token = 'test-token-123';
        $config = new Config($token);

        $this->assertSame($token, $config->getToken());
    }

    public function testDefaultValues(): void
    {
        $config = new Config('test-token');

        $this->assertSame('q.togomq.io', $config->getHost());
        $this->assertSame(5123, $config->getPort());
        $this->assertSame('info', $config->getLogLevel());
    }

    public function testWithHostReturnsNewInstance(): void
    {
        $config = new Config('test-token');
        $newConfig = $config->withHost('custom.togomq.io');

        $this->assertNotSame($config, $newConfig);
        $this->assertSame('q.togomq.io', $config->getHost());
        $this->assertSame('custom.togomq.io', $newConfig->getHost());
    }

    public function testWithPortReturnsNewInstance(): void
    {
        $config = new Config('test-token');
        $newConfig = $config->withPort(9000);

        $this->assertNotSame($config, $newConfig);
        $this->assertSame(5123, $config->getPort());
        $this->assertSame(9000, $newConfig->getPort());
    }

    public function testWithLogLevelReturnsNewInstance(): void
    {
        $config = new Config('test-token');
        $newConfig = $config->withLogLevel('debug');

        $this->assertNotSame($config, $newConfig);
        $this->assertSame('info', $config->getLogLevel());
        $this->assertSame('debug', $newConfig->getLogLevel());
    }

    public function testWithLogLevelThrowsExceptionForInvalidLevel(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid log level');

        $config = new Config('test-token');
        $config->withLogLevel('invalid-level');
    }

    public function testGetAddress(): void
    {
        $config = new Config('test-token');
        $this->assertSame('q.togomq.io:5123', $config->getAddress());

        $customConfig = $config->withHost('localhost')->withPort(8080);
        $this->assertSame('localhost:8080', $customConfig->getAddress());
    }

    public function testFluentInterface(): void
    {
        $config = (new Config('test-token'))
            ->withHost('custom.togomq.io')
            ->withPort(9000)
            ->withLogLevel('debug');

        $this->assertSame('custom.togomq.io', $config->getHost());
        $this->assertSame(9000, $config->getPort());
        $this->assertSame('debug', $config->getLogLevel());
        $this->assertSame('test-token', $config->getToken());
    }

    public function testAllValidLogLevels(): void
    {
        $validLevels = ['debug', 'info', 'warn', 'error', 'none'];
        $config = new Config('test-token');

        foreach ($validLevels as $level) {
            $newConfig = $config->withLogLevel($level);
            $this->assertSame($level, $newConfig->getLogLevel());
        }
    }
}

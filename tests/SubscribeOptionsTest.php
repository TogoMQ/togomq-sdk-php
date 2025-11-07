<?php

declare(strict_types=1);

namespace TogoMQ\SDK\Tests;

use PHPUnit\Framework\TestCase;
use TogoMQ\SDK\SubscribeOptions;

class SubscribeOptionsTest extends TestCase
{
    public function testConstructorSetsTopic(): void
    {
        $options = new SubscribeOptions('test-topic');

        $this->assertSame('test-topic', $options->getTopic());
    }

    public function testDefaultValues(): void
    {
        $options = new SubscribeOptions('test-topic');

        $this->assertSame(0, $options->getBatch());
        $this->assertSame(0, $options->getSpeedPerSec());
    }

    public function testWithBatch(): void
    {
        $options = new SubscribeOptions('test-topic');
        $result = $options->withBatch(10);

        $this->assertSame($options, $result); // Fluent interface
        $this->assertSame(10, $options->getBatch());
    }

    public function testWithBatchNegativeValueBecomesZero(): void
    {
        $options = new SubscribeOptions('test-topic');
        $options->withBatch(-5);

        $this->assertSame(0, $options->getBatch());
    }

    public function testWithSpeedPerSec(): void
    {
        $options = new SubscribeOptions('test-topic');
        $result = $options->withSpeedPerSec(100);

        $this->assertSame($options, $result); // Fluent interface
        $this->assertSame(100, $options->getSpeedPerSec());
    }

    public function testWithSpeedPerSecNegativeValueBecomesZero(): void
    {
        $options = new SubscribeOptions('test-topic');
        $options->withSpeedPerSec(-10);

        $this->assertSame(0, $options->getSpeedPerSec());
    }

    public function testFluentInterface(): void
    {
        $options = (new SubscribeOptions('test-topic'))
            ->withBatch(20)
            ->withSpeedPerSec(50);

        $this->assertSame('test-topic', $options->getTopic());
        $this->assertSame(20, $options->getBatch());
        $this->assertSame(50, $options->getSpeedPerSec());
    }

    public function testWildcardTopic(): void
    {
        $options = new SubscribeOptions('*');
        $this->assertSame('*', $options->getTopic());

        $options = new SubscribeOptions('orders.*');
        $this->assertSame('orders.*', $options->getTopic());
    }

    public function testCompleteConfiguration(): void
    {
        $options = (new SubscribeOptions('events.*'))
            ->withBatch(5)
            ->withSpeedPerSec(10);

        $this->assertSame('events.*', $options->getTopic());
        $this->assertSame(5, $options->getBatch());
        $this->assertSame(10, $options->getSpeedPerSec());
    }
}

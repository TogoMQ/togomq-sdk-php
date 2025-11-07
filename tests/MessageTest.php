<?php

declare(strict_types=1);

namespace TogoMQ\SDK\Tests;

use PHPUnit\Framework\TestCase;
use TogoMQ\SDK\Message;

class MessageTest extends TestCase
{
    public function testConstructorSetsTopicAndBody(): void
    {
        $message = new Message('test-topic', 'test-body');

        $this->assertSame('test-topic', $message->getTopic());
        $this->assertSame('test-body', $message->getBody());
    }

    public function testConstructorWithEmptyBody(): void
    {
        $message = new Message('test-topic');

        $this->assertSame('test-topic', $message->getTopic());
        $this->assertSame('', $message->getBody());
    }

    public function testWithVariables(): void
    {
        $message = new Message('test-topic');
        $variables = ['key1' => 'value1', 'key2' => 'value2'];

        $result = $message->withVariables($variables);

        $this->assertSame($message, $result); // Fluent interface
        $this->assertSame($variables, $message->getVariables());
    }

    public function testWithPostpone(): void
    {
        $message = new Message('test-topic');
        $result = $message->withPostpone(60);

        $this->assertSame($message, $result); // Fluent interface
        $this->assertSame(60, $message->getPostpone());
    }

    public function testWithRetention(): void
    {
        $message = new Message('test-topic');
        $result = $message->withRetention(3600);

        $this->assertSame($message, $result); // Fluent interface
        $this->assertSame(3600, $message->getRetention());
    }

    public function testSetUuid(): void
    {
        $message = new Message('test-topic');
        $uuid = 'test-uuid-123';

        $result = $message->setUuid($uuid);

        $this->assertSame($message, $result); // Fluent interface
        $this->assertSame($uuid, $message->getUuid());
    }

    public function testGetVariableReturnsValue(): void
    {
        $message = (new Message('test-topic'))
            ->withVariables(['key1' => 'value1', 'key2' => 'value2']);

        $this->assertSame('value1', $message->getVariable('key1'));
        $this->assertSame('value2', $message->getVariable('key2'));
    }

    public function testGetVariableReturnsNullForNonExistentKey(): void
    {
        $message = new Message('test-topic');

        $this->assertNull($message->getVariable('non-existent'));
    }

    public function testGetVariableReturnsDefaultValue(): void
    {
        $message = new Message('test-topic');

        $this->assertSame('default', $message->getVariable('non-existent', 'default'));
    }

    public function testDefaultValues(): void
    {
        $message = new Message('test-topic', 'test-body');

        $this->assertSame([], $message->getVariables());
        $this->assertSame(0, $message->getPostpone());
        $this->assertSame(0, $message->getRetention());
        $this->assertNull($message->getUuid());
    }

    public function testFluentInterface(): void
    {
        $message = (new Message('test-topic', 'test-body'))
            ->withVariables(['key' => 'value'])
            ->withPostpone(30)
            ->withRetention(1800);

        $this->assertSame('test-topic', $message->getTopic());
        $this->assertSame('test-body', $message->getBody());
        $this->assertSame(['key' => 'value'], $message->getVariables());
        $this->assertSame(30, $message->getPostpone());
        $this->assertSame(1800, $message->getRetention());
    }

    public function testComplexMessage(): void
    {
        $message = (new Message('orders.premium', json_encode(['order_id' => 123])))
            ->withVariables([
                'priority' => 'high',
                'customer' => '12345',
            ])
            ->withPostpone(60)
            ->withRetention(3600)
            ->setUuid('uuid-123');

        $this->assertSame('orders.premium', $message->getTopic());
        $this->assertSame('{"order_id":123}', $message->getBody());
        $this->assertSame('high', $message->getVariable('priority'));
        $this->assertSame('12345', $message->getVariable('customer'));
        $this->assertSame(60, $message->getPostpone());
        $this->assertSame(3600, $message->getRetention());
        $this->assertSame('uuid-123', $message->getUuid());
    }
}

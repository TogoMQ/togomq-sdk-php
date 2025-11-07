<?php

declare(strict_types=1);

namespace TogoMQ\SDK;

/**
 * Subscription options for TogoMQ subscriptions
 */
class SubscribeOptions
{
    private string $topic;
    private int $batch = 0; // 0 means use server default (1000)
    private int $speedPerSec = 0; // 0 means unlimited

    /**
     * Create a new SubscribeOptions instance
     *
     * @param string $topic Topic to subscribe to (use "*" for all topics, "prefix.*" for wildcards)
     */
    public function __construct(string $topic)
    {
        $this->topic = $topic;
    }

    /**
     * Set the batch size (maximum messages to receive at once)
     *
     * @param int $batch Batch size (0 = server default of 1000)
     * @return self
     */
    public function withBatch(int $batch): self
    {
        $this->batch = max(0, $batch);

        return $this;
    }

    /**
     * Set the rate limit (messages per second)
     *
     * @param int $speedPerSec Messages per second (0 = unlimited)
     * @return self
     */
    public function withSpeedPerSec(int $speedPerSec): self
    {
        $this->speedPerSec = max(0, $speedPerSec);

        return $this;
    }

    /**
     * Get the topic
     *
     * @return string
     */
    public function getTopic(): string
    {
        return $this->topic;
    }

    /**
     * Get the batch size
     *
     * @return int
     */
    public function getBatch(): int
    {
        return $this->batch;
    }

    /**
     * Get the speed per second
     *
     * @return int
     */
    public function getSpeedPerSec(): int
    {
        return $this->speedPerSec;
    }
}

<?php

declare(strict_types=1);

namespace TogoMQ\SDK;

/**
 * Message class representing a TogoMQ message
 */
class Message
{
    private string $topic;
    private string $body;
    private array $variables = [];
    private int $postpone = 0;
    private int $retention = 0;
    private ?string $uuid = null;

    /**
     * Create a new Message instance
     *
     * @param string $topic Message topic (required)
     * @param string $body Message payload
     */
    public function __construct(string $topic, string $body = '')
    {
        $this->topic = $topic;
        $this->body = $body;
    }

    /**
     * Set message variables (custom metadata)
     *
     * @param array<string, string> $variables Key-value pairs
     * @return self
     */
    public function withVariables(array $variables): self
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * Set message postpone (delay in seconds)
     *
     * @param int $seconds Delay in seconds before message is available
     * @return self
     */
    public function withPostpone(int $seconds): self
    {
        $this->postpone = $seconds;

        return $this;
    }

    /**
     * Set message retention (how long to keep message in seconds)
     *
     * @param int $seconds Time to keep message in seconds
     * @return self
     */
    public function withRetention(int $seconds): self
    {
        $this->retention = $seconds;

        return $this;
    }

    /**
     * Set message UUID (for received messages)
     *
     * @param string $uuid Message UUID
     * @return self
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get message topic
     *
     * @return string
     */
    public function getTopic(): string
    {
        return $this->topic;
    }

    /**
     * Get message body
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Get message variables
     *
     * @return array<string, string>
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * Get message postpone value
     *
     * @return int
     */
    public function getPostpone(): int
    {
        return $this->postpone;
    }

    /**
     * Get message retention value
     *
     * @return int
     */
    public function getRetention(): int
    {
        return $this->retention;
    }

    /**
     * Get message UUID
     *
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * Get a specific variable value
     *
     * @param string $key Variable key
     * @param string|null $default Default value if key not found
     * @return string|null
     */
    public function getVariable(string $key, ?string $default = null): ?string
    {
        return $this->variables[$key] ?? $default;
    }
}

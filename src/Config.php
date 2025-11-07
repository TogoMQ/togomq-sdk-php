<?php

declare(strict_types=1);

namespace TogoMQ\SDK;

/**
 * Configuration class for TogoMQ Client
 */
class Config
{
    private string $host = 'q.togomq.io';
    private int $port = 5123;
    private string $token;
    private string $logLevel = 'info';

    /**
     * Create a new Config instance
     *
     * @param string $token Authentication token (required)
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Set the TogoMQ server host
     *
     * @param string $host Server hostname
     * @return self
     */
    public function withHost(string $host): self
    {
        $clone = clone $this;
        $clone->host = $host;

        return $clone;
    }

    /**
     * Set the TogoMQ server port
     *
     * @param int $port Server port
     * @return self
     */
    public function withPort(int $port): self
    {
        $clone = clone $this;
        $clone->port = $port;

        return $clone;
    }

    /**
     * Set the log level
     *
     * @param string $logLevel One of: debug, info, warn, error, none
     * @return self
     */
    public function withLogLevel(string $logLevel): self
    {
        $validLevels = ['debug', 'info', 'warn', 'error', 'none'];
        if (! in_array($logLevel, $validLevels, true)) {
            throw new \InvalidArgumentException(
                "Invalid log level. Must be one of: " . implode(', ', $validLevels)
            );
        }

        $clone = clone $this;
        $clone->logLevel = $logLevel;

        return $clone;
    }

    /**
     * Get the server host
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Get the server port
     *
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Get the authentication token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Get the log level
     *
     * @return string
     */
    public function getLogLevel(): string
    {
        return $this->logLevel;
    }

    /**
     * Get the full server address
     *
     * @return string
     */
    public function getAddress(): string
    {
        return sprintf('%s:%d', $this->host, $this->port);
    }
}

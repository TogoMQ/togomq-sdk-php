<?php

declare(strict_types=1);

namespace TogoMQ\SDK\Exception;

/**
 * Base exception class for TogoMQ SDK errors
 */
class TogoMQException extends \Exception
{
    public const ERR_CODE_CONNECTION = 'CONNECTION';
    public const ERR_CODE_AUTH = 'AUTH';
    public const ERR_CODE_VALIDATION = 'VALIDATION';
    public const ERR_CODE_PUBLISH = 'PUBLISH';
    public const ERR_CODE_SUBSCRIBE = 'SUBSCRIBE';
    public const ERR_CODE_STREAM = 'STREAM';
    public const ERR_CODE_CONFIGURATION = 'CONFIGURATION';

    private string $errorCode;

    /**
     * Create a new TogoMQException
     *
     * @param string $message Error message
     * @param string $errorCode Error code (use ERR_CODE_* constants)
     * @param int $code Exception code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message,
        string $errorCode = self::ERR_CODE_STREAM,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
    }

    /**
     * Get the error code
     *
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Create a connection error
     *
     * @param string $message Error message
     * @param \Throwable|null $previous Previous exception
     * @return self
     */
    public static function connection(string $message, ?\Throwable $previous = null): self
    {
        return new self($message, self::ERR_CODE_CONNECTION, 0, $previous);
    }

    /**
     * Create an authentication error
     *
     * @param string $message Error message
     * @param \Throwable|null $previous Previous exception
     * @return self
     */
    public static function auth(string $message, ?\Throwable $previous = null): self
    {
        return new self($message, self::ERR_CODE_AUTH, 0, $previous);
    }

    /**
     * Create a validation error
     *
     * @param string $message Error message
     * @param \Throwable|null $previous Previous exception
     * @return self
     */
    public static function validation(string $message, ?\Throwable $previous = null): self
    {
        return new self($message, self::ERR_CODE_VALIDATION, 0, $previous);
    }

    /**
     * Create a publish error
     *
     * @param string $message Error message
     * @param \Throwable|null $previous Previous exception
     * @return self
     */
    public static function publish(string $message, ?\Throwable $previous = null): self
    {
        return new self($message, self::ERR_CODE_PUBLISH, 0, $previous);
    }

    /**
     * Create a subscribe error
     *
     * @param string $message Error message
     * @param \Throwable|null $previous Previous exception
     * @return self
     */
    public static function subscribe(string $message, ?\Throwable $previous = null): self
    {
        return new self($message, self::ERR_CODE_SUBSCRIBE, 0, $previous);
    }

    /**
     * Create a stream error
     *
     * @param string $message Error message
     * @param \Throwable|null $previous Previous exception
     * @return self
     */
    public static function stream(string $message, ?\Throwable $previous = null): self
    {
        return new self($message, self::ERR_CODE_STREAM, 0, $previous);
    }

    /**
     * Create a configuration error
     *
     * @param string $message Error message
     * @param \Throwable|null $previous Previous exception
     * @return self
     */
    public static function configuration(string $message, ?\Throwable $previous = null): self
    {
        return new self($message, self::ERR_CODE_CONFIGURATION, 0, $previous);
    }
}

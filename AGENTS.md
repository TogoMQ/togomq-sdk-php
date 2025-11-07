# AGENTS.md - AI Agent Instructions for TogoMQ SDK PHP

This document provides instructions for AI coding agents working with the TogoMQ SDK for PHP.

## Project Overview

**Project**: TogoMQ SDK for PHP  
**Purpose**: Official PHP SDK for TogoMQ message queue service  
**Technology Stack**: PHP 8.1+, gRPC, Composer  
**Architecture**: Object-oriented, PSR-4 autoloading, PSR-12 code style  

## Core Components

### 1. Configuration (`src/Config.php`)
- Immutable configuration object using builder pattern
- Default values: host=q.togomq.io, port=5123, logLevel=info
- Required: authentication token
- Methods use `with*()` pattern returning cloned instances

### 2. Message (`src/Message.php`)
- Represents messages for publishing and receiving
- Required: topic (string)
- Optional: body, variables (metadata), postpone (delay), retention
- Received messages include UUID
- Uses fluent interface for configuration

### 3. SubscribeOptions (`src/SubscribeOptions.php`)
- Configuration for subscriptions
- Required: topic (supports wildcards: `*` for all, `prefix.*` for pattern matching)
- Optional: batch size, speedPerSec (rate limiting)
- Default batch=0 (server default 1000), speedPerSec=0 (unlimited)

### 4. Client (`src/Client.php`)
- Main SDK entry point
- Manages gRPC connection with TLS
- Methods:
  - `pubBatch(Message[]): PubResponse` - Publish messages in batch
  - `sub(SubscribeOptions): Generator<Message>` - Subscribe using PHP generator
  - `close(): void` - Clean up connection
- Uses Bearer token authentication in gRPC metadata

### 5. Exception Handling (`src/Exception/TogoMQException.php`)
- Custom exception with error codes
- Error codes: CONNECTION, AUTH, VALIDATION, PUBLISH, SUBSCRIBE, STREAM, CONFIGURATION
- Static factory methods for each error type

### 6. Logger (`src/Logger.php`)
- PSR-3 LoggerInterface implementation
- Log levels: debug, info, warn/warning, error, none
- Simple stdout logging with timestamps

## Dependencies

### Required
- `php: ^8.1` - Modern PHP with strict types
- `togomq/togomq-grpc: dev-main` - Auto-generated gRPC client/protos
- `grpc/grpc: ^1.57` - gRPC PHP extension
- `psr/log: ^3.0` - PSR-3 logging interface

### Development
- `phpunit/phpunit: ^10.0` - Unit testing
- `friendsofphp/php-cs-fixer: ^3.0` - Code style fixer
- `phpstan/phpstan: ^1.10` - Static analysis
- `mockery/mockery: ^1.6` - Mocking framework

## Code Style Guidelines

1. **PHP Version**: Use PHP 8.1+ features (strict types, typed properties, named arguments)
2. **Declarations**: Always use `declare(strict_types=1);`
3. **Namespaces**: `TogoMQ\SDK\` for main code, `TogoMQ\SDK\Exception\` for exceptions
4. **Type Hints**: Use strict type hints for all parameters and return types
5. **Docblocks**: Include comprehensive PHPDoc for all public methods
6. **Visibility**: Prefer private over protected, use public only for API methods
7. **Immutability**: Config uses immutable builder pattern (clone on modification)
8. **Error Handling**: Use TogoMQException with appropriate error codes

## Testing Strategy

### Unit Tests Location
- `tests/` directory mirrors `src/` structure
- Namespace: `TogoMQ\SDK\Tests\`

### Test Coverage Areas
1. **Config**: Test builder pattern, defaults, validation
2. **Message**: Test fluent interface, getters, variables
3. **SubscribeOptions**: Test configuration options
4. **Client**: Mock gRPC calls, test error handling
5. **Exception**: Test error codes and factory methods
6. **Logger**: Test log levels and filtering

### Testing Best Practices
- Use Mockery for mocking gRPC client
- Test both success and error paths
- Validate exception error codes
- Test immutability of Config class

## Common Development Tasks

### Adding a New Feature
1. Update relevant class in `src/`
2. Add/update PHPDoc comments
3. Write unit tests in `tests/`
4. Update README.md with usage examples
5. Add example to `examples/` if applicable
6. Run tests: `composer test`
7. Run code style check: `composer cs-check`
8. Run static analysis: `composer analyse`

### Fixing a Bug
1. Write a failing test that reproduces the bug
2. Fix the bug in source code
3. Ensure test passes
4. Check for edge cases
5. Update documentation if behavior changed

### Updating Dependencies
1. Update `composer.json`
2. Run `composer update`
3. Test all functionality
4. Update README if requirements changed

## gRPC Integration

### Generated Code (from togomq-grpc-php)
- `Mq\V1\MqServiceClient` - Main gRPC service client
- `Mq\V1\PubRequest` - Publish request message
- `Mq\V1\PubResponse` - Publish response message
- `Mq\V1\SubRequest` - Subscribe request message
- `Mq\V1\MqMessage` - Message proto structure

### Authentication
- Uses TLS (ChannelCredentials::createSsl())
- Bearer token in metadata: `['authorization' => ['Bearer TOKEN']]`

### Streaming Pattern
- Subscribe uses gRPC server streaming
- Client.sub() returns PHP Generator
- Yields Message objects as they arrive
- Generator pattern allows foreach iteration

## CI/CD Pipeline

### GitHub Actions Workflows

#### CI Workflow (`.github/workflows/ci.yml`)
- Runs on: push, pull_request
- PHP versions: 8.1, 8.2, 8.3
- Steps: checkout, setup PHP, install dependencies, run tests, code style, static analysis

#### Release Workflow (`.github/workflows/release.yml`)
- Triggered by: version tags (v*.*.*)
- Uses semantic versioning
- Creates GitHub releases
- Could publish to Packagist (if configured)

### Versioning Strategy
- Follow Semantic Versioning (SemVer)
- Tags: v0.1.0, v0.2.0, v1.0.0, etc.
- Breaking changes: major version bump
- New features: minor version bump
- Bug fixes: patch version bump

## Best Practices for AI Agents

### When Modifying Code
1. Preserve strict typing and type hints
2. Maintain PSR-12 code style
3. Keep immutability patterns (especially Config)
4. Update corresponding tests
5. Update README examples if API changes
6. Add PHPDoc for new public methods

### When Adding Features
1. Check Go SDK for reference implementation
2. Follow existing patterns (fluent interfaces, builder pattern)
3. Add comprehensive error handling with appropriate error codes
4. Create example usage in `examples/`
5. Update README with new feature documentation

### When Debugging
1. Check log output (use debug log level)
2. Verify gRPC proto compatibility
3. Test with actual TogoMQ server if possible
4. Use static analysis: `composer analyse`

### When Reviewing Code
1. Ensure all public methods have PHPDoc
2. Check type safety (no mixed types without reason)
3. Verify exception handling uses TogoMQException
4. Confirm logging at appropriate levels
5. Validate examples are up-to-date

## Example Workflow for Common Changes

### Adding a New Client Method

```php
/**
 * Description of what the method does
 *
 * @param ParameterType $param Parameter description
 * @return ReturnType
 * @throws TogoMQException
 */
public function methodName(ParameterType $param): ReturnType
{
    $this->logger->debug('Descriptive log message', ['context' => 'value']);
    
    try {
        // Implementation
        
        $this->logger->info('Success message');
        return $result;
    } catch (\Throwable $e) {
        throw TogoMQException::appropriateType(
            'Error message: ' . $e->getMessage(),
            $e
        );
    }
}
```

### Adding Configuration Option

```php
// In Config.php
private string $newOption = 'default';

public function withNewOption(string $value): self
{
    // Validate if needed
    $clone = clone $this;
    $clone->newOption = $value;
    return $clone;
}

public function getNewOption(): string
{
    return $this->newOption;
}
```

## Resources

- **TogoMQ Website**: https://togomq.io
- **Go SDK Reference**: https://github.com/TogoMQ/togomq-sdk-go
- **gRPC PHP Docs**: https://grpc.io/docs/languages/php/
- **PSR-3 Logging**: https://www.php-fig.org/psr/psr-3/
- **PSR-4 Autoloading**: https://www.php-fig.org/psr/psr-4/
- **PSR-12 Code Style**: https://www.php-fig.org/psr/psr-12/

## Questions to Ask

Before implementing changes, consider:
1. Does this match the Go SDK behavior?
2. Is error handling comprehensive?
3. Are types strictly defined?
4. Is logging at appropriate level?
5. Do examples demonstrate the feature?
6. Are tests covering edge cases?
7. Is documentation updated?

## Maintenance Notes

- **gRPC Proto Changes**: Regenerate `togomq-grpc-php` package, update usage in Client
- **PHP Version Updates**: Test with new PHP versions, update composer.json
- **Dependency Updates**: Review breaking changes, update code accordingly
- **Security Issues**: Follow responsible disclosure, patch immediately

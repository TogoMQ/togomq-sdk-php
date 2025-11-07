# Contributing to TogoMQ SDK for PHP

Thank you for considering contributing to the TogoMQ SDK for PHP! This document provides guidelines and instructions for contributing.

## Code of Conduct

Please be respectful and constructive in all interactions.

## How to Contribute

### Reporting Bugs

1. Check if the bug has already been reported in [Issues](https://github.com/TogoMQ/togomq-sdk-php/issues)
2. If not, create a new issue with:
   - Clear title and description
   - Steps to reproduce
   - Expected vs actual behavior
   - PHP version and environment details
   - Code sample demonstrating the issue

### Suggesting Features

1. Check existing [Issues](https://github.com/TogoMQ/togomq-sdk-php/issues) and [Pull Requests](https://github.com/TogoMQ/togomq-sdk-php/pulls)
2. Create a new issue describing:
   - The feature and its use case
   - How it aligns with the SDK's goals
   - Example usage code

### Pull Requests

1. Fork the repository
2. Create a new branch: `git checkout -b feature/your-feature-name`
3. Make your changes following our coding standards
4. Write or update tests
5. Ensure all tests pass: `composer test`
6. Run code style fixes: `composer cs-fix`
7. Run static analysis: `composer analyse`
8. Commit with clear, descriptive messages
9. Push to your fork
10. Create a Pull Request

## Development Setup

### Requirements

- PHP 8.1 or higher
- Composer
- gRPC PHP extension
- Git

### Installation

```bash
# Clone your fork
git clone https://github.com/YOUR-USERNAME/togomq-sdk-php.git
cd togomq-sdk-php

# Install dependencies
composer install
```

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run specific test file
vendor/bin/phpunit tests/ConfigTest.php
```

### Code Quality Tools

```bash
# Fix code style
composer cs-fix

# Check code style (dry-run)
composer cs-check

# Run static analysis
composer analyse
```

## Coding Standards

### PHP Standards

- Follow PSR-12 code style
- Use strict typing: `declare(strict_types=1);`
- Use PHP 8.1+ features where appropriate
- All public methods must have PHPDoc comments

### Code Style

```php
<?php

declare(strict_types=1);

namespace TogoMQ\SDK;

/**
 * Class description
 */
class ExampleClass
{
    private string $property;

    /**
     * Method description
     *
     * @param string $param Parameter description
     * @return string Return value description
     * @throws ExceptionType When something goes wrong
     */
    public function exampleMethod(string $param): string
    {
        // Implementation
        return $result;
    }
}
```

### Testing

- Write unit tests for all new code
- Maintain or improve code coverage
- Test both success and error cases
- Use descriptive test method names

```php
public function testMethodNameDoesExpectedBehavior(): void
{
    // Arrange
    $object = new ExampleClass();
    
    // Act
    $result = $object->method();
    
    // Assert
    $this->assertSame('expected', $result);
}
```

### Commit Messages

Use clear, descriptive commit messages:

```
Add support for custom timeout configuration

- Add timeout parameter to Config class
- Update Client to respect timeout setting
- Add tests for timeout configuration
- Update README with timeout examples
```

## Project Structure

```
togomq-sdk-php/
├── src/                    # Source code
│   ├── Client.php         # Main client class
│   ├── Config.php         # Configuration
│   ├── Message.php        # Message class
│   ├── SubscribeOptions.php
│   ├── Logger.php
│   └── Exception/         # Exception classes
├── tests/                 # Unit tests
├── examples/              # Usage examples
├── .github/workflows/     # CI/CD pipelines
└── docs/                  # Documentation (if needed)
```

## Testing Your Changes

Before submitting a PR, ensure:

1. ✅ All tests pass: `composer test`
2. ✅ Code style is correct: `composer cs-check`
3. ✅ Static analysis passes: `composer analyse`
4. ✅ New features have tests
5. ✅ Documentation is updated
6. ✅ Examples demonstrate new features (if applicable)

## Release Process

Maintainers follow these steps for releases:

1. Update version in relevant files
2. Update CHANGELOG.md
3. Create a git tag: `git tag -a v1.0.0 -m "Release v1.0.0"`
4. Push tag: `git push origin v1.0.0`
5. GitHub Actions will create the release automatically

## Getting Help

- Read the [README](README.md)
- Check [existing issues](https://github.com/TogoMQ/togomq-sdk-php/issues)
- Review the [Go SDK](https://github.com/TogoMQ/togomq-sdk-go) for reference
- See [AGENTS.md](AGENTS.md) for implementation details

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

Thank you for contributing! 🎉

# TogoMQ SDK PHP - Quick Reference

## Installation

```bash
composer require togomq/togomq-sdk
```

## Basic Usage

### Publishing Messages

```php
use TogoMQ\SDK\Config;
use TogoMQ\SDK\Client;
use TogoMQ\SDK\Message;

// Create client
$config = new Config('your-token');
$client = new Client($config);

// Publish messages
$messages = [
    new Message('orders', 'order-1'),
    (new Message('orders', 'order-2'))
        ->withVariables(['priority' => 'high'])
        ->withPostpone(60)
        ->withRetention(3600),
];

$response = $client->pubBatch($messages);
echo "Published {$response->getMessagesReceived()} messages\n";

$client->close();
```

### Subscribing to Messages

```php
use TogoMQ\SDK\Config;
use TogoMQ\SDK\Client;
use TogoMQ\SDK\SubscribeOptions;

// Create client
$config = new Config('your-token');
$client = new Client($config);

// Subscribe
$options = new SubscribeOptions('orders');
$subscription = $client->sub($options);

foreach ($subscription as $message) {
    echo "Topic: {$message->getTopic()}\n";
    echo "Body: {$message->getBody()}\n";
    echo "UUID: {$message->getUuid()}\n";
}

$client->close();
```

## Configuration Options

```php
$config = (new Config('token'))
    ->withHost('custom.togomq.io')  // Default: q.togomq.io
    ->withPort(9000)                // Default: 5123
    ->withLogLevel('debug');        // Default: info
```

**Log Levels**: `debug`, `info`, `warn`, `error`, `none`

## Message Options

```php
$message = (new Message('topic', 'body'))
    ->withVariables(['key' => 'value'])  // Custom metadata
    ->withPostpone(60)                   // Delay 60 seconds
    ->withRetention(3600);               // Keep for 1 hour
```

## Subscription Options

```php
$options = (new SubscribeOptions('topic'))
    ->withBatch(10)          // Max 10 messages at once
    ->withSpeedPerSec(100);  // Max 100 messages/second
```

**Topic Wildcards**:
- `*` - All topics
- `orders.*` - All topics starting with "orders."

## Error Handling

```php
use TogoMQ\SDK\Exception\TogoMQException;

try {
    $client->pubBatch($messages);
} catch (TogoMQException $e) {
    echo "Error ({$e->getErrorCode()}): {$e->getMessage()}\n";
}
```

**Error Codes**:
- `CONNECTION` - Connection/network errors
- `AUTH` - Authentication failures
- `VALIDATION` - Invalid input
- `PUBLISH` - Publishing errors
- `SUBSCRIBE` - Subscription errors
- `STREAM` - Streaming errors
- `CONFIGURATION` - Config errors

## Development Commands

```bash
# Install dependencies
composer install

# Run tests
composer test

# Fix code style
composer cs-fix

# Static analysis
composer analyse

# All quality checks
composer test && composer cs-check && composer analyse
```

## Environment Setup

```bash
# Set your token
export TOGOMQ_TOKEN="your-token-here"

# Run examples
php examples/publish.php
php examples/subscribe.php
```

## Common Patterns

### Publish with Error Handling

```php
try {
    $messages = [new Message('topic', 'body')];
    $response = $client->pubBatch($messages);
    echo "Published {$response->getMessagesReceived()} messages\n";
} catch (TogoMQException $e) {
    error_log("Publish failed: {$e->getMessage()}");
}
```

### Subscribe with Batch Processing

```php
$options = (new SubscribeOptions('events'))
    ->withBatch(50);

foreach ($client->sub($options) as $message) {
    processMessage($message);
}
```

### Custom Configuration

```php
$config = (new Config($_ENV['TOGOMQ_TOKEN']))
    ->withHost($_ENV['TOGOMQ_HOST'] ?? 'q.togomq.io')
    ->withPort((int)($_ENV['TOGOMQ_PORT'] ?? 5123))
    ->withLogLevel($_ENV['LOG_LEVEL'] ?? 'info');
```

## Links

- [Full Documentation](README.md)
- [Examples](examples/)
- [Contributing](CONTRIBUTING.md)
- [TogoMQ Website](https://togomq.io)

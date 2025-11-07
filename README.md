# TogoMQ SDK for PHP

[![CI](https://github.com/TogoMQ/togomq-sdk-php/actions/workflows/ci.yml/badge.svg)](https://github.com/TogoMQ/togomq-sdk-php/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

The official PHP SDK for [TogoMQ](https://togomq.io/) - a modern, high-performance message queue service. This SDK provides a simple and intuitive API for publishing and subscribing to messages using gRPC streaming.

## Features

- 🚀 **High Performance**: Built on gRPC for efficient communication
- 📡 **Streaming Support**: Native support for streaming pub/sub operations
- 🔒 **Secure**: TLS encryption and token-based authentication
- 🎯 **Simple API**: Easy-to-use client with fluent configuration
- 📝 **Comprehensive Logging**: Configurable log levels for debugging
- ⚡ **Modern PHP**: Requires PHP 8.1+ with strict typing
- ✅ **Well Tested**: Comprehensive test coverage

## Requirements

- PHP 8.1 or higher
- gRPC PHP extension
- Access to a TogoMQ server
- Valid TogoMQ authentication token

## Installation

Install the SDK using Composer:

```bash
composer require togomq/togomq-sdk
```

Make sure you have the gRPC PHP extension installed:

```bash
pecl install grpc
```

## Configuration

The SDK supports flexible configuration with sensible defaults:

### Default Configuration

```php
<?php

use TogoMQ\SDK\Config;
use TogoMQ\SDK\Client;

// Create client with defaults (only token is required)
$config = new Config('your-token-here');

$client = new Client($config);
```

### Configuration Options

| Option | Default | Description |
|--------|---------|-------------|
| Host | q.togomq.io | TogoMQ server hostname |
| Port | 5123 | TogoMQ server port |
| LogLevel | info | Logging level (debug, info, warn, error, none) |
| Token | (required) | Authentication token |

### Custom Configuration

```php
<?php

use TogoMQ\SDK\Config;

$config = (new Config('your-token-here'))
    ->withHost('custom.togomq.io')
    ->withPort(9000)
    ->withLogLevel('debug');

$client = new Client($config);
```

## Usage

### Publishing Messages

#### Publishing a Batch of Messages

```php
<?php

use TogoMQ\SDK\Config;
use TogoMQ\SDK\Client;
use TogoMQ\SDK\Message;

// Create client
$config = new Config('your-token');
$client = new Client($config);

// Create messages - topic is required for each message
$messages = [
    new Message('orders', 'order-1'),
    (new Message('orders', 'order-2'))
        ->withVariables([
            'priority' => 'high',
            'customer' => '12345',
        ]),
    (new Message('orders', 'order-3'))
        ->withPostpone(60)      // Delay 60 seconds
        ->withRetention(3600),  // Keep for 1 hour
];

// Publish
$response = $client->pubBatch($messages);

echo "Published {$response->getMessagesReceived()} messages\n";

// Always close the client when done
$client->close();
```

### Subscribing to Messages

#### Basic Subscription

```php
<?php

use TogoMQ\SDK\Config;
use TogoMQ\SDK\Client;
use TogoMQ\SDK\SubscribeOptions;

// Create client
$config = new Config('your-token');
$client = new Client($config);

// Subscribe to specific topic
// Topic is required - use "*" to subscribe to all topics
$options = new SubscribeOptions('orders');
$subscription = $client->sub($options);

// Receive messages
foreach ($subscription as $message) {
    echo "Received message from {$message->getTopic()}: {$message->getBody()}\n";
    echo "Message UUID: {$message->getUuid()}\n";
    
    // Access variables
    $priority = $message->getVariable('priority');
    if ($priority !== null) {
        echo "Priority: {$priority}\n";
    }
}

$client->close();
```

#### Advanced Subscription with Options

```php
<?php

// Subscribe with batch size and rate limiting
// Default values: Batch = 0 (server default 1000), SpeedPerSec = 0 (unlimited)
$options = (new SubscribeOptions('orders.*'))  // Wildcard topic
    ->withBatch(10)                             // Receive up to 10 messages at once
    ->withSpeedPerSec(100);                     // Limit to 100 messages per second

$subscription = $client->sub($options);

foreach ($subscription as $message) {
    processMessage($message);
}
```

**Subscription Options:**

- **Batch**: Maximum number of messages to receive at once (default: 0 = server default 1000)
- **SpeedPerSec**: Rate limit for message delivery per second (default: 0 = unlimited)

#### Subscribe to All Topics (Wildcard)

```php
<?php

// Subscribe to all topics using "*" wildcard
$options = new SubscribeOptions('*'); // "*" = all topics
$subscription = $client->sub($options);
```

#### Subscribe with Pattern Wildcards

```php
<?php

// Subscribe to all orders topics (orders.new, orders.updated, etc.)
$options = new SubscribeOptions('orders.*');
$subscription = $client->sub($options);

// Subscribe to all topics
$options = new SubscribeOptions('*');
$subscription = $client->sub($options);
```

#### Subscription with Error Handling

```php
<?php

use TogoMQ\SDK\Exception\TogoMQException;

try {
    $options = new SubscribeOptions('events');
    $subscription = $client->sub($options);
    
    foreach ($subscription as $message) {
        processMessage($message);
    }
} catch (TogoMQException $e) {
    echo "Subscription error ({$e->getErrorCode()}): {$e->getMessage()}\n";
}
```

## Message Structure

### Publishing Message

**Important**: Topic is required when publishing messages.

```php
<?php

use TogoMQ\SDK\Message;

$message = new Message(
    topic: 'orders',           // Message topic (required)
    body: 'Message payload'    // Message payload
);

// Optional properties (fluent interface)
$message
    ->withVariables(['key' => 'value'])  // Custom metadata
    ->withPostpone(60)                   // Delay in seconds
    ->withRetention(3600);               // Keep for 1 hour
```

### Received Message

```php
<?php

// When receiving messages, they include:
$topic = $message->getTopic();              // Message topic
$uuid = $message->getUuid();                // Unique message identifier
$body = $message->getBody();                // Message payload
$variables = $message->getVariables();      // Custom metadata array
$value = $message->getVariable('key');      // Get specific variable
```

## Error Handling

The SDK provides detailed error information:

```php
<?php

use TogoMQ\SDK\Exception\TogoMQException;

try {
    $response = $client->pubBatch($messages);
} catch (TogoMQException $e) {
    // Check error type
    switch ($e->getErrorCode()) {
        case TogoMQException::ERR_CODE_AUTH:
            echo "Authentication failed\n";
            break;
        case TogoMQException::ERR_CODE_CONNECTION:
            echo "Connection error\n";
            break;
        case TogoMQException::ERR_CODE_VALIDATION:
            echo "Validation error\n";
            break;
        default:
            echo "Error: {$e->getMessage()}\n";
    }
}
```

### Error Codes

- `ERR_CODE_CONNECTION` - Connection or network errors
- `ERR_CODE_AUTH` - Authentication failures
- `ERR_CODE_VALIDATION` - Invalid input or configuration
- `ERR_CODE_PUBLISH` - Publishing errors
- `ERR_CODE_SUBSCRIBE` - Subscription errors
- `ERR_CODE_STREAM` - General streaming errors
- `ERR_CODE_CONFIGURATION` - Configuration errors

## Logging

Control logging verbosity with the `logLevel` configuration:

```php
<?php

$config = (new Config('your-token'))
    ->withLogLevel('debug'); // debug, info, warn, error, none
```

**Log levels:**

- `debug` - All logs including debug information
- `info` - Informational messages and above
- `warn` - Warnings and errors only
- `error` - Error messages only
- `none` - Disable logging

## Best Practices

1. **Reuse Clients**: Create one client per application and reuse it across requests
2. **Handle Errors**: Always check and handle errors appropriately
3. **Close Connections**: Always call `$client->close()` when done
4. **Batch Messages**: Use `pubBatch()` for better performance when publishing multiple messages
5. **Monitor Subscriptions**: Use try-catch blocks to handle subscription errors gracefully
6. **Validate Topics**: Always provide valid topic names for both publishing and subscribing

## Examples

Check out the `examples/` directory for complete working examples:

- `examples/publish.php` - Publishing examples
- `examples/subscribe.php` - Subscription examples

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

- Documentation: [https://togomq.io/docs](https://togomq.io/docs)
- Issues: [https://github.com/TogoMQ/togomq-sdk-php/issues](https://github.com/TogoMQ/togomq-sdk-php/issues)
- TogoMQ Website: [https://togomq.io](https://togomq.io/)

## Related Projects

- [togomq-grpc-php](https://github.com/TogoMQ/togomq-grpc-php) - Auto-generated gRPC protobuf definitions
- [togomq-sdk-go](https://github.com/TogoMQ/togomq-sdk-go) - TogoMQ SDK for Go
TogoMQ SDK library for PHP

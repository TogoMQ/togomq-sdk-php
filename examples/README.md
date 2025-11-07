# TogoMQ SDK PHP Examples

This directory contains examples demonstrating how to use the TogoMQ SDK for PHP.

## Setup

Before running the examples, make sure you have:

1. Installed dependencies:
   ```bash
   composer install
   ```

2. Set your TogoMQ token as an environment variable:
   ```bash
   export TOGOMQ_TOKEN="your-token-here"
   ```

## Available Examples

### publish.php
Demonstrates various ways to publish messages:
- Simple messages
- Messages with custom variables
- Delayed messages (postpone)
- Messages with retention
- Batch publishing
- Publishing to multiple topics

**Run:**
```bash
php examples/publish.php
```

### subscribe.php
Basic subscription example:
- Subscribe to a specific topic
- Receive and process messages
- Access message metadata and variables

**Run:**
```bash
php examples/subscribe.php [topic]
```

Default topic is `orders`. You can specify a different topic:
```bash
php examples/subscribe.php notifications
php examples/subscribe.php "orders.*"
php examples/subscribe.php "*"
```

### subscribe_advanced.php
Advanced subscription examples:
- Batch size configuration
- Rate limiting (messages per second)
- Wildcard topic subscriptions
- Subscribe to all topics

**Run:**
```bash
php examples/subscribe_advanced.php
```

## Common Usage Patterns

### Quick Publish Test
```bash
export TOGOMQ_TOKEN="your-token"
php examples/publish.php
```

### Quick Subscribe Test
```bash
export TOGOMQ_TOKEN="your-token"
php examples/subscribe.php
```

### Testing with Wildcards
```bash
# Subscribe to all orders topics
php examples/subscribe.php "orders.*"

# Subscribe to all topics
php examples/subscribe.php "*"
```

## Notes

- Press `Ctrl+C` to stop subscription examples
- Some examples have built-in limits to prevent infinite loops
- Make sure the gRPC PHP extension is installed
- Check the TogoMQ server connection details in your config

## Troubleshooting

**Error: "Failed to initialize TogoMQ client"**
- Check that gRPC extension is installed: `php -m | grep grpc`
- Verify your token is correct
- Ensure the TogoMQ server is accessible

**Error: "Authentication failed"**
- Verify your TOGOMQ_TOKEN is set correctly
- Check that your token hasn't expired

**No messages received in subscribe.php**
- Make sure messages are being published to the topic
- Try subscribing to `*` to see all messages
- Run `publish.php` in another terminal to generate test messages

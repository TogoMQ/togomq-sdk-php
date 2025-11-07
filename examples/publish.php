<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use TogoMQ\SDK\Client;
use TogoMQ\SDK\Config;
use TogoMQ\SDK\Exception\TogoMQException;
use TogoMQ\SDK\Message;

// Get token from environment variable or use a placeholder
$token = getenv('TOGOMQ_TOKEN') ?: 'your-token-here';

try {
    // Create client with configuration
    $config = (new Config($token))
        ->withLogLevel('info');  // Use 'debug' for more verbose output

    $client = new Client($config);

    echo "=== TogoMQ Publishing Examples ===\n\n";

    // Example 1: Simple message
    echo "Example 1: Publishing a simple message\n";
    $messages = [
        new Message('orders', 'Simple order message'),
    ];
    $response = $client->pubBatch($messages);
    echo "Published {$response->getMessagesReceived()} message(s)\n\n";

    // Example 2: Message with variables
    echo "Example 2: Publishing message with custom variables\n";
    $messages = [
        (new Message('orders', 'Order with metadata'))
            ->withVariables([
                'priority' => 'high',
                'customer_id' => '12345',
                'order_type' => 'express',
            ]),
    ];
    $response = $client->pubBatch($messages);
    echo "Published {$response->getMessagesReceived()} message(s)\n\n";

    // Example 3: Delayed message (postpone)
    echo "Example 3: Publishing delayed message (60 seconds)\n";
    $messages = [
        (new Message('notifications', 'Delayed notification'))
            ->withPostpone(60), // Message will be available after 60 seconds
    ];
    $response = $client->pubBatch($messages);
    echo "Published {$response->getMessagesReceived()} delayed message(s)\n\n";

    // Example 4: Message with retention
    echo "Example 4: Publishing message with retention (1 hour)\n";
    $messages = [
        (new Message('events', 'Event with retention'))
            ->withRetention(3600), // Keep message for 1 hour
    ];
    $response = $client->pubBatch($messages);
    echo "Published {$response->getMessagesReceived()} message(s) with retention\n\n";

    // Example 5: Batch publishing
    echo "Example 5: Publishing multiple messages in a batch\n";
    $messages = [];
    for ($i = 1; $i <= 10; $i++) {
        $messages[] = (new Message('batch-test', "Batch message #{$i}"))
            ->withVariables(['message_number' => (string) $i]);
    }
    $response = $client->pubBatch($messages);
    echo "Published {$response->getMessagesReceived()} messages in batch\n\n";

    // Example 6: Publishing to different topics
    echo "Example 6: Publishing to different topics\n";
    $messages = [
        new Message('orders', 'New order'),
        new Message('notifications', 'New notification'),
        new Message('events', 'New event'),
    ];
    $response = $client->pubBatch($messages);
    echo "Published {$response->getMessagesReceived()} messages to different topics\n\n";

    // Example 7: Complex message with all options
    echo "Example 7: Publishing complex message with all options\n";
    $messages = [
        (new Message('orders.premium', json_encode([
            'order_id' => 'ORD-12345',
            'items' => ['item1', 'item2'],
            'total' => 299.99,
        ])))
            ->withVariables([
                'priority' => 'urgent',
                'customer_tier' => 'premium',
                'region' => 'US-EAST',
            ])
            ->withPostpone(30)    // Delay 30 seconds
            ->withRetention(7200), // Keep for 2 hours
    ];
    $response = $client->pubBatch($messages);
    echo "Published {$response->getMessagesReceived()} complex message(s)\n\n";

    echo "All publishing examples completed successfully!\n";

    // Close the client
    $client->close();
} catch (TogoMQException $e) {
    echo "TogoMQ Error ({$e->getErrorCode()}): {$e->getMessage()}\n";
    exit(1);
} catch (\Throwable $e) {
    echo "Unexpected error: {$e->getMessage()}\n";
    exit(1);
}

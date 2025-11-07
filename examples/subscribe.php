<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use TogoMQ\SDK\Client;
use TogoMQ\SDK\Config;
use TogoMQ\SDK\Exception\TogoMQException;
use TogoMQ\SDK\SubscribeOptions;

// Get token from environment variable or use a placeholder
$token = getenv('TOGOMQ_TOKEN') ?: 'your-token-here';

// Get topic from command line argument or use default
$topic = $argv[1] ?? 'orders';

try {
    // Create client with configuration
    $config = (new Config($token))
        ->withLogLevel('info');  // Use 'debug' for more verbose output

    $client = new Client($config);

    echo "=== TogoMQ Subscription Examples ===\n\n";

    // Example 1: Basic subscription to a specific topic
    echo "Example 1: Subscribing to topic '{$topic}'\n";
    echo "Press Ctrl+C to stop...\n\n";

    $options = new SubscribeOptions($topic);
    $subscription = $client->sub($options);

    $messageCount = 0;
    foreach ($subscription as $message) {
        $messageCount++;

        echo str_repeat('-', 60) . "\n";
        echo "Message #{$messageCount}\n";
        echo "Topic: {$message->getTopic()}\n";
        echo "UUID: {$message->getUuid()}\n";
        echo "Body: {$message->getBody()}\n";

        // Display variables if present
        $variables = $message->getVariables();
        if (! empty($variables)) {
            echo "Variables:\n";
            foreach ($variables as $key => $value) {
                echo "  - {$key}: {$value}\n";
            }
        }

        echo str_repeat('-', 60) . "\n\n";

        // Optional: Stop after receiving a certain number of messages
        // if ($messageCount >= 10) {
        //     echo "Received 10 messages, stopping...\n";
        //     break;
        // }
    }

    // Close the client
    $client->close();
} catch (TogoMQException $e) {
    echo "TogoMQ Error ({$e->getErrorCode()}): {$e->getMessage()}\n";
    exit(1);
} catch (\Throwable $e) {
    echo "Unexpected error: {$e->getMessage()}\n";
    exit(1);
}

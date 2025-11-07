<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use TogoMQ\SDK\Client;
use TogoMQ\SDK\Config;
use TogoMQ\SDK\Exception\TogoMQException;
use TogoMQ\SDK\SubscribeOptions;

// Get token from environment variable or use a placeholder
$token = getenv('TOGOMQ_TOKEN') ?: 'your-token-here';

try {
    echo "=== TogoMQ Advanced Subscription Examples ===\n\n";

    // Example 1: Subscribe with batch size limit
    echo "Example 1: Subscribe with batch size (max 5 messages at once)\n";
    $config = new Config($token);
    $client = new Client($config);

    $options = (new SubscribeOptions('events'))
        ->withBatch(5);  // Receive up to 5 messages at once

    echo "Subscribing to 'events' topic with batch size 5...\n";
    echo "Press Ctrl+C to stop...\n\n";

    $subscription = $client->sub($options);
    $count = 0;

    foreach ($subscription as $message) {
        $count++;
        echo "[{$count}] {$message->getTopic()}: {$message->getBody()}\n";

        // Stop after 20 messages for demo
        if ($count >= 20) {
            break;
        }
    }

    $client->close();
    echo "\n";

    // Example 2: Subscribe with rate limiting
    echo "Example 2: Subscribe with rate limit (max 10 messages/sec)\n";
    $config = new Config($token);
    $client = new Client($config);

    $options = (new SubscribeOptions('notifications'))
        ->withSpeedPerSec(10);  // Limit to 10 messages per second

    echo "Subscribing to 'notifications' topic with 10 msg/sec limit...\n";
    echo "Press Ctrl+C to stop...\n\n";

    $subscription = $client->sub($options);
    $count = 0;
    $startTime = time();

    foreach ($subscription as $message) {
        $count++;
        $elapsed = time() - $startTime;
        $rate = $elapsed > 0 ? round($count / $elapsed, 2) : 0;

        echo "[{$count}] {$message->getTopic()} (Rate: {$rate} msg/sec)\n";

        // Stop after 30 messages for demo
        if ($count >= 30) {
            break;
        }
    }

    $client->close();
    echo "\n";

    // Example 3: Subscribe to wildcard topics
    echo "Example 3: Subscribe to wildcard topics (orders.*)\n";
    $config = new Config($token);
    $client = new Client($config);

    $options = new SubscribeOptions('orders.*');  // Match orders.new, orders.updated, etc.

    echo "Subscribing to 'orders.*' wildcard pattern...\n";
    echo "Press Ctrl+C to stop...\n\n";

    $subscription = $client->sub($options);
    $count = 0;

    foreach ($subscription as $message) {
        $count++;
        echo "[{$count}] {$message->getTopic()}: {$message->getBody()}\n";

        // Stop after 15 messages for demo
        if ($count >= 15) {
            break;
        }
    }

    $client->close();
    echo "\n";

    // Example 4: Subscribe to all topics
    echo "Example 4: Subscribe to all topics (*)\n";
    $config = new Config($token);
    $client = new Client($config);

    $options = new SubscribeOptions('*');  // Subscribe to ALL topics

    echo "Subscribing to ALL topics using '*'...\n";
    echo "Press Ctrl+C to stop...\n\n";

    $subscription = $client->sub($options);
    $count = 0;

    foreach ($subscription as $message) {
        $count++;
        echo "[{$count}] {$message->getTopic()}: {$message->getBody()}\n";

        // Stop after 10 messages for demo
        if ($count >= 10) {
            break;
        }
    }

    $client->close();
    echo "\n";

    echo "All advanced subscription examples completed!\n";
} catch (TogoMQException $e) {
    echo "TogoMQ Error ({$e->getErrorCode()}): {$e->getMessage()}\n";
    exit(1);
} catch (\Throwable $e) {
    echo "Unexpected error: {$e->getMessage()}\n";
    exit(1);
}

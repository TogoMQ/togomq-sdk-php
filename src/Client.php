<?php

declare(strict_types=1);

namespace TogoMQ\SDK;

use Grpc\ChannelCredentials;
use Mq\V1\MqMessage;
use Mq\V1\MqServiceClient;
use Mq\V1\PubRequest;
use Mq\V1\PubResponse;
use Mq\V1\SubRequest;
use TogoMQ\SDK\Exception\TogoMQException;

/**
 * TogoMQ Client for publishing and subscribing to messages
 */
class Client
{
    private Config $config;
    private Logger $logger;
    private MqServiceClient $grpcClient;
    private array $metadata = [];

    /**
     * Create a new TogoMQ Client
     *
     * @param Config $config Client configuration
     * @throws TogoMQException
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->logger = new Logger($config->getLogLevel());

        try {
            $this->initializeClient();
        } catch (\Throwable $e) {
            throw TogoMQException::connection(
                'Failed to initialize TogoMQ client: ' . $e->getMessage(),
                $e
            );
        }
    }

    /**
     * Initialize the gRPC client and set up authentication
     */
    private function initializeClient(): void
    {
        $this->logger->info('Initializing TogoMQ client', [
            'host' => $this->config->getHost(),
            'port' => $this->config->getPort(),
        ]);

        // Create gRPC client with TLS credentials
        $this->grpcClient = new MqServiceClient(
            $this->config->getAddress(),
            [
                'credentials' => ChannelCredentials::createSsl(),
            ]
        );

        // Set up authentication metadata
        $this->metadata = ['authorization' => ['Bearer ' . $this->config->getToken()]];

        $this->logger->debug('TogoMQ client initialized successfully');
    }

    /**
     * Publish a batch of messages
     *
     * @param Message[] $messages Array of messages to publish
     * @return PubResponse
     * @throws TogoMQException
     */
    public function pubBatch(array $messages): PubResponse
    {
        if (empty($messages)) {
            throw TogoMQException::validation('Messages array cannot be empty');
        }

        $this->logger->debug('Publishing batch of messages', ['count' => count($messages)]);

        try {
            // Create gRPC messages
            $grpcMessages = [];
            foreach ($messages as $message) {
                if (! $message instanceof Message) {
                    throw TogoMQException::validation('All items must be Message instances');
                }

                if (empty($message->getTopic())) {
                    throw TogoMQException::validation('Message topic is required');
                }

                $grpcMessage = new MqMessage();
                $grpcMessage->setTopic($message->getTopic());
                $grpcMessage->setBody($message->getBody());

                if (! empty($message->getVariables())) {
                    $grpcMessage->setVariables($message->getVariables());
                }

                if ($message->getPostpone() > 0) {
                    $grpcMessage->setPostpone($message->getPostpone());
                }

                if ($message->getRetention() > 0) {
                    $grpcMessage->setRetention($message->getRetention());
                }

                $grpcMessages[] = $grpcMessage;
            }

            // Create publish request
            $request = new PubRequest();
            $request->setMessages($grpcMessages);

            // Make the gRPC call
            [$response, $status] = $this->grpcClient->Pub($request, $this->metadata)->wait();

            if ($status->code !== \Grpc\STATUS_OK) {
                throw TogoMQException::publish(
                    'Failed to publish messages: ' . $status->details,
                    null
                );
            }

            $this->logger->info('Successfully published messages', [
                'received' => $response->getMessagesReceived(),
            ]);

            return $response;
        } catch (TogoMQException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw TogoMQException::publish(
                'Error publishing messages: ' . $e->getMessage(),
                $e
            );
        }
    }

    /**
     * Subscribe to messages from a topic
     *
     * This method returns a generator that yields Message objects as they arrive.
     *
     * @param SubscribeOptions $options Subscription options
     * @return \Generator<Message>
     * @throws TogoMQException
     */
    public function sub(SubscribeOptions $options): \Generator
    {
        if (empty($options->getTopic())) {
            throw TogoMQException::validation('Topic is required for subscription');
        }

        $this->logger->info('Starting subscription', [
            'topic' => $options->getTopic(),
            'batch' => $options->getBatch(),
            'speedPerSec' => $options->getSpeedPerSec(),
        ]);

        try {
            // Create subscription request
            $request = new SubRequest();
            $request->setTopic($options->getTopic());

            if ($options->getBatch() > 0) {
                $request->setBatch($options->getBatch());
            }

            if ($options->getSpeedPerSec() > 0) {
                $request->setSpeedPerSec($options->getSpeedPerSec());
            }

            // Start streaming subscription
            $call = $this->grpcClient->Sub($request, $this->metadata);

            $this->logger->debug('Subscription stream started');

            // Read messages from the stream
            foreach ($call->responses() as $response) {
                $grpcMessages = $response->getMessages();

                foreach ($grpcMessages as $grpcMessage) {
                    $message = new Message(
                        $grpcMessage->getTopic(),
                        $grpcMessage->getBody()
                    );

                    if ($grpcMessage->getUuid()) {
                        $message->setUuid($grpcMessage->getUuid());
                    }

                    $variables = $grpcMessage->getVariables();
                    if ($variables !== null && is_iterable($variables)) {
                        $varsArray = [];
                        foreach ($variables as $key => $value) {
                            $varsArray[$key] = $value;
                        }
                        if (! empty($varsArray)) {
                            $message->withVariables($varsArray);
                        }
                    }

                    $this->logger->debug('Received message', [
                        'topic' => $message->getTopic(),
                        'uuid' => $message->getUuid(),
                    ]);

                    yield $message;
                }
            }

            $this->logger->info('Subscription stream ended');
        } catch (\Throwable $e) {
            $this->logger->error('Subscription error: ' . $e->getMessage());

            throw TogoMQException::subscribe(
                'Error in subscription: ' . $e->getMessage(),
                $e
            );
        }
    }

    /**
     * Close the client connection
     */
    public function close(): void
    {
        $this->logger->debug('Closing TogoMQ client');
        $this->grpcClient->close();
    }

    /**
     * Destructor to ensure connection is closed
     */
    public function __destruct()
    {
        try {
            $this->grpcClient->close();
        } catch (\Throwable $e) {
            // Ignore errors during cleanup
        }
    }
}

<?php

namespace Gtlogistics\ExtensivClient\Serializer;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @internal
 */
class JsonSerializer implements SerializerInterface
{
    private StreamFactoryInterface $streamFactory;

    public function __construct(StreamFactoryInterface $streamFactory)
    {
        $this->streamFactory = $streamFactory;
    }

    public function serialize(RequestInterface $request, $payload): RequestInterface
    {
        $encoded = json_encode($payload, JSON_THROW_ON_ERROR);

        return $request
            ->withHeader('content-type', 'application/json')
            ->withBody($this->streamFactory->createStream($encoded));
    }

    public function deserialize(ResponseInterface $response)
    {
        return json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }
}

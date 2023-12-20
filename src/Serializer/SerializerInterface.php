<?php

namespace Gtlogistics\ExtensivClient\Serializer;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface SerializerInterface
{
    public function serialize(RequestInterface $request, $payload): RequestInterface;

    public function deserialize(ResponseInterface $response): array;
}

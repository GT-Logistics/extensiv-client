<?php

namespace Gtlogistics\ExtensivClient\Serializer;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface SerializerInterface
{
    /**
     * @param mixed $payload
     */
    public function serialize(RequestInterface $request, $payload): RequestInterface;

    /**
     * @return mixed
     */
    public function deserialize(ResponseInterface $response);
}

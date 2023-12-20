<?php

namespace Gtlogistics\ExtensivClient\Serializer;

use Psr\Http\Message\RequestInterface;

/**
 * @internal
 */
class JsonHalSerializer extends JsonSerializer
{
    public function serialize(RequestInterface $request, $payload): RequestInterface
    {
        return parent::serialize($request, $payload)
            ->withHeader('content-type', 'application/hal+json');
    }
}

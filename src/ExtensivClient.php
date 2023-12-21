<?php

namespace Gtlogistics\ExtensivClient;

use Gtlogistics\ExtensivClient\Authentication\AccessTokenAuthentication;
use Gtlogistics\ExtensivClient\Serializer\JsonHalSerializer;
use Gtlogistics\ExtensivClient\Serializer\JsonSerializer;
use Gtlogistics\ExtensivClient\Responses\PaginatedResponse;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\PluginClient;
use Psr\Clock\ClockInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

final class ExtensivClient
{
    private ClientInterface $client;

    private RequestFactoryInterface $requestFactory;

    private JsonSerializer $serializer;

    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        UriFactoryInterface $uriFactory,
        ClockInterface $clock,
        string $username,
        string $password,
        string $tpl,
        string $baseUri = 'https://secure-wms.com'
    ) {
        $uri = $uriFactory->createUri($baseUri);

        $this->client = new PluginClient($client, [
            new BaseUriPlugin($uri),
            new AuthenticationPlugin(
                new AccessTokenAuthentication($client, $requestFactory, $streamFactory, $clock, $uri, $username, $password, $tpl)
            ),
        ]);
        $this->requestFactory = $requestFactory;
        $this->serializer = new JsonHalSerializer($streamFactory);
    }

    /**
     * @api
     * @param mixed $payload
     * @return mixed
     */
    public function sendRequest(string $method, string $path, $payload = null)
    {
        $request = $this->requestFactory->createRequest($method, $path);
        if ($payload === null) {
            $request = $this->serializer->serialize($request, $payload);
        }

        $response = $this->client->sendRequest($request);

        return $this->serializer->deserialize($response);
    }

    /**
     * @api
     * @return iterable<mixed>
     */
    public function sendRequestPaginated(string $path): iterable
    {
        while (true) {
            $data = $this->sendRequest('GET', $path);
            /** @var PaginatedResponse<mixed> $paginatedResponse */
            $paginatedResponse = new PaginatedResponse($data);

            foreach ($paginatedResponse->getItems() as $item) {
                yield $item;
            }

            if (!$paginatedResponse->hasNext()) {
                break;
            }
            $path = $paginatedResponse->getNextUrl();
        }
    }
}

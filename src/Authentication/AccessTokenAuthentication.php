<?php

namespace Gtlogistics\ExtensivClient\Authentication;

use Gtlogistics\ExtensivClient\Serializer\JsonSerializer;
use Gtlogistics\ExtensivClient\Serializer\SerializerInterface;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\PluginClient;
use Http\Message\Authentication;
use Http\Message\Authentication\BasicAuth;
use Psr\Clock\ClockInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriInterface;

final class AccessTokenAuthentication implements Authentication
{
    private ClientInterface $client;

    private RequestFactoryInterface $requestFactory;

    private SerializerInterface $serializer;

    private ClockInterface $clock;

    private string $tpl;

    private ?AccessToken $token = null;

    /**
     * @internal
     */
    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        ClockInterface $clock,
        UriInterface $baseUri,
        string $username,
        string $password,
        string $tpl
    ) {
        $this->client = new PluginClient($client, [
            new BaseUriPlugin($baseUri),
            new AuthenticationPlugin(new BasicAuth($username, $password)),
        ]);
        $this->requestFactory = $requestFactory;
        $this->serializer = new JsonSerializer($streamFactory);
        $this->clock = $clock;
        $this->tpl = $tpl;
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        return $request->withHeader('Authorization', "Bearer {$this->getToken()}");
    }

    private function getToken(): AccessToken
    {
        if (!$this->tokenIsExpired()) {
            return $this->token;
        }

        $request = $this->requestFactory->createRequest('POST', 'AuthServer/api/Token');
        $request = $this->serializer->serialize($request, [
            'grant_type' => 'client_credentials',
            'tpl' => $this->tpl,
            'user_login_id' => '1',
        ]);
        $response = $this->client->sendRequest($request);

        /** @var array{
         *     access_token: string,
         *     token_type: string,
         *     expires_in: int,
         *     refresh_token: null,
         *     scope: null,
         * } $data
         */
        $data = $this->serializer->deserialize($response);
        $expires = $this->clock->now()->add(new \DateInterval("PT{$data['expires_in']}S"));

        return $this->token = new AccessToken($data['access_token'], $expires);
    }

    /**
     * @phpstan-assert-if-false !null $this->token
     */
    private function tokenIsExpired(): bool
    {
        if ($this->token === null) {
            return true;
        }

        return $this->token->getExpires() <= $this->clock->now()->sub(new \DateInterval('P5M'));
    }
}

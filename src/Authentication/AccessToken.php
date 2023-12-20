<?php

namespace Gtlogistics\ExtensivClient\Authentication;

final class AccessToken
{
    private string $token;

    private \DateTimeInterface $expires;

    public function __construct(string $token, \DateTimeInterface $expires)
    {
        $this->token = $token;
        $this->expires = $expires;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpires(): \DateTimeInterface
    {
        return $this->expires;
    }

    public function __toString(): string
    {
        return $this->token;
    }
}

<?php

namespace Gtlogistics\ExtensivClient\Responses;

use Psr\Http\Message\ResponseInterface;

final class PaginatedResponse
{
    /** @var array{
     *     totalResults: int,
     *     _links: array{
     *         prev?: array {
     *             href: string,
     *         },
     *         next?: array{
     *             href: string,
     *         },
     *     },
     *     _embedded: array{
     *         item: mixed[],
     *     },
     * }
     */
    private array $data;


    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getTotalResults(): int
    {
        return $this->data['totalResults'];
    }

    /**
     * @return mixed[]
     */
    public function getItems(): array
    {
        return $this->data['_embedded']['item'];
    }

    /**
     * @phpstan-assert-if-true !null $this->getPrevUrl()
     */
    public function hasPrev(): bool
    {
        return isset($this->data['_links']['prev']);
    }

    public function getPrevUrl(): ?string
    {
        return urldecode($this->data['_links']['prev']);
    }

    /**
     * @phpstan-assert-if-true !null $this->getNextUrl()
     */
    public function hasNext(): bool
    {
        return isset($this->data['_links']['next']);
    }

    public function getNextUrl(): ?string
    {
        return urldecode($this->data['_links']['next']);
    }
}

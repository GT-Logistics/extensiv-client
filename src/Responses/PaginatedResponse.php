<?php

namespace Gtlogistics\ExtensivClient\Responses;

/**
 * @template T
 */
final class PaginatedResponse
{
    /** @var array{
     *     totalResults: int,
     *     _links: array{
     *         prev?: array{
     *             href: string,
     *         },
     *         next?: array{
     *             href: string,
     *         },
     *     },
     *     _embedded: array{
     *         item: T[],
     *     },
     * }
     */
    private array $data;


    /**
     * @param mixed $data
     */
    public function __construct($data)
    {
        /** @phpstan-ignore-next-line */
        $this->data = $data;
    }

    public function getTotalResults(): int
    {
        return $this->data['totalResults'];
    }

    /**
     * @return T[]
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
        if (!isset($this->data['_links']['prev'])) {
            return null;
        }

        return urldecode($this->data['_links']['prev']['href']);
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
        if (!isset($this->data['_links']['next'])) {
            return null;
        }

        return urldecode($this->data['_links']['next']['href']);
    }
}

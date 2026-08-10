<?php

declare(strict_types=1);

namespace Mindee\V2\ClientOptions;

/**
 * Base parameters for searches.
 */
abstract class BaseSearchParameters
{
    /**
     * @var string Slug of the resource.
     */
    public static string $slug;

    /**
     * @param integer|null $page 1-based page index.
     * @param integer|null $perPage Number of items per page.
     */
    public function __construct(
        public ?int $page = null,
        public ?int $perPage = null
    ) {}

    /**
     * Gets the query parameters for the search request.
     *
     * @return array<string, string> Query parameters.
     */
    public function getQueryParams(): array
    {
        $params = [];
        if ($this->page !== null && $this->page > 0) {
            $params['page'] = (string) $this->page;
        }
        if ($this->perPage !== null && $this->perPage > 0) {
            $params['per_page'] = (string) $this->perPage;
        }
        return $params;
    }
}

<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Search;

use Stringable;

/**
 * Pagination metadata.
 */
class PaginationMetadata implements Stringable
{
    /**
     * @var integer Number of items per page.
     */
    public int $perPage;
    /**
     * @var integer 1-indexed page number.
     */
    public int $page;
    /**
     * @var integer Total number of items.
     */
    public int $totalItems;
    /**
     * @var integer Total number of pages.
     */
    public int $totalPages;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->perPage = $rawResponse['per_page'];
        $this->page = $rawResponse['page'];
        $this->totalItems = $rawResponse['total_items'];
        $this->totalPages = $rawResponse['total_pages'];
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return ":Per Page: $this->perPage\n"
            . ":Page: $this->page\n"
            . ":Total Items: $this->totalItems\n"
            . ":Total Pages: $this->totalPages\n";
    }
}

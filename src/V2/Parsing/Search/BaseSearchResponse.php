<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Search;

use Mindee\V2\Parsing\Inference\BaseResponse;
use Stringable;

/**
 * Base class for search responses.
 */
abstract class BaseSearchResponse extends BaseResponse implements Stringable
{
    /**
     * @var PaginationMetadata Pagination metadata for the search results.
     */
    public PaginationMetadata $pagination;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->pagination = new PaginationMetadata($rawResponse['pagination']);
    }

    /**
     * Lines composing the response-specific body (header + items).
     *
     * @return array<int, string> Body lines.
     */
    abstract protected function bodyLines(): array;

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return implode("\n", array_merge(
            $this->bodyLines(),
            [
                'Pagination Metadata',
                '###################',
                (string) $this->pagination,
                '',
            ]
        ));
    }
}

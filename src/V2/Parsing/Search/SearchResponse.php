<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Search;

use Mindee\V2\Parsing\Inference\BaseResponse;
use Stringable;

/**
 * Models search response.
 */
class SearchResponse extends BaseResponse implements Stringable
{
    /**
     * @var SearchModels Parsed search payload.
     */
    public SearchModels $models;

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
        $this->models = new SearchModels($rawResponse['models']);
        $this->pagination = new PaginationMetadata($rawResponse['pagination']);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return implode("\n", [
            'Models',
            '######',
            (string) $this->models,
            'Pagination Metadata',
            '###################',
            (string) $this->pagination,
            '',
        ]);
    }
}

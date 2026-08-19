<?php

declare(strict_types=1);

namespace Mindee\V2\Search\Models;

use Mindee\V2\Parsing\Search\BaseSearchResponse;
use Mindee\V2\Parsing\Search\SearchModels;

/**
 * Models search response.
 */
class ModelSearchResponse extends BaseSearchResponse
{
    /**
     * @var SearchModels List of all models matching the search query.
     */
    public SearchModels $models;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->models = new SearchModels($rawResponse['models']);
    }

    /**
     * @return array<int, string> Body lines.
     */
    protected function bodyLines(): array
    {
        return [
            'Models',
            '######',
            (string) $this->models,
        ];
    }
}

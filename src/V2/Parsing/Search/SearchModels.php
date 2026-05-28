<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Search;

use ArrayObject;
use Stringable;

use function count;

/**
 * Array of search models.
 * @extends ArrayObject<int, SearchModel>
 */
class SearchModels extends ArrayObject implements Stringable
{
    /**
     * @param array<array<string, int|float|string|bool|null|array<array-key, mixed>>> $prediction Raw prediction.
     */
    public function __construct(array $prediction)
    {
        $models = array_map(static fn($entry) => new SearchModel($entry), $prediction);

        parent::__construct($models);
    }

    /**
     * Default string representation.
     */
    public function __toString(): string
    {
        if ($this->count() === 0) {
            return "\n";
        }

        $lines = [];
        foreach ($this as $model) {
            $lines[] = "* :Name: " . $model->name;
            $lines[] = "  :ID: " . $model->id;
            $lines[] = "  :Model Type: " . $model->modelType;
            $lines[] = "  :Webhooks: " . count($model->webhooks);
        }

        return implode("\n", $lines) . "\n";
    }
}

<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Search;

use Stringable;

/**
 * Individual model information.
 */
class SearchModel implements Stringable
{
    /**
     * @var string Model ID.
     */
    public string $id;
    /**
     * @var string Model name.
     */
    public string $name;
    /**
     * @var string Model type.
     */
    public string $modelType;
    /**
     * @var array<ModelWebhook> List of webhooks associated with the model.
     */
    public array $webhooks;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->id = $rawResponse['id'];
        $this->name = $rawResponse['name'];
        $this->modelType = $rawResponse['model_type'];
        $this->webhooks = array_map(static fn($webhook) => new ModelWebhook($webhook), $rawResponse['webhooks']);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return ":Name: $this->name\n"
            . ":ID: $this->id\n"
            . ":Model Type: $this->modelType\n"
            . ":Webhooks: " . implode(', ', array_map(static fn($webhook) => $webhook->name, $this->webhooks)) . "\n";
    }
}

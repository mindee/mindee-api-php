<?php

declare(strict_types=1);

namespace Mindee\V2\ClientOptions;

/**
 * Base parameters for utility products chained to extraction.
 */
abstract class BaseClassificationProductParameters extends BaseProductParameters
{
    /**
     * @param string $modelId ID of the model.
     * @param string|null $alias Optional file alias.
     * @param array<string>|null $webhookIds List of webhook IDs.
     * @param string|null $extractionTextContext Additional context for the chained extraction model.
     */
    public function __construct(
        string $modelId,
        ?string $alias,
        ?array $webhookIds,
        public ?string $extractionTextContext = null,
    ) {
        parent::__construct($modelId, $alias, $webhookIds);
    }

    /**
     * @return array<string, string> Request parameters.
     */
    public function getRequestParameters(): array
    {
        $outHash = parent::getRequestParameters();
        if ($this->extractionTextContext !== null) {
            $outHash['extraction_text_context'] = $this->extractionTextContext;
        }
        return $outHash;
    }
}

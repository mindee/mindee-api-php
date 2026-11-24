<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Crop\Params;

use Mindee\V2\ClientOptions\BaseClassificationProductParameters;

/**
 * Parameters accepted by the asynchronous Crop product endpoint.
 */
class CropParameters extends BaseClassificationProductParameters
{
    /**
     * @var string Slug of the product.
     */
    public static string $slug = "crop";

    /**
     * @param string $modelId ID of the model.
     * @param string|null $alias Optional file alias.
     * @param array<string>|null $webhookIds List of webhook IDs.
     * @param string|null $extractionTextContext Additional context for the chained extraction model.
     */
    public function __construct(
        string $modelId,
        ?string $alias = null,
        ?array $webhookIds = null,
        ?string $extractionTextContext = null
    ) {
        parent::__construct($modelId, $alias, $webhookIds, $extractionTextContext);
    }
}

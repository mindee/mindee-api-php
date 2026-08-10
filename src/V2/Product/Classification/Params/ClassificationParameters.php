<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Classification\Params;

use Mindee\ClientOptions\PollingOptions;
use Mindee\V2\ClientOptions\BaseProductParameters;

/**
 * Parameters accepted by the asynchronous Classification product endpoint.
 */
class ClassificationParameters extends BaseProductParameters
{
    /**
     * @var string Slug of the prodcut.
     */
    public static string $slug = "classification";

    /**
     * @param string $modelId ID of the model.
     * @param string|null $alias Optional file alias.
     * @param array<string>|null $webhookIds List of webhook IDs.
     */
    public function __construct(
        string $modelId,
        ?string $alias = null,
        ?array $webhookIds = null
    ) {
        parent::__construct($modelId, $alias, $webhookIds);
    }
}

<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Split\Params;

use Mindee\ClientOptions\PollingOptions;
use Mindee\V2\ClientOptions\BaseParameters;

/**
 * Parameters for a split utility inference.
 */
class SplitParameters extends BaseParameters
{
    /**
     * @var string Slug of the endpoint.
     */
    public static string $slug = "split";

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

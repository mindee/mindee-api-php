<?php

declare(strict_types=1);

namespace Mindee\V2\Product\OCR\Params;

use Mindee\ClientOptions\PollingOptions;
use Mindee\V2\ClientOptions\BaseParameters;

/**
 * Parameters for an ocr utility inference.
 */
class OCRParameters extends BaseParameters
{
    /**
     * @var string Slug of the endpoint.
     */
    public static string $slug = "ocr";

    /**
     * @param string $modelId ID of the model.
     * @param string|null $alias Optional file alias.
     * @param array<string>|null $webhooksIds List of webhook IDs.
     */
    public function __construct(
        string $modelId,
        ?string $alias = null,
        ?array $webhooksIds = null
    ) {
        parent::__construct($modelId, $alias, $webhooksIds);
    }
}

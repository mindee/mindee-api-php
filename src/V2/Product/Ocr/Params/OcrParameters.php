<?php

namespace Mindee\V2\Product\Ocr\Params;

use Mindee\Input\PollingOptions;
use Mindee\V2\ClientOptions\BaseParameters;

/**
 * Parameters for an ocr utility inference.
 */
class OcrParameters extends BaseParameters
{
    /**
     * @var string Slug of the endpoint.
     */
    public static string $slug = "ocr";

    /**
     * @param string              $modelId        ID of the model.
     * @param string|null         $alias          Optional file alias.
     * @param array<string>|null  $webhooksIds    List of webhook IDs.
     * @param PollingOptions|null $pollingOptions Polling options.
     */
    public function __construct(
        string $modelId,
        ?string $alias = null,
        ?array $webhooksIds = null,
        ?PollingOptions $pollingOptions = null
    ) {
        parent::__construct($modelId, $alias, $webhooksIds, $pollingOptions);
    }
}

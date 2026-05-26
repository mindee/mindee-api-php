<?php

declare(strict_types=1);

/**
 * Generated V1.
 */

namespace Mindee\V1\Product\Generated;

use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * Generated document (API Builder) v1 inference results.
 */
class GeneratedV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "custom";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpointVersion = "1";

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);

        $this->prediction = new GeneratedV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            if ($page['prediction']) {
                $this->pages[] = new Page(GeneratedV1Page::class, $page);
            }
        }
    }
}

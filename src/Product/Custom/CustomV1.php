<?php

/**
 * Custom V1.
 */

namespace Mindee\Product\Custom;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;

/**
 * Custom document (API Builder) v1 inference results.
 */
class CustomV1 extends Inference
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
     * @param array $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);

        $this->prediction = new CustomV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            $this->pages[] = new Page(CustomV1Page::class, $page);
        }
    }
}

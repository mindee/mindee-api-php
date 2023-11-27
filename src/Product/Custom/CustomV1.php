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
    public static string $endpoint_name = "custom";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpoint_version = "1";

    /**
     * @param array $raw_prediction Raw prediction from the HTTP response.
     */
    public function __construct(array $raw_prediction)
    {
        parent::__construct($raw_prediction);
        $this->prediction = new CustomV1Document($raw_prediction['prediction']);
        $this->pages = [];
        foreach ($raw_prediction['pages'] as $page) {
            $this->pages[] = new Page(CustomV1Page::class, $page);
        }
    }
}

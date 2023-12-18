<?php

/** W9 V1. */

namespace Mindee\Product\Us\W9;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;

/**
 * Inference prediction for W9, API version 1.
 */
class W9V1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "us_w9";
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
        $this->prediction = new W9V1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            $pagePrediction = [];
            try {
                $pagePrediction = new Page(W9V1Page::class, $page);
            } catch (\Exception $ignored) {
            }
            $this->pages[] = $pagePrediction;
        }
    }
}

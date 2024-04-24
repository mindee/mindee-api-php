<?php

/** W9 V1. */

namespace Mindee\Product\Us\W9;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

/**
 * W9 API version 1 inference prediction.
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
            try {
                $this->pages[] = new Page(W9V1Page::class, $page);
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}

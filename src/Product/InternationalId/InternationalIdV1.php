<?php

/** International ID V1. */

namespace Mindee\Product\InternationalId;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;

/**
 * Inference prediction for International ID, API version 1.
 */
class InternationalIdV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "international_id";
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
        $this->prediction = new InternationalIdV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            $this->pages[] = new Page(InternationalIdV1Document::class, $page);
        }
    }
}

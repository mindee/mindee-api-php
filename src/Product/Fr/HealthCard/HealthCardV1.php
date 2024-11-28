<?php

/** Health Card V1. */

namespace Mindee\Product\Fr\HealthCard;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

/**
 * Health Card API version 1 inference prediction.
 */
class HealthCardV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "french_healthcard";
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
        $this->prediction = new HealthCardV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(HealthCardV1Document::class, $page);
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}

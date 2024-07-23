<?php

/** Healthcare Card V1. */

namespace Mindee\Product\Us\HealthcareCard;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

/**
 * Healthcare Card API version 1 inference prediction.
 */
class HealthcareCardV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "us_healthcare_cards";
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
        $this->prediction = new HealthcareCardV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(HealthcareCardV1Document::class, $page);
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}

<?php

/** Nutrition Facts Label V1. */

namespace Mindee\V1\Product\NutritionFactsLabel;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * Nutrition Facts Label API version 1 inference prediction.
 */
class NutritionFactsLabelV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "nutrition_facts";
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
        $this->prediction = new NutritionFactsLabelV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(NutritionFactsLabelV1Document::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}

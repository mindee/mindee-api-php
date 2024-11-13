<?php

/** Business Card V1. */

namespace Mindee\Product\BusinessCard;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

/**
 * Business Card API version 1 inference prediction.
 */
class BusinessCardV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "business_card";
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
        $this->prediction = new BusinessCardV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(BusinessCardV1Document::class, $page);
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}

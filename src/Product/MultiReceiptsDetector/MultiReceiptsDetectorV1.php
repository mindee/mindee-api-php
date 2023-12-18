<?php

/** Multi Receipts Detector V1. */

namespace Mindee\Product\MultiReceiptsDetector;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;

/**
 * Inference prediction for Multi Receipts Detector, API version 1.
 */
class MultiReceiptsDetectorV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "multi_receipts_detector";
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
        $this->prediction = new MultiReceiptsDetectorV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            $pagePrediction = [];
            try {
                $pagePrediction = new Page(MultiReceiptsDetectorV1Document::class, $page);
            } catch (\Exception $ignored) {
            }
            $this->pages[] = $pagePrediction;
        }
    }
}

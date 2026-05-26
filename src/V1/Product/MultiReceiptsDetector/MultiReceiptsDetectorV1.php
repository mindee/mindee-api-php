<?php

declare(strict_types=1);

/** Multi Receipts Detector V1. */

namespace Mindee\V1\Product\MultiReceiptsDetector;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * Multi Receipts Detector API version 1 inference prediction.
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
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new MultiReceiptsDetectorV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(MultiReceiptsDetectorV1Document::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}

<?php

/** Bill of Lading V1. */

namespace Mindee\Product\BillOfLading;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

/**
 * Bill of Lading API version 1 inference prediction.
 */
class BillOfLadingV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "bill_of_lading";
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
        $this->prediction = new BillOfLadingV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(BillOfLadingV1Document::class, $page);
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}

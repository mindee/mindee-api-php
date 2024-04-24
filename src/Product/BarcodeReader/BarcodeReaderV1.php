<?php

/** Barcode Reader V1. */

namespace Mindee\Product\BarcodeReader;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

/**
 * Barcode Reader API version 1 inference prediction.
 */
class BarcodeReaderV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "barcode_reader";
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
        $this->prediction = new BarcodeReaderV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(BarcodeReaderV1Document::class, $page);
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}

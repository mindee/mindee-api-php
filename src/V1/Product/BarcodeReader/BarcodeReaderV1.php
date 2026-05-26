<?php

declare(strict_types=1);

/** Barcode Reader V1. */

namespace Mindee\V1\Product\BarcodeReader;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

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
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new BarcodeReaderV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(BarcodeReaderV1Document::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}

<?php

declare(strict_types=1);

/** Invoice Splitter V1. */

namespace Mindee\V1\Product\InvoiceSplitter;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * Invoice Splitter API version 1 inference prediction.
 */
class InvoiceSplitterV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "invoice_splitter";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpointVersion = "1";

    /**
     * @param array<string, mixed> $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new InvoiceSplitterV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(InvoiceSplitterV1Document::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}

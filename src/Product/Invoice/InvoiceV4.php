<?php

/**
 * Invoice V4.
 */

namespace Mindee\Product\Invoice;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;

/**
 * Inference prediction for Invoice, API version 4.
 */
class InvoiceV4 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "invoices";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpointVersion = "4";

    /**
     * @param array $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new InvoiceV4Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            $this->pages[] = new Page(InvoiceV4Document::class, $page);
        }
    }
}

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
    public static string $endpoint_name = "invoices";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpoint_version = "4";

    /**
     * @param array $raw_prediction Raw prediction from the HTTP response.
     */
    public function __construct(array $raw_prediction)
    {
        parent::__construct($raw_prediction);
        $this->prediction = new InvoiceV4Document($raw_prediction['prediction']);
        $this->pages = [];
        foreach ($raw_prediction['pages'] as $page) {
            $this->pages[] = new Page(InvoiceV4Document::class, $page);
        }
    }
}

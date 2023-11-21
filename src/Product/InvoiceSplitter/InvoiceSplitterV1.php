<?php

namespace Mindee\Product\InvoiceSplitter;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;

class InvoiceSplitterV1 extends Inference
{
    public static string $endpoint_name = "invoice_splitter";
    public static string $endpoint_version = "1";

    public function __construct(array $raw_prediction)
    {
        parent::__construct($raw_prediction);
        $this->prediction = new InvoiceSplitterV1Document($raw_prediction['prediction']);
        $this->pages = [];
        foreach ($raw_prediction['pages'] as $page) {
            $this->pages[] = new Page(InvoiceSplitterV1Document::class, $page);
        }
    }
}

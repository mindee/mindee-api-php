<?php

namespace Mindee\Product\InvoiceSplitter;

use Mindee\Parsing\Common\Prediction;

/**
 * Document data for Invoice Splitter, API version 1.
 */
class InvoiceSplitterV1Document extends Prediction
{
    /**
     * Page groups linked to an invoice.
     */
    public array $invoicePageGroups;

    public function __construct(array $raw_prediction, ?int $page_id = null)
    {
        $this->invoicePageGroups = [];
        if (array_key_exists("invoice_page_groups", $raw_prediction)) {
            foreach ($raw_prediction['invoice_page_groups'] as $prediction) {
                $this->invoicePageGroups[] = new InvoiceSplitterV1PageGroup($prediction);
            }
        }
    }

    public function __toString(): string
    {
        $out_str = ":Invoice Page Groups:";
        foreach ($this->invoicePageGroups as $pageGroup) {
            $out_str .= "\n  $pageGroup";
        }
        return trim($out_str);
    }
}

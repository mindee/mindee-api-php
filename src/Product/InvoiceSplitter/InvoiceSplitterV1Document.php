<?php

namespace Mindee\Product\InvoiceSplitter;

use Mindee\Parsing\Common\Prediction;

/**
 * Document data for Invoice Splitter, API version 1.
 */
class InvoiceSplitterV1Document extends Prediction
{
    /**
     * @var array Page groups linked to an invoice.
     */
    public array $invoicePageGroups;

    /**
     * @param array $rawPrediction Raw prediction from HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        $this->invoicePageGroups = [];
        if (array_key_exists("invoice_page_groups", $rawPrediction)) {
            foreach ($rawPrediction['invoice_page_groups'] as $prediction) {
                $this->invoicePageGroups[] = new InvoiceSplitterV1PageGroup($prediction);
            }
        }
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $outStr = ":Invoice Page Groups:";
        foreach ($this->invoicePageGroups as $pageGroup) {
            $outStr .= "\n  $pageGroup";
        }
        return trim($outStr);
    }
}

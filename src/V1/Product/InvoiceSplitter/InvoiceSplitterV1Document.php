<?php

declare(strict_types=1);

namespace Mindee\V1\Product\InvoiceSplitter;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Prediction;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * Invoice Splitter API version 1.4 document data.
 */
class InvoiceSplitterV1Document extends Prediction
{
    /**
     * @var InvoiceSplitterV1InvoicePageGroups List of page groups. Each group represents a single invoice within a
     *                                         multi-invoice document.
     */
    public InvoiceSplitterV1InvoicePageGroups $invoicePageGroups;
    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["invoice_page_groups"])) {
            throw new MindeeUnsetException();
        }
        $this->invoicePageGroups = new InvoiceSplitterV1InvoicePageGroups(
            $rawPrediction["invoice_page_groups"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $invoicePageGroupsSummary = (string) ($this->invoicePageGroups);

        $outStr = ":Invoice Page Groups: $invoicePageGroupsSummary
";
        return SummaryHelperV1::cleanOutString($outStr);
    }
}

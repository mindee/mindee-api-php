<?php

declare(strict_types=1);

namespace Mindee\V1\Product\InvoiceSplitter;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;
use Stringable;

/**
 * List of page groups. Each group represents a single invoice within a multi-invoice document.
 */
class InvoiceSplitterV1InvoicePageGroup implements Stringable
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var integer[] List of page indexes that belong to the same invoice (group).
     */
    public array $pageIndexes;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, public ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->pageIndexes = $rawPrediction["page_indexes"] ?? [];
    }

    /**
     * Return values for printing inside an RST table.
     * @return array<string, string>
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["pageIndexes"] = implode(", ", $this->pageIndexes);
        return $outArr;
    }

    /**
     * Output in a format suitable for inclusion in an rST table.
     *
     */
    public function toTableLine(): string
    {
        $printable = $this->tablePrintableValues();
        $outStr = "| ";
        $outStr .= SummaryHelperV1::padString($printable["pageIndexes"], 72);
        return rtrim(SummaryHelperV1::cleanOutString($outStr));
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return SummaryHelperV1::cleanOutString($this->toTableLine());
    }
}

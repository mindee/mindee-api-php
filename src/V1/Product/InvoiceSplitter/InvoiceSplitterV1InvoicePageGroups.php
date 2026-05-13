<?php

declare(strict_types=1);

namespace Mindee\V1\Product\InvoiceSplitter;

use ArrayObject;

/**
 * List of page groups. Each group represents a single invoice within a multi-invoice document.
 * @extends ArrayObject<integer, InvoiceSplitterV1InvoicePageGroup>
 */
class InvoiceSplitterV1InvoicePageGroups extends ArrayObject
{
    /**
     * @param array<string, mixed> $rawPrediction Raw prediction array.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
            $entries[] = new InvoiceSplitterV1InvoicePageGroup($entry, $pageId);
        }
        parent::__construct($entries);
    }

    /**
     * Creates a line of rST table-compliant string separators.
     *
     * @param string $char Character to use as a separator.
     */
    public static function invoicePageGroupsSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 74);
        return $outStr . "+";
    }


    /**
     * String representation.
     *
     */
    public function __toString(): string
    {
        $lines = "";
        $iterator = $this->getIterator();
        if (!$iterator->valid()) {
            return "";
        }
        while ($iterator->valid()) {
            $entry = $iterator->current();
            $lines .= "\n  " . $entry->toTableLine() . "\n" . self::invoicePageGroupsSeparator('-');
            $iterator->next();
        }
        $outStr = "\n" . self::invoicePageGroupsSeparator('-') . "\n ";
        $outStr .= " | Page Indexes                                                            ";
        $outStr .= " |\n" . self::invoicePageGroupsSeparator('=');
        $outStr .= $lines;
        return $outStr;
    }
}

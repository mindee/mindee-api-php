<?php

declare(strict_types=1);

namespace Mindee\V1\Pdf;

use InvalidArgumentException;
use Mindee\Error\MindeePdfException;
use Mindee\Pdf\ExtractedPdf;
use Mindee\Pdf\PdfExtractor as BasePdfExtractor;
use Mindee\V1\Product\InvoiceSplitter\InvoiceSplitterV1InvoicePageGroups;

use function is_array;
use function count;

class PdfExtractor extends BasePdfExtractor
{
    /**
     * Extracts sub-documents from the source document using list of page indexes.
     *
     * @param array<array<integer>>|InvoiceSplitterV1InvoicePageGroups $pageIndexes List of sub-lists of pages to keep.
     *
     * @return ExtractedPdf[] list of extracted documents
     *
     * @throws MindeePdfException Throws if FDPF/FPDI wasn't able to handle the pdf during the extraction.
     * @throws InvalidArgumentException Throws if invalid indexes are provided.
     */
    public function extractSubDocuments(array|InvoiceSplitterV1InvoicePageGroups $pageIndexes): array
    {
        if (is_array($pageIndexes[0])) {
            $indexes = $pageIndexes;
        } else {
            $indexes = array_map(static fn($pageIndex) => $pageIndex->pageIndexes, (array) $pageIndexes);
        }
        return parent::extractSubDocuments($indexes);
    }


    /**
     * Extracts invoices as complete PDFs from the document.
     *
     * @param array<array<integer>>|InvoiceSplitterV1InvoicePageGroups $pageIndexes List of sub-lists of pages to keep.
     * @param boolean $strict Whether to trust confidence scores or not.
     *
     * @return ExtractedPdf[] a list of extracted invoices
     */
    public function extractInvoices(array|InvoiceSplitterV1InvoicePageGroups $pageIndexes, bool $strict = false): array
    {
        if (empty($pageIndexes)) {
            return [];
        }
        if (!$strict) {
            $indexes = array_map(static fn($invoicePageIndexes) => $invoicePageIndexes->pageIndexes, (array) $pageIndexes);

            return $this->extractSubDocuments($indexes);
        }
        if (is_array($pageIndexes[0])) {
            return parent::extractInvoices($pageIndexes, $strict);
        }

        $correctPageIndexes = [];
        $currentList = [];
        $previousConfidence = null;

        $i = 0;
        foreach ($pageIndexes as $pageIndex) {
            $confidence = $pageIndex->confidence;
            $pageList = $pageIndex->pageIndexes;

            if ($confidence >= 0.5 && null === $previousConfidence) {
                $currentList = $pageList;
            } elseif ($confidence >= 0.5 && $i !== count($pageIndexes) - 1) {
                if (!empty($currentList)) {
                    $correctPageIndexes[] = $currentList;
                }
                $currentList = $pageList;
            } elseif ($confidence < 0.5 && $i === count($pageIndexes) - 1) {
                $currentList = array_merge($currentList, $pageList);
                if (!empty($currentList)) {
                    $correctPageIndexes[] = $currentList;
                }
            } else {
                if (!empty($currentList)) {
                    $correctPageIndexes[] = $currentList;
                }
                $correctPageIndexes[] = $pageList;
            }

            $previousConfidence = $confidence;
            ++$i;
        }

        return $this->extractSubDocuments($correctPageIndexes);
    }
}

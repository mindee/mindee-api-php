<?php

namespace Mindee\Extraction;

use InvalidArgumentException;
use Mindee\Error\MindeePDFException;
use Mindee\Error\MindeeUnhandledException;
use Mindee\Input\LocalInputSource;
use Mindee\Parsing\DependencyChecker;
use Mindee\Product\InvoiceSplitter\InvoiceSplitterV1InvoicePageGroup;
use Mindee\Product\InvoiceSplitter\InvoiceSplitterV1InvoicePageGroups;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;

/**
 * PDF extraction class.
 */
class PdfExtractor
{
    /**
     * @var string Bytes representation of a file.
     */
    private string $pdfBytes;
    /**
     * @var string Name of the file.
     */
    private string $fileName;

    /**
     * @param LocalInputSource $localInput Local Input, accepts all compatible formats.
     * @throws MindeeUnhandledException|MindeePDFException Throws if PDF operations aren't supported,
     * or if the file can't be read, respectively.
     */
    public function __construct(LocalInputSource $localInput)
    {
        DependencyChecker::isImageMagickAvailable();
        DependencyChecker::isGhostscriptAvailable();
        $this->fileName = $localInput->fileName;

        if ($localInput->isPDF()) {
            $this->pdfBytes = $localInput->readContents()[1];
        } else {
            try {
                $image = new \Imagick();
            } catch (\ImagickException $e) {
                throw new MindeePDFException("Imagick could not process this file.\n", 0, $e);
            }
            $image->readImageBlob($localInput->readContents()[1]);
            $image->setImageFormat('pdf');
            $this->pdfBytes = $image->getImageBlob();
        }
    }

    /**
     * Wrapper for pdf GetPageCount().
     *
     * @return integer The number of pages in the file.
     * @throws MindeePDFException Throws if FPDI is unable to process the file.
     */
    public function getPageCount(): int
    {
        try {
            $pdfHandle = new FPDI();

            $tempFilename = tempnam(sys_get_temp_dir(), 'extracted_pdf_');
            file_put_contents($tempFilename, $this->pdfBytes);
            return $pdfHandle->setSourceFile($tempFilename);
        } catch (PdfParserException $e) {
            throw new MindeePDFException("Couldn't open PDF file. FPDI sent the following: ", 0, $e);
        }
    }


    /**
     * Extracts sub-documents from the source document using list of page indexes.
     *
     * @param array $pageIndexes List of sub-lists of pages to keep.
     * @return array List of extracted documents.
     * @throws MindeePDFException Throws if FDPF/FPDI wasn't able to handle the pdf during the extraction.
     * @throws InvalidArgumentException Throws if invalid indexes are provided.
     */
    public function extractSubDocuments(array $pageIndexes): array
    {
        $extractedPdfs = [];

        foreach ($pageIndexes as $pageIndexElem) {
            if (empty($pageIndexElem)) {
                throw new InvalidArgumentException("Empty indexes not allowed for extraction.");
            }

            $extension = pathinfo($this->fileName, PATHINFO_EXTENSION);
            $prefix = pathinfo($this->fileName, PATHINFO_FILENAME);
            $fieldFilename = sprintf(
                "%s_%03d-%03d.%s",
                $prefix,
                $pageIndexElem[0] + 1,
                $pageIndexElem[count($pageIndexElem) - 1] + 1,
                $extension
            );
            try {
                $pdf = new FPDI();
                $tempFilename = tempnam(sys_get_temp_dir(), 'extracted_pdf_');
                file_put_contents($tempFilename, $this->pdfBytes);
                $pdf->setSourceFile($tempFilename);

                foreach ($pageIndexElem as $pageIndex) {
                    $pdf->AddPage();
                    $pdf->useTemplate($pdf->importPage($pageIndex + 1));
                }

                $mergedPdfBytes = $pdf->Output('S');
            } catch (
                PdfParserException |
                CrossReferenceException |
                FilterException |
                PdfTypeException |
                PdfReaderException $e
            ) {
                throw new MindeePDFException("PDF file couldn't be processed during extraction.");
            }
            $extractedPdfs[] = new ExtractedPdf($mergedPdfBytes, $fieldFilename);
        }

        return $extractedPdfs;
    }

    /**
     * Extracts invoices as complete PDFs from the document.
     *
     * @param array| InvoiceSplitterV1InvoicePageGroups $pageIndexes List of sub-lists of pages to keep.
     * @param boolean                                   $strict      Whether to trust confidence scores or not.
     * @return array A list of extracted invoices.
     */
    public function extractInvoices($pageIndexes, bool $strict = false): array
    {
        if (empty($pageIndexes)) {
            return [];
        }
        if (!$strict) {
            $indexes = array_map(function ($invoicePageIndexes) {
                return $invoicePageIndexes->pageIndexes;
            }, (array)$pageIndexes);
            return $this->extractSubDocuments($indexes);
        } elseif (is_array($pageIndexes[0])) {
            return $this->extractSubDocuments($pageIndexes);
        }

        $correctPageIndexes = [];
        $currentList = [];
        $previousConfidence = null;

        $i = 0;
        foreach ($pageIndexes as $pageIndex) {
            $confidence = $pageIndex->confidence;
            $pageList = $pageIndex->pageIndexes;

            if ($confidence >= 0.5 && $previousConfidence === null) {
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
            $i++;
        }
        return $this->extractSubDocuments($correctPageIndexes);
    }

    /**
     * @return string Name of the file.
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}

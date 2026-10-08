<?php

declare(strict_types=1);

namespace Mindee\Pdf;

use BernardLedit\Pdf\PdfDocument;
use Exception;
use InvalidArgumentException;
use Mindee\Dependency\DependencyChecker;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeePdfException;
use Mindee\Input\LocalInputSource;

use function BernardLedit\Image\decode;
use function count;
use function sprintf;

/**
 * PDF extraction class.
 */
class PdfExtractor
{
    /**
     * @var string bytes representation of a file
     */
    private string $pdfBytes;

    /**
     * @var string name of the file
     */
    private readonly string $fileName;
    /**
     * @var integer number of pages in the file
     */
    public int $pageCount;

    /**
     * @param LocalInputSource $localInput Local Input, accepts all compatible formats.
     *
     * @throws MindeePdfException Throws if PDF operations aren't supported, or if the file
     *                            can't be read, respectively.
     */
    public function __construct(LocalInputSource $localInput)
    {
        DependencyChecker::requireBernardLedit();
        $this->fileName = $localInput->fileName;
        try {
            if ($localInput->isPdf()) {
                $this->pdfBytes = $localInput->readContents()[1];
            } else {
                $this->pdfBytes = decode($localInput->readContents()[1])->encode('PDF');

            }
            $this->pageCount = $this->getPageCount();
        } catch (Exception $e) {
            throw new MindeePdfException("PDF couldn't be opened. Bernard L'Édit sent the following: ", 0, $e);
        }
    }

    /**
     * Wrapper for pdf GetPageCount().
     *
     * @return integer The number of pages in the file.
     *
     * @throws MindeePdfException Throws if Bernard L'Édit is unable to process the file.
     */
    private function getPageCount(): int
    {
        try {
            $pdf = new PdfDocument($this->pdfBytes);
            return $pdf->pageCount();
        } catch (Exception $e) {
            throw new MindeePdfException(
                "PDF couldn't be opened.",
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        }
    }

    /**
     * Extracts sub-documents from the source document using list of page indexes.
     *
     * @param array<array<integer>> $pageIndexes List of sub-lists of pages to keep.
     *
     * @return ExtractedPdf[] list of extracted documents
     *
     * @throws MindeePdfException Throws if bernard_ledit wasn't able to handle the pdf during the extraction.
     */
    public function extractSubDocuments(array $pageIndexes): array
    {
        $extractedPdfs = [];
        $extension = pathinfo($this->fileName, PATHINFO_EXTENSION);
        $prefix = pathinfo($this->fileName, PATHINFO_FILENAME);

        try {
            $sourcePdf = new PdfDocument($this->pdfBytes);
        } catch (Exception $e) {
            throw new MindeePdfException("PDF file couldn't be processed during extraction.", 0, $e);
        }
        try {
            foreach ($pageIndexes as $pageIndexElem) {
                if (empty($pageIndexElem)) {
                    throw new InvalidArgumentException('Empty indexes not allowed for extraction.');
                }

                $fieldFilename = sprintf(
                    '%s_%03d-%03d.%s',
                    $prefix,
                    $pageIndexElem[0] + 1,
                    $pageIndexElem[count($pageIndexElem) - 1] + 1,
                    $extension
                );

                try {
                    $subPdf = PdfDocument::create();
                    $subPdf->importPages($sourcePdf, $pageIndexElem);
                    $mergedPdfBytes = $subPdf->save();
                    $subPdf->close();
                } catch (Exception $e) {
                    throw new MindeePdfException("PDF file couldn't be processed during extraction.", 0, $e);
                }
                $extractedPdfs[] = new ExtractedPdf($mergedPdfBytes, $fieldFilename);
            }
        } finally {
            try {
                $sourcePdf->close();
            } catch (Exception $e) {
                throw new MindeePdfException("PDF file couldn't be closed properly.", 0, $e);
            }
        }

        return $extractedPdfs;
    }

    /**
     * Extracts invoices as complete PDFs from the document.
     *
     * @param array<array<integer>> $pageIndexes List of sub-lists of pages to keep.
     * @param boolean $strict Whether to trust confidence scores or not.
     *
     * @return ExtractedPdf[] a list of extracted invoices
     * @throws Exception
     */
    public function extractInvoices(array $pageIndexes, bool $strict = false): array
    {
        if (empty($pageIndexes)) {
            return [];
        }
        return $this->extractSubDocuments($pageIndexes);
    }

    /**
     * @return string name of the file
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}

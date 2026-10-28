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
        $contents = $localInput->readContents()[1];
        if ($localInput->isPdf()) {
            $this->pdfBytes = $contents;
        } else {
            try {
                $this->pdfBytes = decode($contents)->encode('PDF');
            } catch (Exception $e) {
                throw new MindeePdfException(
                    "Couldn't convert image {$this->fileName} to PDF: {$e->getMessage()}",
                    ErrorCode::IMAGE_CANT_PROCESS,
                    $e
                );
            }
        }
        try {
            $this->pageCount = PdfUtils::withPdf($this->pdfBytes, static fn(PdfDocument $pdf): int => $pdf->getPageCount());
        } catch (Exception $e) {
            throw new MindeePdfException(
                "PDF couldn't be opened.",
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        }
    }

    /**
     * @param array<array<int>> $pageIndexes List of page-index groups.
     * @return ExtractedPdf[] list of extracted documents
     * @throws MindeePdfException Throws if bernard_ledit can't handle the PDF during the extraction.
     * @throws InvalidArgumentException Throws if a group of page indexes is empty.
     */
    public function extractSubDocuments(array $pageIndexes): array
    {
        $extension = pathinfo($this->fileName, PATHINFO_EXTENSION);
        $prefix = pathinfo($this->fileName, PATHINFO_FILENAME);

        try {
            $sourcePdf = new PdfDocument($this->pdfBytes);
        } catch (Exception $e) {
            throw new MindeePdfException(
                "PDF file couldn't be processed during extraction.",
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        }

        $extractedPdfs = [];
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
                $extractedPdfs[] = new ExtractedPdf(self::buildSubDocument($sourcePdf, $pageIndexElem), $fieldFilename);
            }
        } finally {
            PdfUtils::closeQuietly($sourcePdf);
        }

        return $extractedPdfs;
    }

    /**
     * @param array<int> $pageIndexes
     * @throws MindeePdfException
     */
    private static function buildSubDocument(PdfDocument $source, array $pageIndexes): string
    {
        $subPdf = null;
        try {
            $subPdf = PdfDocument::create();
            $subPdf->importPages($source, array_values($pageIndexes));
            return $subPdf->asBytes();
        } catch (Exception $e) {
            throw new MindeePdfException(
                "PDF file couldn't be processed during extraction.",
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        } finally {
            PdfUtils::closeQuietly($subPdf);
        }
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

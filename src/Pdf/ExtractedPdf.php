<?php

declare(strict_types=1);

namespace Mindee\Pdf;

use Mindee\Dependency\DependencyChecker;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeePdfException;
use Mindee\Error\MindeeUnhandledException;
use Mindee\Input\BytesInput;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\PdfParserException;

use const DIRECTORY_SEPARATOR;

/**
 * An extracted sub-Pdf.
 */
class ExtractedPdf
{
    /**
     * The number of pages in the file.
     */
    public int $pageCount;

    /**
     * Initializes a new instance of the ExtractedPdf class.
     *
     * @param string $pdfBytes A binary string representation of the PDF.
     * @param string $filename Name of the original file.
     *
     * @throws MindeeUnhandledException Throws if PDF operations aren't supported.
     */
    public function __construct(protected string $pdfBytes, public string $filename)
    {
        DependencyChecker::isImageMagickAvailable();
        DependencyChecker::isGhostscriptAvailable();
        $this->pageCount = $this->getPageCount();
    }

    /**
     * Wrapper for pdf GetPageCount().
     *
     * @return integer the number of pages in the file
     *
     * @throws MindeePdfException Throws if FPDI is unable to process the file.
     */
    private function getPageCount(): int
    {
        try {
            $pdfHandle = new Fpdi();

            $tempFilename = tempnam(sys_get_temp_dir(), 'extracted_pdf_');
            file_put_contents($tempFilename, $this->pdfBytes);

            return $pdfHandle->setSourceFile($tempFilename);
        } catch (PdfParserException $e) {
            throw new MindeePdfException(
                "Couldn't open PDF file.",
                ErrorCode::PDF_CANT_CREATE,
                $e
            );
        }
    }

    /**
     * Write the PDF to a file.
     *
     * @param string $outputPath The output directory (must exist).
     */
    public function writeToFile(string $outputPath): void
    {
        $pdfPath = $outputPath . DIRECTORY_SEPARATOR . $this->filename;
        if ('' !== basename($outputPath)) {
            if (!($pdfPath = realpath($outputPath))) {
                $pdfPath = $outputPath;
            }
        }
        if (!str_ends_with(strtolower($pdfPath), 'pdf')) {
            $pdfPath .= '.pdf';
        }
        file_put_contents($pdfPath, $this->pdfBytes);
    }

    /**
     * Return the file in a format suitable for sending to MindeeClient for parsing.
     *
     * @return BytesInput bytes input for the image
     */
    public function asInputSource(): BytesInput
    {
        return new BytesInput($this->pdfBytes, $this->filename);
    }

    /**
     * @return string the pdf bytes
     */
    public function getPdfBytes(): string
    {
        return $this->pdfBytes;
    }

    /**
     * @return string the name of the file
     */
    public function getFilename(): string
    {
        return $this->filename;
    }
}

<?php

namespace Mindee\Extraction;

use Mindee\Error\MindeePDFException;
use Mindee\Error\MindeeUnhandledException;
use Mindee\Input\BytesInput;
use Mindee\Parsing\DependencyChecker;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\PdfParserException;

/**
 * An extracted sub-Pdf.
 */
class ExtractedPdf
{
    /**
     * File object for an ExtractedPdf.
     *
     * @var string
     */
    protected string $pdfBytes;

    /**
     * Name of the original file.
     *
     * @var string
     */
    protected string $filename;

    /**
     * Initializes a new instance of the ExtractedPdf class.
     *
     * @param string $pdfBytes A binary string representation of the PDF.
     * @param string $filename Name of the original file.
     * @throws MindeeUnhandledException Throws if PDF operations aren't supported.
     */
    public function __construct(string $pdfBytes, string $filename)
    {
        if (!DependencyChecker::isImageMagickAvailable()) {
            throw new MindeeUnhandledException(
                "To enable full support of PDF features, you need " .
                "to enable ImageMagick on your PHP installation. Also, you " .
                "should setup ImageMagick's policy to allow for PDF operations."
            );
        }
        if (!DependencyChecker::isGhostscriptAvailable()) {
            throw new MindeeUnhandledException(
                "To enable full support of PDF features, you need " .
                "to enable Ghostscript on your PHP installation."
            );
        }
        $this->pdfBytes = $pdfBytes;
        $this->filename = $filename;
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
            throw new MindeePDFException("Couldn't open PDF file. FPDI sent the following: " . $e->getMessage());
        }
    }

    /**
     * Write the PDF to a file.
     *
     * @param string $outputPath The output directory (must exist).
     * @return void
     */
    public function writeToFile(string $outputPath): void
    {
        $pdfPath = $outputPath . DIRECTORY_SEPARATOR . $this->filename;
        if (basename($outputPath) !== '') {
            $pdfPath = realpath($outputPath);
        }
        file_put_contents($pdfPath, $this->pdfBytes);
    }

    /**
     * Return the file in a format suitable for sending to MindeeClient for parsing.
     *
     * @return BytesInput Bytes input for the image.
     */
    public function asInputSource(): BytesInput
    {
        return new BytesInput($this->pdfBytes, $this->filename);
    }

    /**
     * @return string The pdf bytes.
     */
    public function getPdfBytes(): string
    {
        return $this->pdfBytes;
    }

    /**
     * @return string The name of the file.
     */
    public function getFilename(): string
    {
        return $this->filename;
    }
}

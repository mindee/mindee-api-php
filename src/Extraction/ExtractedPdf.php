<?php

namespace Mindee\Extraction;

use Mindee\Error\ErrorCode;
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
     * @var string name of the original file
     */
    public string $filename;

    /**
     * File object for an ExtractedPdf.
     */
    protected string $pdfBytes;

    /**
     * Initializes a new instance of the ExtractedPdf class.
     *
     * @param string $pdfBytes a binary string representation of the PDF
     * @param string $filename name of the original file
     *
     * @throws MindeeUnhandledException throws if PDF operations aren't supported
     */
    public function __construct(string $pdfBytes, string $filename)
    {
        DependencyChecker::isImageMagickAvailable();
        DependencyChecker::isGhostscriptAvailable();
        $this->pdfBytes = $pdfBytes;
        $this->filename = $filename;
    }

    /**
     * Wrapper for pdf GetPageCount().
     *
     * @return int the number of pages in the file
     *
     * @throws MindeePDFException throws if FPDI is unable to process the file
     */
    public function getPageCount(): int
    {
        try {
            $pdfHandle = new Fpdi();

            $tempFilename = tempnam(sys_get_temp_dir(), 'extracted_pdf_');
            file_put_contents($tempFilename, $this->pdfBytes);

            return $pdfHandle->setSourceFile($tempFilename);
        } catch (PdfParserException $e) {
            throw new MindeePDFException(
                "Couldn't open PDF file.",
                ErrorCode::PDF_CANT_CREATE,
                $e
            );
        }
    }

    /**
     * Write the PDF to a file.
     *
     * @param string $outputPath the output directory (must exist)
     */
    public function writeToFile(string $outputPath): void
    {
        $pdfPath = $outputPath.DIRECTORY_SEPARATOR.$this->filename;
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

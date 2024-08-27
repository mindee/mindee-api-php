<?php

namespace Mindee\Extraction;

use Mindee\Error\MindeePDFException;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\PdfParserException;

/**
 * An extracted sub-Pdf.
 */
class ExtractedPdf
{
    /**
     * File handle for an ExtractedPdf.
     *
     * @var FPDI
     */
    private FPDI $pdfHandle;

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
     */
    public function __construct(string $pdfBytes, string $filename)
    {
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
            $this->pdfHandle = new FPDI();

            $tempFilename = tempnam(sys_get_temp_dir(), 'extracted_pdf_');
            file_put_contents($tempFilename, $this->pdfBytes);
            return $this->pdfHandle->setSourceFile($tempFilename);
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
     * @return array An array containing the PDF bytes and filename.
     */
    public function asInputSource(): array
    {
        return [
            'bytes' => $this->pdfBytes,
            'filename' => $this->filename
        ];
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

    /**
     * Returns the FPDI wrapper for the file.
     *
     * @return FPDI A custom FPDI PDF wrapper handle.
     * @throws MindeePDFException Throws if a FPDI handle can't be generated for the given object.
     */
    public function getPdfHandle(): FPDI
    {
        if (!isset($this->pdfBytes)) {
            throw new MindeePDFException("Extracted pdf contains no bytes!");
        }
        try {
            $this->pdfHandle = new FPDI();

            $tempFilename = tempnam(sys_get_temp_dir(), 'extracted_pdf_');
            file_put_contents($tempFilename, $this->pdfBytes);
            $this->pdfHandle->setSourceFile($tempFilename);
        } catch (PdfParserException $e) {
            throw new MindeePDFException("Invalid data in extracted pdf bytes.");
        }
        return $this->pdfHandle;
    }
}

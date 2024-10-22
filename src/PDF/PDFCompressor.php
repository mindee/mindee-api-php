<?php

namespace Mindee\PDF;

use CURLFile;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeePDFException;
use setasign\Fpdi\Fpdi;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Parser;

/**
 * PDF utility class.
 */
class PDFCompressor
{
    /**
     * Compresses each page of a provided PDF stream. Skips if force_source_text isn't set and source text is detected.
     *
     * @param mixed   $input                      Path to the PDF file.
     * @param integer $quality                    Compression quality (70-100 for most JPG images in the test dataset).
     * @param boolean $forceSourceTextCompression If true, attempts to re-write detected text.
     * @param boolean $disableSourceText          If true, doesn't re-apply source text to the original PDF.
     * @return CURLFile Compressed PDF file.
     * @throws MindeePDFException Throws if the operation fails at any step.
     */
    public static function compress(
        $input,
        int $quality = 85,
        bool $forceSourceTextCompression = false,
        bool $disableSourceText = true
    ): \CURLFile {
        try {
            $pdfPath = PDFUtils::extractFilePath($input);
            $parser = new Parser();
            $pdf = $parser->parseFile($pdfPath);

            if (PDFUtils::hasSourceText($pdfPath)) {
                if ($forceSourceTextCompression) {
                    if ($disableSourceText) {
                        echo "\033[33m[WARNING] Re-writing PDF source-text is an EXPERIMENTAL feature.\033[0m\n";
                    } else {
                        echo "\033[33m[WARNING] Source-file contains text, but disable_source_text flag is ignored. " .
                            "Resulting file will not contain any embedded text.\033[0m\n";
                    }
                } else {
                    echo "\033[33m[WARNING] Source-text detected in input PDF. Aborting operation.\033[0m\n";
                    return PDFUtils::toCURLFile($pdfPath);
                }
            }

            $fpdi = new Fpdi();
            $pageCount = $fpdi->setSourceFile($pdfPath);
            $outPdf = self::processPdfPages($pdfPath, $quality, $pageCount);

            $outputPath = self::createOutputPdf($outPdf, $disableSourceText, $pdf);
            return PDFUtils::toCURLFile($outputPath);
        } catch (\Exception $e) {
            throw new MindeePDFException(
                "Couldn't compress PDF.",
                ErrorCode::FILE_OPERATION_ABORTED,
                $e
            );
        }
    }

    /**
     * Processes all pages in the PDF.
     *
     * @param string  $sourcePdfPath Path to the source PDF file.
     * @param integer $quality       Compression quality.
     * @param integer $pageCount     Total number of pages.
     * @return FPDI The FPDI object with processed pages.
     */
    private static function processPdfPages(string $sourcePdfPath, int $quality, int $pageCount): FPDI
    {
        $outputPdf = new FPDI();

        for ($i = 1; $i <= $pageCount; $i++) {
            $tempJpegFile = self::processPdfPage($sourcePdfPath, $i, $quality);
            list($width, $height) = getimagesize($tempJpegFile);
            $outputPdf->AddPage('', [$width, $height]);
            $outputPdf->Image($tempJpegFile, 0, 0, $width, $height);
            unlink($tempJpegFile);
        }
        return $outputPdf;
    }

    /**
     * Creates the final output PDF, optionally injecting text from the original PDF.
     *
     * @param FPDI     $processedPdf      The FPDI object containing the processed pages.
     * @param boolean  $disableSourceText Whether to disable source text injection.
     * @param Document $originalPdf       The original PDF document (used for text injection).
     * @return string Path to the output PDF file
     * @throws MindeePDFException If there's an error creating the output PDF.
     */
    private static function createOutputPdf(FPDI $processedPdf, bool $disableSourceText, Document $originalPdf): string
    {
        try {
            if (!$disableSourceText) {
                self::injectText($originalPdf, [], $processedPdf);
            }

            $outputPath = tempnam(sys_get_temp_dir(), 'compressed_pdf_') . '.pdf';
            $processedPdf->Output($outputPath, 'F');

            return $outputPath;
        } catch (\Exception $e) {
            throw new MindeePDFException(
                "Couldn't create output PDF.",
                ErrorCode::PDF_CANT_CREATE,
                $e
            );
        }
    }


    /**
     * Extracts text from a source text PDF, and injects it into a newly-created one.
     *
     * @param Document $originalPdf Original PDF document.
     * @param array    $pages       Array of pages containing the rasterized version of the initial pages.
     * @param FPDI     $outputPdf   The output PDF object.
     * @return void
     * @throws MindeePDFException Throws if the text can't be injected.
     */
    private static function injectText(Document $originalPdf, array $pages, FPDI $outputPdf): void
    {
        try {
            foreach ($originalPdf->getPages() as $index => $page) {
                if (!isset($pages[$index])) {
                    break;
                }

                $outputPdf->AddPage();

                // Extract text elements with their properties
                $textElements = PDFUtils::extractTextElements($page);

                foreach ($textElements as $element) {
                    PDFUtils::addTextElement($outputPdf, $element);
                }
            }
        } catch (\Exception $e) {
            throw new MindeePDFException(
                "Couldn't inject text into the new file.",
                ErrorCode::PDF_CANT_EDIT,
                $e
            );
        }
    }

    /**
     * Processes a single PDF page, rasterizing it to a JPEG image.
     *
     * @param string  $sourcePdfPath Path to the source PDF file.
     * @param integer $pageIndex     The index of the page to process.
     * @param integer $imageQuality  The quality setting for JPEG compression.
     * @return string Path to the temporary JPEG file.
     * @throws MindeePDFException If there's an error processing the page.
     */
    private static function processPdfPage(string $sourcePdfPath, int $pageIndex, int $imageQuality): string
    {
        try {
            $singlePagePdf = new FPDI();
            $singlePagePdf->setSourceFile($sourcePdfPath);
            $tplId = $singlePagePdf->importPage($pageIndex);
            $size = $singlePagePdf->getTemplateSize($tplId);

            $singlePagePdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $singlePagePdf->useTemplate($tplId);

            $tempPdfFile = tempnam(sys_get_temp_dir(), 'pdf_page_') . '.pdf';
            $singlePagePdf->Output($tempPdfFile, 'F');

            $imagick = new \Imagick();
            $imagick->readImage($tempPdfFile);
            $imagick->setImageFormat('jpg');
            $imagick->setImageCompressionQuality($imageQuality);

            $tempJpegFile = tempnam(sys_get_temp_dir(), 'pdf_page_') . '.jpg';
            $imagick->writeImage($tempJpegFile);

            unlink($tempPdfFile);

            return $tempJpegFile;
        } catch (\Exception $e) {
            throw new MindeePDFException(
                "Couldn't process PDF page $pageIndex.",
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        }
    }
}

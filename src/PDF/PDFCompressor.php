<?php

namespace Mindee\PDF;

use CURLFile;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeePDFException;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use Smalot\PdfParser\Config;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Page;
use Smalot\PdfParser\Parser;

/**
 * PDF compression class.
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
            $config = new Config();
            $config->setDataTmFontInfoHasToBeIncluded(true);
            $parser = new Parser([], $config);
            $pdf = $parser->parseFile($pdfPath);

            if (strlen($pdf->getText()) > 0) {
                if ($forceSourceTextCompression) {
                    if (!$disableSourceText) {
                        echo "\033[33m[WARNING] Re-writing PDF source-text is an EXPERIMENTAL feature.\033[0m\n";
                    } else {
                        echo "\033[33m[WARNING] Source-file contains text, but disable_source_text flag is set to" .
                            " false. Resulting file will not contain any embedded text.\033[0m\n";
                    }
                } else {
                    echo "\033[33m[WARNING] Source-text detected in input PDF. Aborting operation.\033[0m\n";
                    $outputPath = tempnam(sys_get_temp_dir(), 'compressed_pdf_') . '.pdf';
                    copy($pdfPath, $outputPath);
                    return PDFUtils::toCURLFile($outputPath);
                }
            }

            try {
                $fpdi = new CustomFPDI();
                $pageCount = $fpdi->setSourceFile($pdfPath);
            } catch (CrossReferenceException $e) {
                echo "\033[33m[WARNING] PDF Format is not directly supported. Output PDF will be rasterized and" .
                    " source text won't be available.\033[0m\n";
                $pdfPath = PDFUtils::downgradePdfVersion($pdfPath);
                $fpdi = new CustomFPDI();
                $pdf = $parser->parseFile($pdfPath);
                $pageCount = $fpdi->setSourceFile($pdfPath);
            }

            $outPdf = new CustomFPDI();
            for ($i = 1; $i <= $pageCount; $i++) {
                list($tempJpegFile, $orientation) = static::processPdfPage($pdfPath, $i, $quality);
                list($width, $height) = getimagesize($tempJpegFile);
                $outPdf->AddPage($orientation, [$width, $height]);
                $outPdf->Image($tempJpegFile, 0, 0, $width, $height);
                unlink($tempJpegFile);

                if (!$disableSourceText) {
                    static::injectTextForPage($pdf->getPages()[$i - 1], $outPdf);
                }
            }

            $outputPath = tempnam(sys_get_temp_dir(), 'compressed_pdf_') . '.pdf';
            $outPdf->Output('F', $outputPath);

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
     * @param Page       $inputPage Input page.
     * @param CustomFPDI $outputPdf Output PDF handle.
     * @return void
     * @throws MindeePDFException Throws if text can't be inserted into the page.
     */
    private static function injectTextForPage(Page $inputPage, CustomFPDI $outputPdf): void
    {
        try {
            $textElements = PDFUtils::extractTextElements($inputPage);
            foreach ($textElements as $element) {
                PDFUtils::addTextElement($outputPdf, $element);
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
            list($tempJpegFile, $orientation) = static::processPdfPage($sourcePdfPath, $i, $quality);
            list($width, $height) = getimagesize($tempJpegFile);
            $outputPdf->AddPage($orientation, [$width, $height]);
            $outputPdf->Image($tempJpegFile, 0, 0, $width, $height);
            unlink($tempJpegFile);
        }
        return $outputPdf;
    }

    /**
     * Creates the final output PDF, optionally injecting text from the original PDF.
     *
     * @param CustomFPDI $processedPdf      The FPDI object containing the processed pages.
     * @param boolean    $disableSourceText Whether to disable source text injection.
     * @param Document   $originalPdf       The original PDF document (used for text injection).
     * @return string Path to the output PDF file
     * @throws MindeePDFException If there's an error creating the output PDF.
     */
    private static function createOutputPdf(
        CustomFPDI $processedPdf,
        bool $disableSourceText,
        Document $originalPdf
    ): string {
        try {
            if (!$disableSourceText) {
                static::injectText($originalPdf, $processedPdf);
            }

            $outputPath = tempnam(sys_get_temp_dir(), 'compressed_pdf_') . '.pdf';
            $processedPdf->Output('F', $outputPath);

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
     * @param Document   $inputPdf  Input PDF document.
     * @param CustomFPDI $outputPdf The output PDF object.
     * @return void
     * @throws MindeePDFException Throws if the text can't be injected.
     */
    private static function injectText(Document $inputPdf, CustomFPDI $outputPdf): void
    {
        try {
            $pages = $inputPdf->getPages();
            $pageCount = count($pages);

            for ($i = 1; $i <= $pageCount; $i++) {
                $textElements = PDFUtils::extractTextElements($pages[$i - 1]);

                if (!empty($textElements)) {
                    $tplIdx = $outputPdf->importPage($i);
                    $size = $outputPdf->getTemplateSize($tplIdx);
                    $outputPdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $outputPdf->useTemplate($tplIdx);
                    foreach ($textElements as $element) {
                        PDFUtils::addTextElement($outputPdf, $element);
                    }
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
     * @return array Path to the temporary JPEG file and orientation of the page.
     * @throws MindeePDFException If there's an error processing the page.
     */
    private static function processPdfPage(string $sourcePdfPath, int $pageIndex, int $imageQuality): array
    {
        try {
            $singlePagePdf = new FPDI();
            $singlePagePdf->setSourceFile($sourcePdfPath);
            $tplId = $singlePagePdf->importPage($pageIndex);
            $size = $singlePagePdf->getTemplateSize($tplId);

            $singlePagePdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $singlePagePdf->useTemplate($tplId);

            $tempPdfFile = tempnam(sys_get_temp_dir(), 'pdf_page_') . '.pdf';
            $singlePagePdf->Output('F', $tempPdfFile);

            $imagick = new \Imagick();
            $imagick->readImage($tempPdfFile);
            $imagick->setImageFormat('jpg');
            $imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
            $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
            $imagick->setImageCompressionQuality($imageQuality);

            $tempJpegFile = tempnam(sys_get_temp_dir(), 'pdf_page_') . '.jpg';
            $imagick->writeImage($tempJpegFile);

            unlink($tempPdfFile);

            return [$tempJpegFile, $size['orientation']];
        } catch (\Exception $e) {
            throw new MindeePDFException(
                "Couldn't process PDF page $pageIndex.",
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        }
    }
}

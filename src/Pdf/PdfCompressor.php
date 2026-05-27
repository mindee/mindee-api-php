<?php

declare(strict_types=1);

namespace Mindee\Pdf;

use Mindee\Dependency\DependencyChecker;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeePdfException;
use Mindee\Error\MindeeUnhandledException;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use Smalot\PdfParser\Config;
use Smalot\PdfParser\Page;
use Smalot\PdfParser\Parser;
use CURLFile;
use Exception;
use Imagick;
use SplFileObject;

/**
 * PDF compression class.
 */
class PdfCompressor
{
    /**
     * Compresses each page of a provided PDF stream. Skips if force_source_text isn't set and source text is detected.
     *
     * @param resource|string|SplFileObject|CURLFile $input Path to the PDF file.
     * @param integer $quality Compression quality (70-100 for most JPG images in the test dataset).
     * @param boolean $forceSourceTextCompression If true, attempts to re-write detected text.
     * @param boolean $disableSourceText If true, doesn't re-apply source text to the original PDF.
     * @throws MindeePdfException Throws if the operation fails at any step.
     *                            //phpcs:disable
     * @throws MindeeUnhandledException Throws if one of the dependencies isn't installed.
     */
    public static function compress(
        mixed $input,
        int $quality = 85,
        bool $forceSourceTextCompression = false,
        bool $disableSourceText = true
    ): CURLFile {
        //phpcs: enable
        DependencyChecker::isImageMagickAvailable();
        DependencyChecker::isGhostscriptAvailable();
        try {
            $pdfPath = PdfUtils::extractFilePath($input);
            $initialFileSize = filesize($pdfPath);
            $config = new Config();
            $config->setDataTmFontInfoHasToBeIncluded(true);
            $parser = new Parser([], $config);
            $pdf = $parser->parseFile($pdfPath);

            if ($pdf->getText() !== '') {
                if ($forceSourceTextCompression) {
                    if (!$disableSourceText) {
                        error_log("[WARNING] Re-writing PDF source-text is an EXPERIMENTAL feature.");
                    } else {
                        error_log("[WARNING] Source file '$pdfPath' contains text, but disable_source_text flag"
                            . " is set to false. Resulting file will not contain any embedded text.");
                    }
                } else {
                    error_log("[WARNING] Source-text detected in input PDF '$pdfPath'. Aborting operation.");
                    $outputPath = tempnam(sys_get_temp_dir(), 'compressed_pdf_') . '.pdf';
                    copy($pdfPath, $outputPath);
                    return PdfUtils::toCURLFile($outputPath);
                }
            }

            try {
                $fpdi = new CustomFpdi();
                $pageCount = $fpdi->setSourceFile($pdfPath);
            } catch (CrossReferenceException) {
                error_log("[WARNING] PDF format for '$pdfPath' is not directly supported."
                    . " Output PDF will be rasterized and source text won't be available.");
                $pdfPath = PdfUtils::downgradePdfVersion($pdfPath);
                $fpdi = new CustomFpdi();
                $pdf = $parser->parseFile($pdfPath);
                $pageCount = $fpdi->setSourceFile($pdfPath);
            }

            $outPdf = new CustomFpdi();
            for ($i = 1; $i <= $pageCount; $i++) {
                [$tempJpegFile, $orientation] = static::processPdfPage($pdfPath, $i, $quality);
                [$width, $height] = getimagesize($tempJpegFile);
                $outPdf->AddPage($orientation, [$width, $height]);
                $outPdf->Image($tempJpegFile, 0, 0, $width, $height);
                unlink($tempJpegFile);

                if (!$disableSourceText) {
                    static::injectTextForPage($pdf->getPages()[$i - 1], $outPdf);
                }
            }

            $outputPath = tempnam(sys_get_temp_dir(), 'compressed_pdf_') . '.pdf';
            $outPdf->Output('F', $outputPath);
            $finalPdfSize = filesize($outputPath);

            if ($initialFileSize < $finalPdfSize) {
                error_log("[WARNING] Compressed PDF for '$pdfPath' would be larger than input."
                    . " Aborting operation.");
                return PdfUtils::toCURLFile(PdfUtils::extractFilePath($input));
            }
            return PdfUtils::toCURLFile($outputPath);
        } catch (Exception $e) {
            throw new MindeePdfException(
                "Couldn't compress PDF.",
                ErrorCode::FILE_OPERATION_ABORTED,
                $e
            );
        }
    }

    /**
     * @param Page $inputPage Input page.
     * @param CustomFpdi $outputPdf Output PDF handle.
     * @throws MindeePdfException Throws if text can't be inserted into the page.
     */
    protected static function injectTextForPage(Page $inputPage, CustomFpdi $outputPdf): void
    {
        try {
            $textElements = PdfUtils::extractTextElements($inputPage);
            foreach ($textElements as $element) {
                PdfUtils::addTextElement($outputPdf, $element);
            }
        } catch (Exception $e) {
            throw new MindeePdfException(
                "Couldn't inject text into the new file.",
                ErrorCode::PDF_CANT_EDIT,
                $e
            );
        }
    }

    /**
     * Processes a single PDF page, rasterizing it to a JPEG image.
     *
     * @param string $sourcePdfPath Path to the source PDF file.
     * @param integer $pageIndex The index of the page to process.
     * @param integer $imageQuality The quality setting for JPEG compression.
     * @return array{0: string, 1: string} Path to the temporary JPEG file and orientation of the page.
     * @throws MindeePdfException If there's an error processing the page.
     */
    protected static function processPdfPage(string $sourcePdfPath, int $pageIndex, int $imageQuality): array
    {
        try {
            $singlePagePdf = new Fpdi();
            $singlePagePdf->setSourceFile($sourcePdfPath);
            $tplId = $singlePagePdf->importPage($pageIndex);
            $size = $singlePagePdf->getTemplateSize($tplId);

            $singlePagePdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $singlePagePdf->useTemplate($tplId);

            $tempPdfFile = tempnam(sys_get_temp_dir(), 'pdf_page_') . '.pdf';
            $singlePagePdf->Output('F', $tempPdfFile);

            $imagick = new Imagick();
            $imagick->readImage($tempPdfFile);
            $imagick->setImageFormat('jpg');
            $imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
            $imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
            $imagick->setImageCompressionQuality($imageQuality);

            $tempJpegFile = tempnam(sys_get_temp_dir(), 'pdf_page_') . '.jpg';
            $imagick->writeImage($tempJpegFile);

            unlink($tempPdfFile);

            return [$tempJpegFile, $size['orientation']];
        } catch (Exception $e) {
            throw new MindeePdfException(
                "Couldn't process PDF page $pageIndex.",
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        }
    }
}

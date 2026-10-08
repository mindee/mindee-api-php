<?php

declare(strict_types=1);

namespace Mindee\Pdf;

use BernardLedit\Pdf\PdfDocument;
use CURLFile;
use Exception;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeImageException;
use Mindee\Error\MindeePdfException;
use SplFileObject;
use TypeError;

use function is_resource;
use function is_string;

/**
 * PDF utility class.
 */
class PdfUtils
{
    /**
     * Checks whether the file has source text.
     * @param string $pdfData Raw byte string of the PDF file.
     * @return boolean Returns true if the PDF has source text, false otherwise.
     */
    public static function hasSourceText(string $pdfData): bool
    {
        try {
            $pdf = new PdfDocument($pdfData);
            return $pdf->hasText();
        } catch (Exception $e) {
            throw new MindeePdfException(
                "PDF couldn't be opened.",
                ErrorCode::PDF_CANT_CREATE,
                $e
            );
        }
    }

    /**
     * Extracts text elements with their properties from all pages in a PDF.
     *
     * @param string $pdfPath Path to the PDF file.
     * @return array<integer, string> A page-indexed array of text elements.
     *                                Each text element includes text content, position, font, size, and color.
     * @throws MindeePdfException Throws if the PDF can't be parsed or text elements can't be extracted.
     */
    public static function extractPagesTextElements(string $pdfPath): array
    {
        try {
            $pdf = new PdfDocument(file_get_contents($pdfPath));
            $allPagesTextElements = [];
            $pageCount = $pdf->pageCount();
            for ($i = 0; $i < $pageCount; $i++) {
                $page = $pdf->getPage($i);
                $allPagesTextElements[$i] = $page->text();
            }

            return $allPagesTextElements;
        } catch (Exception $e) {
            throw new MindeePdfException(
                'Failed to parse PDF or extract text elements: ',
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        }
    }

    /**
     * Linearly interpolates between two values.
     *
     * @param float $a The start value.
     * @param float $b The end value.
     * @param float $t The interpolation factor (typically between 0.0 and 1.0).
     * @return float The interpolated value.
     */
    public static function lerp(float $a, float $b, float $t): float
    {
        return $a + ($b - $a) * $t;
    }
}

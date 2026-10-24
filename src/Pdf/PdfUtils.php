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
}

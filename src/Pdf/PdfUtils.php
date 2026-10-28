<?php

declare(strict_types=1);

namespace Mindee\Pdf;

use BernardLedit\Pdf\PdfDocument;
use Exception;
use Mindee\Dependency\DependencyChecker;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeePdfException;

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
    public static function hasSourceTextFromBytes(string $pdfData): bool
    {
        DependencyChecker::requireBernardLedit();
        try {
            return self::withPdf($pdfData, static fn(PdfDocument $pdf): bool => $pdf->hasText());
        } catch (Exception $e) {
            throw new MindeePdfException("PDF couldn't be opened.", ErrorCode::PDF_CANT_PROCESS, $e);
        }
    }

    /** @deprecated since 3.4.0: use hasSourceTextFromBytes(). */
    public static function hasSourceText(string $pdfPath): bool
    {
        $bytes = @file_get_contents($pdfPath);
        if (false === $bytes) {
            throw new MindeePdfException("Couldn't read $pdfPath.", ErrorCode::FILE_OPERATION_ERROR);
        }
        return self::hasSourceTextFromBytes($bytes);
    }

    /**
     * Closes a document, logging instead of throwing: never mask the error being handled.
     */
    public static function closeQuietly(?PdfDocument $pdf): void
    {
        if ($pdf === null) {
            return;
        }
        try {
            $pdf->close();
        } catch (Exception $e) {
            error_log("Couldn't close PDF document: " . $e->getMessage());
        }
    }

    /**
     * Opens a PDF, runs $fn on it, and always closes it.
     *
     * @template T
     * @param callable(PdfDocument): T $fn
     * @return T
     */
    public static function withPdf(string $bytes, callable $fn): mixed
    {
        $pdf = new PdfDocument($bytes);
        try {
            return $fn($pdf);
        } finally {
            self::closeQuietly($pdf);
        }
    }
}

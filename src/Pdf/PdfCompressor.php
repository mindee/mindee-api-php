<?php

declare(strict_types=1);

namespace Mindee\Pdf;

use BernardLedit\Pdf\PdfDocument;
use Mindee\Dependency\DependencyChecker;
use Exception;

use function BernardLedit\Image\compress as compressImage;
use function strlen;

/**
 * PDF compression class.
 */
class PdfCompressor
{
    private const MIN_QUALITY = 1;

    /**
     * Compresses each page of a provided PDF stream. Skips if force_source_text isn't set and source text is detected.
     *
     * @param string $pdfData Data containing a PDF byte string.
     * @param int $imageQuality Quality of the compressed images.
     * @param boolean $forceSourceTextCompression If true, attempts to re-write detected text.
     * @param boolean $disableSourceText If true, doesn't re-apply source text to the original PDF.
     * @throws Exception Throws if an error occurs during the compression process.
     */
    public static function compress(
        string $pdfData,
        int    $imageQuality = 85,
        bool   $forceSourceTextCompression = false,
        bool   $disableSourceText = true
    ): string {
        DependencyChecker::requireBernardLedit();

        if (PdfUtils::hasSourceText($pdfData)) {
            if (!$forceSourceTextCompression) {
                error_log('Found text inside of the provided PDF file. Compression aborted since disableSourceText is true.');
                return $pdfData;
            }
            error_log($disableSourceText
                ? 'Source file contains text, but disableSourceText is set. Output will contain no embedded text.'
                : 'Re-writing PDF source-text is an EXPERIMENTAL feature.');
        }

        $extractedText = null;
        if (!$disableSourceText) {
            $src = new PdfDocument($pdfData);
            $extractedText = [];
            for ($i = 0; $i < $src->pageCount(); $i++) {
                $extractedText[] = $src->getPage($i)->chars();
            }
            $src->close();
        }

        $pages = self::compressPdfPages($pdfData, $imageQuality);
        if ($pages === null) {
            error_log('Could not compress PDF to a smaller size. Returning original PDF.');
            return $pdfData;
        }

        $out = PdfDocument::create();
        $out->appendMultipleJpegPages(array_column($pages, 0));
        if ($extractedText !== null) {
            foreach ($extractedText as $i => $chars) {
                $out->addText($i, $chars);
            }
        }
        $bytes = $out->save();
        $out->close();
        return $bytes;
    }

    /**
     * Compresses PDF pages with a given quality.
     *
     * @param string $pdfData The PDF data to compress.
     * @param int $imageQuality The image quality.
     * @return list<array{0:string,1:int,2:int}>|null
     * @throws Exception Throws if compression failed.
     */
    private static function compressPdfPages(string $pdfData, int $imageQuality): ?array
    {
        $originalSize = strlen($pdfData);
        $q = $imageQuality;
        while ($q >= self::MIN_QUALITY) {
            $pages = self::compressPagesWithQuality($pdfData, $q);
            $total = array_sum(array_map(static fn(array $p): int => strlen($p[0]), $pages));
            if (self::isCompressionSuccessful($total, $originalSize, $imageQuality)) {
                return $pages;
            }
            $q -= (int) round(PdfUtils::lerp(1, 10, $q / 100));   // Python: round(lerp(1,10,q/100)), NOT a flat -10
        }
        return null;
    }

    /**
     * Compresses PDF pages with a given quality.
     *
     * @param string $pdfData The PDF data to compress.
     * @param int $q The image quality.
     * @return list<array{0:string,1:int,2:int}>
     * @throws Exception Throws if compression failed.
     */
    private static function compressPagesWithQuality(string $pdfData, int $q): array
    {
        $doc = new PdfDocument($pdfData);
        $pages = [];
        for ($i = 0; $i < $doc->pageCount(); $i++) {
            $raster = $doc->rasterizePage($i, $q);
            $pages[] = compressImage($raster, $q);
        }
        $doc->close();
        return $pages;
    }

    /**
     * Checks whether the compression actually compressed the PDF size.
     * @param int $total Total size of compressed pages.
     * @param int $original Original size of the PDF.
     * @param int $q Image quality.
     * @return boolean True if the compression went successfully.
     */
    private static function isCompressionSuccessful(int $total, int $original, int $q): bool
    {
        $overhead = PdfUtils::lerp(0.54, 0.18, $q / 100);
        return $total + $total * $overhead < $original;
    }
}

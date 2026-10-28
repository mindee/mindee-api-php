<?php

declare(strict_types=1);

namespace Dependencies;

use Mindee\Dependency\DependencyChecker;
use Mindee\Error\MindeeUnhandledException;
use Mindee\Image\ExtractedImage;
use Mindee\Input\PageOptions;
use Mindee\Input\PathInput;
use Mindee\Pdf\ExtractedPdf;
use Mindee\Pdf\PdfExtractor;
use Mindee\V1\Image\ImageExtractor;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

use const Mindee\Input\KEEP_ONLY;

class DependencyCheckerNoExtensionTestPdf extends TestCase
{
    protected function setUp(): void
    {
        if (DependencyChecker::isBernardLeditAvailable()) {
            self::fail('This suite must run without the bernard_ledit extension.');
        }
    }

    private static function pdf(): PathInput
    {
        return new PathInput(TestingUtilities::getFileTypesDir() . '/pdf/multipage.pdf');
    }

    public function testInputSourceStillLoadsWithoutPageCount(): void
    {
        // Assumes the LocalInputSource constructor still swallows MindeeUnhandledException.
        self::assertNull(self::pdf()->pageCount);
    }

    public function testImageExtractorRequiresExtension(): void
    {
        $this->expectException(MindeeUnhandledException::class);
        new ImageExtractor(self::pdf());
    }

    public function testPdfExtractorRequiresExtension(): void
    {
        $this->expectException(MindeeUnhandledException::class);
        new PdfExtractor(self::pdf());
    }

    public function testExtractedImageRequiresExtension(): void
    {
        $this->expectException(MindeeUnhandledException::class);
        new ExtractedImage('', 'dummy.jpg', 'jpg', 0, 0);
    }

    public function testExtractedPdfRequiresExtension(): void
    {
        $this->expectException(MindeeUnhandledException::class);
        new ExtractedPdf('', 'dummy.pdf');
    }

    public function testApplyPageOptionsRequiresExtension(): void
    {
        $this->expectException(MindeeUnhandledException::class);
        self::pdf()->applyPageOptions(new PageOptions([0], KEEP_ONLY, 1));
    }

    public function testCompressRequiresExtension(): void
    {
        $this->expectException(MindeeUnhandledException::class);
        self::pdf()->compress();
    }

    public function testHasSourceTextOnImageIsFalse(): void
    {
        $image = new PathInput(TestingUtilities::getFileTypesDir() . '/receipt.jpg');
        self::assertFalse($image->hasSourceText());
    }
}

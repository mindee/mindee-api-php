<?php

declare(strict_types=1);

namespace Dependencies;

use Mindee\Error\MindeeUnhandledException;
use Mindee\Image\ExtractedImage;
use Mindee\Input\PathInput;
use Mindee\Pdf\ExtractedPdf;
use Mindee\Pdf\PdfExtractor;
use Mindee\V1\Image\ImageExtractor;
use PHPUnit\Framework\TestCase;
use TestingUtilities;


class DependencyCheckerNoExtendedTestPdf extends TestCase
{
    public function testNoImageExtractor(): void
    {
        $this->expectException(MindeeUnhandledException::class);
        $inputObj = new PathInput(TestingUtilities::getFileTypesDir() . "/pdf/blank.pdf");
        new ImageExtractor($inputObj);
    }
    public function testNoPdfExtractor(): void
    {
        $this->expectException(MindeeUnhandledException::class);
        $inputObj = new PathInput(TestingUtilities::getFileTypesDir() . "/pdf/blank.pdf");
        new PdfExtractor($inputObj);
    }
    public function testNoExtractedImage(): void
    {
        $this->expectException(MindeeUnhandledException::class);
        $inputImage = "";
        $filename = "dummy";
        $saveFormat = "pdf";
        new ExtractedImage($inputImage, $filename, $saveFormat, 0, 0);
    }
    public function testNoExtractedPdf(): void
    {
        $this->expectException(MindeeUnhandledException::class);
        $inputImage = "";
        $filename = "dummy";
        new ExtractedPdf($inputImage, $filename);
    }
}

<?php

declare(strict_types=1);

namespace Dependencies;

use Mindee\Error\MindeeUnhandledException;
use Mindee\Extraction\ExtractedImage;
use Mindee\Extraction\ExtractedPdf;
use Mindee\V1\Image\ImageExtractor;
use Mindee\Extraction\PdfExtractor;
use Mindee\Input\PathInput;
use PHPUnit\Framework\TestCase;
use TestingUtilities;
use Imagick;
use stdClass;

require_once(__DIR__ . "/../TestingUtilities.php");

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
        if (!class_exists('Imagick')) {
            class_alias(stdClass::class, 'Imagick');
        }
        $inputImage = new Imagick();
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

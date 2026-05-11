<?php

namespace Dependencies;

use Mindee\Error\MindeeUnhandledException;
use Mindee\Extraction\ExtractedImage;
use Mindee\Extraction\ExtractedPDF;
use Mindee\V1\Image\ImageExtractor;
use Mindee\Extraction\PDFExtractor;
use Mindee\Input\PathInput;
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . "/../TestingUtilities.php");

class DependencyCheckerNoExtendedTestPDF extends TestCase
{
    public function testNoImageExtractor()
    {
        $this->expectException(MindeeUnhandledException::class);
        $inputObj = new PathInput(\TestingUtilities::getFileTypesDir() . "/pdf/blank.pdf");
        new ImageExtractor($inputObj);
    }
    public function testNoPDFExtractor()
    {
        $this->expectException(MindeeUnhandledException::class);
        $inputObj = new PathInput(\TestingUtilities::getFileTypesDir() . "/pdf/blank.pdf");
        new PDFExtractor($inputObj);
    }
    public function testNoExtractedImage()
    {
        $this->expectException(MindeeUnhandledException::class);
        $inputImage = "";
        $filename = "dummy";
        $saveFormat = "pdf";
        new ExtractedImage($inputImage, $filename, $saveFormat, 0, 0);
    }
    public function testNoExtractedPDF()
    {
        $this->expectException(MindeeUnhandledException::class);
        $inputImage = "";
        $filename = "dummy";
        new ExtractedPDF($inputImage, $filename);
    }
}

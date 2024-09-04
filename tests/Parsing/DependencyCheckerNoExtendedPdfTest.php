<?php

use Mindee\Error\MindeeUnhandledException;
use Mindee\Extraction\ExtractedImage;
use Mindee\Extraction\ExtractedPdf;
use Mindee\Extraction\ImageExtractor;
use Mindee\Extraction\PdfExtractor;
use Mindee\Input\PathInput;
use PHPUnit\Framework\TestCase;

class DependencyCheckerNoExtendedPdfTest extends TestCase
{
    public function testNoImageExtractor()
    {
        $this->expectException(MindeeUnhandledException::class);
        $inputObj = new PathInput((getenv('GITHUB_WORKSPACE') ?: "."
            ) . "/tests/resources/file_types/pdf/blank.pdf");
        new ImageExtractor($inputObj);
    }
    public function testNoPdfExtractor()
    {
        $this->expectException(MindeeUnhandledException::class);
        $inputObj = new PathInput((getenv('GITHUB_WORKSPACE') ?: "."
            ) . "/tests/resources/file_types/pdf/blank.pdf");
        new PdfExtractor($inputObj);
    }
    public function testNoExtractedImage()
    {
        $this->expectException(MindeeUnhandledException::class);
        $inputImage = "";
        $filename = "dummy";
        $saveFormat = "pdf;";
        new ExtractedImage($inputImage, $filename, $saveFormat);
    }
    public function testNoExtractedPdf()
    {
        $this->expectException(MindeeUnhandledException::class);
        $inputImage = "";
        $filename = "dummy";
        new ExtractedPdf($inputImage, $filename);
    }
}

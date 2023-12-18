<?php

namespace Input;

use Mindee\Client;
use Mindee\Error\MindeeSourceException;
use Mindee\Input\InputSource;
use Mindee\Input\PathInput;
use Mindee\Input\URLInputSource;
use PHPUnit\Framework\TestCase;

use const Mindee\Input\KEEP_ONLY;

class LocalInputSourceTest extends TestCase
{
    protected Client $dummyClient;
    protected string $fileTypesDir;

    public function setUp(): void
    {
        $this->dummyClient = new Client("dummy-key");
        putenv('MINDEE_API_KEY' . '=');
        $this->fileTypesDir = (
            getenv('GITHUB_WORKSPACE') ?: "."
            ) . "/tests/resources/file_types/";
    }

//    public function testPDFReconstructOK()
//    {
//        $inputObj = new PathInput($this->fileTypesDir . "pdf/multipage.pdf");
//        $inputObj->processPDF(KEEP_ONLY, 2, [0, 1, 2, 3, 4]); // TODO: processPDF feature
//        $this->assertInstanceOf(resource, $inputObj->readContents()); TODO when pdf handling lib is added
//    }

    public function testPDFReadContents()
    {
        $inputDoc = new PathInput($this->fileTypesDir . "/pdf/multipage.pdf");
        $contents = $inputDoc->readContents(false);
        $this->assertEquals("multipage.pdf", $contents[0]);
    }

//    public function testPDFreconstructNoCut(){ // TODO when pdf handling lib is added
//
//    }

//    public function testPDFCutNPages(){ // TODO when pdf handling lib is added
//
//    }

//    public function testPDFKeep5FirstPages(){ // TODO when pdf handling lib is added
//
//    }

//    public function testPDFKeepInvalidPages(){ // TODO when pdf handling lib is added
//
//    }

//    public function testPDFRemove5LastPages(){ // TODO when pdf handling lib is added
//
//    }

//    public function testPDFRemove5FirstPages(){ // TODO when pdf handling lib is added
//
//    }

//    public function testPDFRemoveInvalidPages(){ // TODO when pdf handling lib is added
//
//    }

//    public function testPDFKeepNoPages(){ // TODO when pdf handling lib is added
//
//    }

//    public function testPDFRemoveAllPages(){ // TODO when pdf handling lib is added
//
//    }

//    public function testPDFInputFromFile(){ // TODO when pdf handling lib is added
//
//    }

//    public function testPDFInputFromBase64(){ // TODO when pdf handling lib is added
//
//    }

//    public function testPDFInputFromBytes(){ // TODO when pdf handling lib is added
//
//    }

    public function testInputFromHTTPShouldThrow()
    {
        $this->expectException(MindeeSourceException::class);
        $this->dummyClient->sourceFromUrl("http://example.com/invoice.pdf");
    }

    public function testInputFromHTTPShouldNotThrow()
    {
        $inputDoc = $this->dummyClient->sourceFromUrl("https://example.com/invoice.pdf");
        $this->assertInstanceOf(URLInputSource::class, $inputDoc);
    }
}

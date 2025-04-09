<?php

namespace Parsing\Common\Ocr;

use Mindee\Parsing\Common\Ocr\Ocr;
use PHPUnit\Framework\TestCase;

class OcrTest extends TestCase
{
    private Ocr $ocrObject;
    protected function setup(): void{
        $json = file_get_contents(
            (
            getenv('GITHUB_WORKSPACE') ?: "."
            ) . "/tests/resources/extras/ocr/complete.json"
        );
        $jsonData = json_decode($json, true);
        $this->ocrObject = new Ocr($jsonData["document"]["ocr"]);
    }
    public function testResponse()
    {
        $expectedText = file_get_contents(
            (
                getenv('GITHUB_WORKSPACE') ?: "."
            ) . "/tests/resources/extras/ocr/ocr.txt"
        );
        $this->assertEquals($expectedText, strval($this->ocrObject));
        $this->assertEquals($expectedText, strval($this->ocrObject->mvisionV1->pages[0]));
    }

    public function testFindOneLineByRegex()
    {
        $regexFilter = '/platinum[\w\s]*\$65\.00/i';
        $matchingLines = $this->ocrObject->findLineByRegex($regexFilter);
        $this->assertNotNull($matchingLines);
        $this->assertEquals(
            "Platinum web hosting package $65.00 $65.00",
            strval($matchingLines[0][0])
        );
    }

    public function testFindMultipleLinesByRegex()
    {
        $regexFilter = '/^.*\$.*$/m';
        $matchingLines = $this->ocrObject->findLineByRegex($regexFilter);
        $this->assertNotNull($matchingLines);
        $this->assertEquals(8, count($matchingLines[0]));
        $this->assertEquals(
            "Amount Due (USD): $2,608.20",
            strval($matchingLines[0][0])
        );
        $this->assertEquals(
            "Amount due (CAD): $2,608.20",
            strval($matchingLines[0][7])
        );
    }
}

<?php

declare(strict_types=1);

namespace V1\Parsing\Common\Ocr;

use Mindee\V1\Parsing\Common\Ocr\Ocr;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class OcrTest extends TestCase
{
    private Ocr $ocrObject;
    protected function setup(): void
    {
        $json = file_get_contents(
            TestingUtilities::getV1DataDir() . "/extras/ocr/complete.json"
        );
        $jsonData = json_decode($json, true);
        $this->ocrObject = new Ocr($jsonData["document"]["ocr"]);
    }
    public function testResponse(): void
    {
        $expectedText = file_get_contents(
            TestingUtilities::getV1DataDir() . "/extras/ocr/ocr.txt"
        );
        self::assertSame($expectedText, (string) ($this->ocrObject));
        self::assertSame($expectedText, (string) ($this->ocrObject->mvisionV1->pages[0]));
    }

    public function testFindOneLineByRegex(): void
    {
        $regexFilter = '/platinum[\w\s]*\$65\.00/i';
        $matchingLines = $this->ocrObject->findLineByRegex($regexFilter);
        self::assertNotNull($matchingLines);
        self::assertSame(
            "Platinum web hosting package $65.00 $65.00",
            (string) ($matchingLines[0][0])
        );
    }

    public function testFindMultipleLinesByRegex(): void
    {
        $regexFilter = '/^.*\$.*$/m';
        $matchingLines = $this->ocrObject->findLineByRegex($regexFilter);
        self::assertNotNull($matchingLines);
        self::assertCount(8, $matchingLines[0]);
        self::assertSame(
            "Amount Due (USD): $2,608.20",
            (string) ($matchingLines[0][0])
        );
        self::assertSame(
            "Amount due (CAD): $2,608.20",
            (string) ($matchingLines[0][7])
        );
    }
}

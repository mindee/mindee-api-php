<?php

namespace Parsing\Common\Ocr;

use Mindee\Parsing\Common\Ocr\Ocr;
use PHPUnit\Framework\TestCase;

class OcrTest extends TestCase
{
    public function testResponse()
    {
        $json = file_get_contents(
            (
                getenv('GITHUB_WORKSPACE') ?: "."
            ) . "/tests/resources/extras/ocr/complete.json"
        );
        $jsonData = json_decode($json, true);
        $expectedText = file_get_contents(
            (
                getenv('GITHUB_WORKSPACE') ?: "."
            ) . "/tests/resources/extras/ocr/ocr.txt"
        );
        $ocr = new Ocr($jsonData["document"]["ocr"]);
        $this->assertEquals($expectedText, strval($ocr));
        $this->assertEquals($expectedText, strval($ocr->mvisionV1->pages[0]));
    }
}

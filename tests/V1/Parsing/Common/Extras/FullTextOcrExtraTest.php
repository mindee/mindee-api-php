<?php

namespace V1\Parsing\Common\Extras;

use Mindee\V1\Product\InternationalId\InternationalIdV2;
use PHPUnit\Framework\TestCase;

class FullTextOCRTest extends TestCase
{
    private $extrasDir;

    protected function setUp(): void
    {
        $this->extrasDir = \TestingUtilities::getV1DataDir() . "/extras";
    }

    private function loadDocument()
    {
        $dummyClient = new \Mindee\V1\Client("dummy-key");
        $localResponse = new \Mindee\Input\LocalResponse($this->extrasDir . '/full_text_ocr/complete.json');
        $response = $dummyClient->loadPrediction(InternationalIdV2::class, $localResponse);
        return $response->document;
    }

    public function testGetsFullTextOCRResult()
    {
        $expectedText = file_get_contents($this->extrasDir . '/full_text_ocr/full_text_ocr.txt');

        $document = $this->loadDocument();
        $fullTextOcr = $document->extras->fullTextOcr;

        $this->assertEquals(trim($expectedText), trim(strval($fullTextOcr)));
    }
}

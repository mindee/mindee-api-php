<?php

namespace V1\Parsing\Common\Extras;

use Mindee\Product\InternationalId\InternationalIdV2;
use PHPUnit\Framework\TestCase;

class FullTextOCRTest extends TestCase
{
    private $extrasDir; // Adjust this path as needed

    protected function setUp(): void
    {
        $this->extrasDir = \TestingUtilities::getV1DataDir() . "/extras";
    }

    private function loadDocument()
    {
        $dummyClient = new \Mindee\Client("dummy-key");
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

    // NOTE: disabled due to the current system used to manage pages of some APIs.
    /*
    private function loadPages()
    {
        $dummyClient = new \Mindee\Client("dummy-key");
        $localResponse = new \Mindee\Input\LocalResponse($this->extrasDir . '/full_text_ocr/complete.json');
        $response = $dummyClient->loadPrediction(InternationalIdV2::class, $localResponse);
        return $response->document->inference->pages;
    }

    public function testGetsFullTextOCRResultForPage()
    {
        $expectedText = file_get_contents($this->extrasDir . '/full_text_ocr/full_text_ocr.txt');
        
        $pages = $this->loadPages();
        $page0Ocr = $pages[0]->extras->fullTextOcr->content;

        $this->assertEquals(implode("\n", explode("\n", $expectedText)), $page0Ocr);
    }
    */
}

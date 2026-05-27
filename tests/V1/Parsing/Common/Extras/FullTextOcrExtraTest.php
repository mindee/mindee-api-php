<?php

declare(strict_types=1);

namespace V1\Parsing\Common\Extras;

use Mindee\Input\LocalResponse;
use Mindee\V1\Client;
use Mindee\V1\Product\InternationalId\InternationalIdV2;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class FullTextOcrExtraTest extends TestCase
{
    private $extrasDir;

    protected function setUp(): void
    {
        $this->extrasDir = TestingUtilities::getV1DataDir() . "/extras";
    }

    private function loadDocument()
    {
        $dummyClient = new Client("dummy-key");
        $localResponse = new LocalResponse($this->extrasDir . '/full_text_ocr/complete.json');
        $response = $dummyClient->loadPrediction(InternationalIdV2::class, $localResponse);
        return $response->document;
    }

    public function testGetsFullTextOcrResult(): void
    {
        $expectedText = file_get_contents($this->extrasDir . '/full_text_ocr/full_text_ocr.txt');

        $document = $this->loadDocument();
        $fullTextOcr = $document->extras->fullTextOcr;

        self::assertSame(trim($expectedText), trim((string) $fullTextOcr));
    }
}

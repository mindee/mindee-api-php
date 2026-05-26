<?php

declare(strict_types=1);

namespace V1\Parsing\Common\Extras;

use Mindee\V1\Client;
use Mindee\V1\ClientOptions\PredictMethodOptions;
use Mindee\V1\ClientOptions\PredictOptions;
use Mindee\V1\Product\InternationalId\InternationalIdV2;
use Mindee\V1\Product\Invoice\InvoiceV4;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

use function count;
use function strlen;

class ExtrasIntegrationFunctional extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client();
    }

    public function testShouldSendCropperExtra(): void
    {
        $sample = $this->client->sourceFromPath(
            TestingUtilities::getV1DataDir() . "/products/invoices/default_sample.jpg"
        );
        $predictOptions = new PredictOptions();
        $predictOptions->setCropper(true);
        $predictMethodOptions = new PredictMethodOptions();
        $predictMethodOptions->setPredictOptions($predictOptions);

        $response = $this->client->parse(InvoiceV4::class, $sample, $predictMethodOptions);

        self::assertNotNull($response->document->inference->pages[0]->extras->cropper);
        self::assertGreaterThan(0, count($response->document->inference->pages[0]->extras->cropper->croppings));
    }

    public function testShouldSendFullTextOCRExtra(): void
    {
        $sample = $this->client->sourceFromPath(
            TestingUtilities::getV1DataDir() . "/products/international_id/default_sample.jpg"
        );
        $predictOptions = new PredictOptions();
        $predictOptions->setFullText(true);
        $predictMethodOptions = new PredictMethodOptions();
        $predictMethodOptions->setPredictOptions($predictOptions);
        $response = $this->client->enqueueAndParse(InternationalIdV2::class, $sample, $predictMethodOptions);

        self::assertNotNull($response->document->extras->fullTextOcr);
        self::assertGreaterThan(10, strlen((string) $response->document->extras->fullTextOcr->content));
    }
}

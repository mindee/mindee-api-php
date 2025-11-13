<?php

namespace V1\Parsing\Common\Extras;

use Mindee\Client;
use Mindee\Input\PredictMethodOptions;
use Mindee\Input\PredictOptions;
use Mindee\Product\InternationalId\InternationalIdV2;
use Mindee\Product\Invoice\InvoiceV4;
use PHPUnit\Framework\TestCase;

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
            \TestingUtilities::getV1DataDir() . "/products/invoices/default_sample.jpg"
        );
        $predictOptions = new PredictOptions();
        $predictOptions->setCropper(true);
        $predictMethodOptions = new PredictMethodOptions();
        $predictMethodOptions->setPredictOptions($predictOptions);

        $response = $this->client->parse(InvoiceV4::class, $sample, $predictMethodOptions);

        $this->assertNotNull($response->document->inference->pages[0]->extras->cropper);
        $this->assertGreaterThan(0, count($response->document->inference->pages[0]->extras->cropper->croppings));
    }

    public function testShouldSendFullTextOcrExtra(): void
    {
        $sample = $this->client->sourceFromPath(
            \TestingUtilities::getV1DataDir() . "/products/international_id/default_sample.jpg"
        );
        $predictOptions = new PredictOptions();
        $predictOptions->setFullText(true);
        $predictMethodOptions = new PredictMethodOptions();
        $predictMethodOptions->setPredictOptions($predictOptions);
        $response = $this->client->enqueueAndParse(InternationalIdV2::class, $sample, $predictMethodOptions);

        $this->assertNotNull($response->document->extras->fullTextOcr);
        $this->assertGreaterThan(10, strlen($response->document->extras->fullTextOcr->content));
    }
}

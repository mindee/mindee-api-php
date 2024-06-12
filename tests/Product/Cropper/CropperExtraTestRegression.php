<?php

namespace Product\Cropper;

require_once(__DIR__."/../RegressionUtilities.php");

use Mindee\Client;
use Mindee\Input\PredictMethodOptions;
use Mindee\Input\PredictOptions;
use Mindee\Parsing\Common\Extras\CropperExtra;
use Mindee\Product\Invoice\InvoiceV4;
use PHPUnit\Framework\TestCase;

class CropperExtraTestRegression extends TestCase
{
    private CropperExtra $cropperExtra;

    protected function setUp(): void
    {
        $cropperReturn = json_decode(file_get_contents((getenv('GITHUB_WORKSPACE') ?: ".") .
            "/tests/resources/extras/cropper/complete.json"), true);
        $this->cropperExtra = new CropperExtra($cropperReturn["document"]["inference"]["pages"][0]["extras"]["cropper"]);
        $this->mindeeClient = new Client();
    }

    public function testRegression()
    {
        $inputSource = $this->mindeeClient->sourceFromPath(
            (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/expense_receipts/default_sample.jpg"
        );
        $predictOptions = new PredictOptions();
        $predictOptions->setCropper(true);
        $predictMethodOptions = new PredictMethodOptions();
        $predictMethodOptions->setPredictOptions($predictOptions);
        $response = $this->mindeeClient->parse(InvoiceV4::class, $inputSource, $predictMethodOptions);
        $this->assertEquals($this->cropperExtra, $response->document->inference->pages[0]->extras->cropper);
    }
}

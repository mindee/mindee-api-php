<?php

namespace Product\Eu\LicensePlate;

require_once(__DIR__."/../../RegressionUtilities.php");
use Mindee\Product\Eu\LicensePlate\LicensePlateV1;
use Mindee\Client;
use PHPUnit\Framework\TestCase;
use Product\RegressionUtilities;

class LicensePlateV1TestRegression extends TestCase
{
    private string $rstRef;
    private Client $mindeeClient;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") .
            "/tests/resources/products/license_plates/response_v1/";
        $this->rstRef = file_get_contents($productDir . "default_sample.rst");
        $this->mindeeClient = new Client();
    }

    public function testRegression()
    {
        $inputSource = $this->mindeeClient->sourceFromPath(
            (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/license_plates/default_sample.jpg"
        );
        $response = $this->mindeeClient->parse(LicensePlateV1::class, $inputSource);
        $response->document->id = RegressionUtilities::getId($this->rstRef);
        $response->document->inference->product->version = RegressionUtilities::getVersion($this->rstRef);
        $this->assertEquals($this->rstRef, strval($response->document));
    }
}

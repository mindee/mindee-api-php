<?php

namespace Product\Us\DriverLicense;

require_once(__DIR__."/../../RegressionUtilities.php");
use Mindee\Product\Us\DriverLicense\DriverLicenseV1;
use Mindee\Client;
use PHPUnit\Framework\TestCase;
use Product\RegressionUtilities;

class DriverLicenseV1TestRegression extends TestCase
{
    private string $rstRef;
    private Client $mindeeClient;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") .
            "/tests/resources/products/us_driver_license/response_v1/";
        $this->rstRef = file_get_contents($productDir . "default_sample.rst");
        $this->mindeeClient = new Client();
    }

    public function testRegression()
    {
        $inputSource = $this->mindeeClient->sourceFromPath(
            (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/us_driver_license/default_sample.jpg"
        );
        $response = $this->mindeeClient->parse(DriverLicenseV1::class, $inputSource);
        $response->document->id = RegressionUtilities::getId($this->rstRef);
        $response->document->inference->product->version = RegressionUtilities::getVersion($this->rstRef);
        $this->assertEquals($this->rstRef, strval($response->document));
    }
}

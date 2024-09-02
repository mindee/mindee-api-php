<?php

namespace Input;

use Mindee\Client;
use Mindee\Parsing\Common\PredictResponse;
use Mindee\Product\Invoice\InvoiceV4;
use PHPUnit\Framework\TestCase;

class URLInputSourceTestRegression extends TestCase
{
    protected string $fileTypesDir;

    protected function setUp(): void
    {
        $this->realClient = new Client();
    }

    public function testSendUrlInputSource()
    {
        $urlInputSource = $this->realClient->sourceFromUrl("https://raw.githubusercontent.com/mindee/client-lib-test-data/main/file_types/pdf/blank_1.pdf");
        $res = $this->realClient->parse(InvoiceV4::class, $urlInputSource);
        $this->assertInstanceOf(PredictResponse::class, $res);
    }
}

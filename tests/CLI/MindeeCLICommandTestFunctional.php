<?php

namespace CLI;

require_once(__DIR__ . "/../../vendor/autoload.php");
require_once(__DIR__ . "/../../bin/MindeeCLIDocuments.php");
require_once(__DIR__ . "/MindeeCLITestingUtilities.php");

use Mindee\CLI\MindeeCLIDocuments;
use PHPUnit\Framework\TestCase;

class MindeeCLICommandTestFunctional extends TestCase
{
    private string $apiKey;

    protected function setUp(): void
    {
        $this->apiKey = getenv('MINDEE_API_KEY');
    }

    private function runValidCall($productName, $async = false, $initialArgs = []): array
    {
        $filePath = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/file_types/pdf/blank_1.pdf";
        $args = [$productName, $filePath, "-k", $this->apiKey];
        if ($initialArgs) {
            $args = array_merge($args, $initialArgs);
        }
        if ($async) {
            $args[] = "-A";
        }
        return MindeeCLITestingUtilities::executeTest($args);
    }


    public function productDataProvider()
    {
        $data = [];
        $data[] = ["generated", true, ["-a", "mindee", "-e", "invoice_splitter", "-d", "1"]];
        foreach (MindeeCLIDocuments::getSpecs() as $productName => $productSpecs) {
            if ($productName != "custom" && $productName != "generated") {
                if ($productSpecs->isSync) {
                    $data[] = [$productName, false];
                }
                if ($productSpecs->isAsync) {
                    $data[] = [$productName, true];
                }
            }
        }
        return $data;
    }

    /**
     * @dataProvider productDataProvider
     */
    public function testProduct($productName, $async, $additionnalArgs = [])
    {
        $cmdOutput = $this->runValidCall($productName, $async, $additionnalArgs);
        $this->assertEquals(0, $cmdOutput["code"], $productName . ($async ? " async" : " sync") . " test (code).");
        $this->assertTrue(
            str_contains($cmdOutput["output"][1], "Document"),
            $productName . ($async ? " async" : " sync") . " test (string return)."
        );
    }
}

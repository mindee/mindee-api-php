<?php

namespace CLI;

use PHPUnit\Framework\TestCase;

require_once(__DIR__."/MindeeCLITestingUtilities.php");

class MindeeCLICommandTest extends TestCase
{
    private string $apiKey;

    protected function setUp(): void
    {
        $this->apiKey = getenv('MINDEE_API_KEY');
    }

    public function testInvalidFilePath()
    {
        $cmdOutput = MindeeCLITestingUtilities::executeTest(["financial-document", "invalid-file-path", "-k", $this->apiKey, "-D"]);
        // Note : a direct comparison here would be too complicated due to the fact that the output of the command has
        // formatting applied by Symfony CLI.
        $this->assertEquals(1, $cmdOutput["code"]);
        $this->assertTrue(str_contains($cmdOutput["output"][0], "Invalid path or url provided 'invalid-file-path'."));
    }

    public function testInvalidKey()
    {
        $cmdOutput = MindeeCLITestingUtilities::executeTest(["financial-document", (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/file_types/pdf/blank_1.pdf", "-k", "invalid-key"]);
        $this->assertEquals(1, $cmdOutput["code"]);
        $this->assertTrue(str_contains(implode(" ", $cmdOutput["output"]), "Invalid token provided"));
    }

    public function testInvalidProduct()
    {
        $cmdOutput = MindeeCLITestingUtilities::executeTest(["invalid-product", (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/file_types/pdf/blank_1.pdf", "-k", "invalid-key", "-D"]);
        $this->assertEquals(1, $cmdOutput["code"]);
        $this->assertTrue(str_contains($cmdOutput["output"][0], "Invalid product: invalid-product"));
    }
}

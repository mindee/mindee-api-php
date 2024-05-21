<?php

namespace CLI;
use Mindee\Error\MindeeHttpClientException;
use PHPUnit\Framework\TestCase;

class MindeeCLICommandTest extends TestCase
{
    private string $apiKey;
    protected function setUp(): void
    {
        $this->apiKey = getenv('MINDEE_API_KEY');
    }

    private function executeTest($args, $mute){
        $resCode = 0;
        $output = "";
        if ($mute) {
            exec("php ./bin/cli.php " . implode(" ", $args), $output, $resCode);
        } else {
            $nullDevice = (stripos(PHP_OS, 'WIN') === 0) ? 'NUL' : '/dev/null';
            exec("php ./bin/cli.php " . implode(" ", $args) . "&> $nullDevice", $output, $resCode);
        }
        return ["output" => $output, "code" => $resCode];
    }

    public function testInvalidFilePath(){
        $cmdOutput = $this->executeTest(["financial-document", "invalid-file-path", "-k", $this->apiKey, "-D"]);
        // Note : a direct comparison here would be too complicated due to the fact that the output of the command has
        // formatting applied by Symfony CLI.
        $this->assertEquals(1, $cmdOutput["code"]);
        $this->assertTrue(str_contains($cmdOutput["output"][0], "Invalid path or url provided 'invalid-file-path'."));
    }

    public function testInvalidKey(){
        $cmdOutput = $this->executeTest(["financial-document", (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/file_types/pdf/blank_1.pdf", "-k", "invalid-key"]);
        $this->assertEquals(1, $cmdOutput["code"]);
        $this->assertTrue(str_contains($cmdOutput["output"][0], "Invalid API key 'invalid-key'."));
    }

    public function testInvalidProduct(){
        $cmdOutput = $this->executeTest(["invalid-product", (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/file_types/pdf/blank_1.pdf", "-k", "invalid-key", "-D"]);
        $this->assertEquals(1, $cmdOutput["code"]);
        $this->assertTrue(str_contains($cmdOutput["output"][0], "Invalid product: invalid-product"));
    }

    public function testValidSyncCall(){
        $cmdOutput = $this->executeTest(["receipt", (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/file_types/pdf/blank_1.pdf", "-k", $this->apiKey], true);
        $this->assertEquals(0, $cmdOutput["code"]);
    }

    public function testValidAsyncCall(){
        $cmdOutput = $this->executeTest(["invoice-splitter", (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/file_types/pdf/blank_1.pdf", "-k", $this->apiKey, "-A"], true);
        $this->assertEquals(0, $cmdOutput["code"]);
    }
}

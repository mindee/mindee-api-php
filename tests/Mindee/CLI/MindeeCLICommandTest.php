<?php

namespace Mindee\CLI;
use PHPUnit\Framework\TestCase;

class MindeeCLICommandTest extends TestCase
{
    private string $apiKey;
    protected function setUp(): void
    {
        $this->apiKey = getenv('MINDEE_API_KEY');
    }

    private function executeTest($args){
        return exec("php ./bin/cli.php " . implode(" ", $args) . "1>/dev/null"); # TODO: check adapted syntax for windows
    }

    public function testInvalidFilePath(){
        echo($this->executeTest(["financial-document", "invalid-file-path", "-k", $this->apiKey]));
    }
}

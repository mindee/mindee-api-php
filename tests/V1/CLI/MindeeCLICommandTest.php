<?php

declare(strict_types=1);

namespace V1\CLI;

use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/MindeeCLITestingUtilities.php");

class MindeeCLICommandTest extends TestCase
{
    private string $apiKey;
    private string $filePath;

    protected function setUp(): void
    {
        $this->filePath = TestingUtilities::getFileTypesDir() . "/pdf/blank_1.pdf";
        $this->apiKey = getenv('MINDEE_API_KEY');
    }

    public function testInvalidFilePath(): void
    {
        $cmdOutput = MindeeCLITestingUtilities::executeTest(["financial-document", "invalid-file-path", "-k", $this->apiKey, "-D"]);
        self::assertSame(1, $cmdOutput["code"]);
        self::assertTrue(str_contains((string) $cmdOutput["output"][0], "Invalid path or url provided 'invalid-file-path'."));
    }

    public function testInvalidKey(): void
    {
        $cmdOutput = MindeeCLITestingUtilities::executeTest(["financial-document", $this->filePath, "-k", "invalid-key"]);
        self::assertSame(1, $cmdOutput["code"]);
        self::assertTrue(str_contains(implode(" ", $cmdOutput["output"]), "Invalid token provided"));
    }

    public function testInvalidProduct(): void
    {
        $cmdOutput = MindeeCLITestingUtilities::executeTest(["invalid-product", $this->filePath, "-k", "invalid-key", "-D"]);
        self::assertSame(1, $cmdOutput["code"]);
        self::assertTrue(str_contains((string) $cmdOutput["output"][0], "Invalid product: invalid-product"));
    }
}

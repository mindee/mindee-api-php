<?php

declare(strict_types=1);

namespace V2\Input;

use Mindee\Input\LocalResponse;
use Mindee\V2\Product\Extraction\ExtractionResponse;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use TestingUtilities;

class LocalResponseV2Test extends TestCase
{
    private string $filePath;

    protected function setUp(): void
    {
        $this->filePath = TestingUtilities::getV2DataDir() . '/products/extraction/standard_field_types.json';
    }

    protected function assertLocalResponse(LocalResponse $localResponse): void
    {
        $fakeHMACSigning = "ogNjY44MhvKPGTtVsI8zG82JqWQa68woYQH";
        $signature = "e51bdf80f1a08ed44ee161100fc30a25cb35b4ede671b0a575dc9064a3f5dbf1";
        $reflectedLocalResponse = new ReflectionClass($localResponse);
        $reflectedFile = $reflectedLocalResponse->getProperty('file');
        self::assertNotNull($reflectedFile);
        self::assertFalse($localResponse->isValidHMACSignature($fakeHMACSigning, "fake HMAC signature"));
        self::assertSame($signature, $localResponse->getHmacSignature($fakeHMACSigning));
        self::assertTrue($localResponse->isValidHMACSignature($fakeHMACSigning, $signature));
        $response = $localResponse->deserializeResponse(ExtractionResponse::class);
        self::assertInstanceOf(ExtractionResponse::class, $response);
        self::assertNotNull($response->inference);
        self::assertNotNull($response->inference->result);
        self::assertNotNull($response->inference->result->fields);
    }

    public function testValidFileLocalResponse(): void
    {
        $file = fopen($this->filePath, 'r');
        $localResponse = new LocalResponse($file);
        fclose($file);
        $this->assertLocalResponse($localResponse);
    }

    public function testValidPathLocalResponse(): void
    {
        $localResponse = new LocalResponse($this->filePath);
        $this->assertLocalResponse($localResponse);
    }

    public function testValidBytesLocalResponse(): void
    {
        $raw = file_get_contents($this->filePath);
        $encoding = mb_detect_encoding($raw, ['UTF-8','UTF-16','UTF-32','ISO-8859-1','Windows-1252'], true) ?: 'UTF-8';
        $utf8 = ($encoding === 'UTF-8') ? $raw : mb_convert_encoding($raw, 'UTF-8', $encoding);
        $utf8 = preg_replace('/^\xEF\xBB\xBF/', '', $utf8);
        $localResponse = new LocalResponse($utf8);
        $this->assertLocalResponse($localResponse);
    }
}

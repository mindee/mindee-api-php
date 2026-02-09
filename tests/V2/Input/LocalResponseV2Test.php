<?php

namespace V2\Input;

use Mindee\Input\LocalResponse;
use Mindee\Parsing\V2\InferenceResponse;
use PHPUnit\Framework\TestCase;

class LocalResponseV2Test extends TestCase
{
    private string $filePath;

    protected function setUp(): void
    {
        $this->filePath = \TestingUtilities::getV2DataDir() . '/products/extraction/standard_field_types.json';
    }

    protected function assertLocalResponse(LocalResponse $localResponse): void
    {
        $fakeHMACSigning = "ogNjY44MhvKPGTtVsI8zG82JqWQa68woYQH";
        $signature = "e51bdf80f1a08ed44ee161100fc30a25cb35b4ede671b0a575dc9064a3f5dbf1";
        $reflectedLocalResponse = new \ReflectionClass($localResponse);
        $reflectedFile = $reflectedLocalResponse->getProperty('file');
        $reflectedFile->setAccessible(true);
        $this->assertNotNull($reflectedFile);
        $this->assertFalse($localResponse->isValidHMACSignature($fakeHMACSigning, "fake HMAC signature"));
        $this->assertEquals($signature, $localResponse->getHmacSignature($fakeHMACSigning));
        $this->assertTrue($localResponse->isValidHMACSignature($fakeHMACSigning, $signature));
        $response = $localResponse->deserializeResponse(InferenceResponse::class);
        $this->assertInstanceOf(InferenceResponse::class, $response);
        $this->assertNotNull($response->inference);
        $this->assertNotNull($response->inference->result);
        $this->assertNotNull($response->inference->result->fields);
    }

    public function testValidFileLocalResponse(){
        $file = fopen($this->filePath, 'rb');
        $localResponse = new LocalResponse($file);
        fclose($file);
        $this->assertLocalResponse($localResponse);
    }

    public function testValidPathLocalResponse(){
        $localResponse = new LocalResponse($this->filePath);
        $this->assertLocalResponse($localResponse);
    }

    public function testValidBytesLocalResponse(){
        $raw = file_get_contents($this->filePath);
        $encoding = mb_detect_encoding($raw, ['UTF-8','UTF-16','UTF-32','ISO-8859-1','Windows-1252'], true) ?: 'UTF-8';
        $utf8 = ($encoding === 'UTF-8') ? $raw : mb_convert_encoding($raw, 'UTF-8', $encoding);
        $utf8 = preg_replace('/^\xEF\xBB\xBF/', '', $utf8);
        $localResponse = new LocalResponse($utf8);
        $this->assertLocalResponse($localResponse);
    }
}

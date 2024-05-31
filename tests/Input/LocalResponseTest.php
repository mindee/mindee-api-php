<?php

namespace Input;

use Mindee\Input\LocalResponse;
use PHPUnit\Framework\TestCase;

class LocalResponseTest extends TestCase {
    private string $signature;
    private string $dummyKey;
    private string $filePath;

    protected function setUp(): void {
        $this->filePath = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/async/get_completed_empty.json";
        $this->signature = "5ed1673e34421217a5dbfcad905ee62261a3dd66c442f3edd19302072bbf70d0";
        $this->dummyKey = "ogNjY44MhvKPGTtVsI8zG82JqWQa68woYQH";
    }

    public function testValidFileLocalResponse()
    {
        $file = fopen($this->filePath, 'rb');
        $localResponse = new LocalResponse($file);
        fclose($file);

        $this->assertNotNull($localResponse->toArray(), 'Local response file should not be null');

        $invalidSignature = 'invalid_signature';
        $this->assertFalse(
            $localResponse->isValidHmacSignature($this->dummyKey, $invalidSignature),
            'Invalid signature should not be valid'
        );

        $calculatedSignature = $localResponse->getHmacSignature($this->dummyKey);
        $this->assertEquals(
            $this->signature,
            $calculatedSignature,
            'Calculated signature should match the expected valid signature'
        );

        $this->assertTrue(
            $localResponse->isValidHmacSignature($this->dummyKey, $this->signature),
            'Valid signature should be valid'
        );
    }

    public function testValidStringLocalResponse()
    {
        $file = file_get_contents($this->filePath);
        $localResponse = new LocalResponse($file);

        $this->assertNotNull($localResponse->toArray(), 'Local response file should not be null');

        $invalidSignature = 'invalid_signature';
        $this->assertFalse(
            $localResponse->isValidHmacSignature($this->dummyKey, $invalidSignature),
            'Invalid signature should not be valid'
        );

        $calculatedSignature = $localResponse->getHmacSignature($this->dummyKey);
        $this->assertEquals(
            $this->signature,
            $calculatedSignature,
            'Calculated signature should match the expected valid signature'
        );

        $this->assertTrue(
            $localResponse->isValidHmacSignature($this->dummyKey, $this->signature),
            'Valid signature should be valid'
        );
    }


    public function testValidStreamLocalResponse()
    {
        // Create a stream from the file content
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, file_get_contents($this->filePath));
        rewind($stream);

        // Create LocalResponse instance with the stream
        $localResponse = new LocalResponse($stream);

        $this->assertNotNull($localResponse->toArray(), 'Local response file should not be null');

        $invalidSignature = 'invalid_signature';
        $this->assertFalse(
            $localResponse->isValidHmacSignature($this->dummyKey, $invalidSignature),
            'Invalid signature should not be valid'
        );

        $calculatedSignature = $localResponse->getHmacSignature($this->dummyKey);
        $this->assertEquals(
            $this->signature,
            $calculatedSignature,
            'Calculated signature should match the expected valid signature'
        );

        $this->assertTrue(
            $localResponse->isValidHmacSignature($this->dummyKey, $this->signature),
            'Valid signature should be valid'
        );

        fclose($stream); // Close the stream after use
    }
    
    public function testValidFilePathLocalResponse()
    {
        $localResponse = new LocalResponse($this->filePath);

        $this->assertNotNull($localResponse->toArray(), 'Local response file should not be null');

        $invalidSignature = 'invalid_signature';
        $this->assertFalse(
            $localResponse->isValidHmacSignature($this->dummyKey, $invalidSignature),
            'Invalid signature should not be valid'
        );

        $calculatedSignature = $localResponse->getHmacSignature($this->dummyKey);
        $this->assertEquals(
            $this->signature,
            $calculatedSignature,
            'Calculated signat match the expected valid signature'
        );

        $this->assertTrue(
            $localResponse->isValidHmacSignature($this->dummyKey, $this->signature),
            'Valid signature should be valid'
        );
    }
}

<?php

declare(strict_types=1);

namespace V1\Input;

use Mindee\Input\LocalResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class LocalResponseV1Test extends TestCase
{
    private string $signature;
    private string $dummyKey;
    private string $filePath;

    protected function setUp(): void
    {
        $this->filePath = TestingUtilities::getV1DataDir() . '/async/get_completed_empty.json';
        $this->signature = "5ed1673e34421217a5dbfcad905ee62261a3dd66c442f3edd19302072bbf70d0";
        $this->dummyKey = "ogNjY44MhvKPGTtVsI8zG82JqWQa68woYQH";
    }

    public function testValidFileLocalResponse(): void
    {
        $file = fopen($this->filePath, 'r');
        $localResponse = new LocalResponse($file);
        fclose($file);

        self::assertNotNull($localResponse->toArray(), 'Local response file should not be null');

        $invalidSignature = 'invalid_signature';
        self::assertFalse(
            $localResponse->isValidHmacSignature($this->dummyKey, $invalidSignature),
            'Invalid signature should not be valid'
        );

        $calculatedSignature = $localResponse->getHmacSignature($this->dummyKey);
        self::assertSame(
            $this->signature,
            $calculatedSignature,
            'Calculated signature should match the expected valid signature'
        );

        self::assertTrue(
            $localResponse->isValidHmacSignature($this->dummyKey, $this->signature),
            'Valid signature should be valid'
        );
    }

    public function testValidStringLocalResponse(): void
    {
        $file = file_get_contents($this->filePath);
        $localResponse = new LocalResponse($file);

        self::assertNotNull($localResponse->toArray(), 'Local response file should not be null');

        $invalidSignature = 'invalid_signature';
        self::assertFalse(
            $localResponse->isValidHmacSignature($this->dummyKey, $invalidSignature),
            'Invalid signature should not be valid'
        );

        $calculatedSignature = $localResponse->getHmacSignature($this->dummyKey);
        self::assertSame(
            $this->signature,
            $calculatedSignature,
            'Calculated signature should match the expected valid signature'
        );

        self::assertTrue(
            $localResponse->isValidHmacSignature($this->dummyKey, $this->signature),
            'Valid signature should be valid'
        );
    }


    public function testValidStreamLocalResponse(): void
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, file_get_contents($this->filePath));
        rewind($stream);

        $localResponse = new LocalResponse($stream);

        self::assertNotNull($localResponse->toArray(), 'Local response file should not be null');

        $invalidSignature = 'invalid_signature';
        self::assertFalse(
            $localResponse->isValidHmacSignature($this->dummyKey, $invalidSignature),
            'Invalid signature should not be valid'
        );

        $calculatedSignature = $localResponse->getHmacSignature($this->dummyKey);
        self::assertSame(
            $this->signature,
            $calculatedSignature,
            'Calculated signature should match the expected valid signature'
        );

        self::assertTrue(
            $localResponse->isValidHmacSignature($this->dummyKey, $this->signature),
            'Valid signature should be valid'
        );

        fclose($stream);
    }

    public function testValidFilePathLocalResponse(): void
    {
        $localResponse = new LocalResponse($this->filePath);

        self::assertNotNull($localResponse->toArray(), 'Local response file should not be null');

        $invalidSignature = 'invalid_signature';
        self::assertFalse(
            $localResponse->isValidHmacSignature($this->dummyKey, $invalidSignature),
            'Invalid signature should not be valid'
        );

        $calculatedSignature = $localResponse->getHmacSignature($this->dummyKey);
        self::assertSame(
            $this->signature,
            $calculatedSignature,
            'Calculated signat match the expected valid signature'
        );

        self::assertTrue(
            $localResponse->isValidHmacSignature($this->dummyKey, $this->signature),
            'Valid signature should be valid'
        );
    }
}

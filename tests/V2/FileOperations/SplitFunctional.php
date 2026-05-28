<?php

declare(strict_types=1);

namespace V2\FileOperations;

use Mindee\Input\PathInput;
use Mindee\V2\Client;
use Mindee\V2\FileOperations\Split;
use Mindee\V2\Product\Extraction\ExtractionResponse;
use Mindee\V2\Product\Extraction\Params\ExtractionParameters;
use Mindee\V2\Product\Split\Params\SplitParameters;
use Mindee\V2\Product\Split\SplitResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;
use ImagickException;

use function count;
use function sprintf;
use function strlen;

class SplitFunctional extends TestCase
{
    private Client $client;
    private string $splitModelId;
    private string $findocModelId;
    private string $outputDir;

    protected function setUp(): void
    {
        $apiKey = getenv('MINDEE_V2_API_KEY') ?: '';
        $this->client = new Client($apiKey);
        $this->splitModelId = getenv('MINDEE_V2_SPLIT_MODEL_ID') ?: '';
        $this->findocModelId = getenv('MINDEE_V2_FINDOC_MODEL_ID') ?: '';

        $this->outputDir = getcwd() . '/output';
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0o777, true);
        }
    }

    protected function tearDown(): void
    {
        $file1 = $this->outputDir . '/split_001.pdf';
        $file2 = $this->outputDir . '/split_002.pdf';

        if (file_exists($file1)) {
            unlink($file1);
        }
        if (file_exists($file2)) {
            unlink($file2);
        }
    }

    private function checkFindocReturn(ExtractionResponse $findocResponse): void
    {
        self::assertGreaterThan(0, strlen($findocResponse->inference->model->id));

        $totalAmount = $findocResponse->inference->result->fields['total_amount'];
        self::assertNotNull($totalAmount);
        self::assertGreaterThan(0, $totalAmount->value);
    }

    /**
     * @throws ImagickException
     */
    public function testExtractSplitsFromPdfCorrectly(): void
    {
        $inputSource = new PathInput(TestingUtilities::getV2ProductDir() . '/split/default_sample.pdf');
        $splitParams = new SplitParameters($this->splitModelId);

        $response = $this->client->enqueueAndGetResult(SplitResponse::class, $inputSource, $splitParams);

        self::assertNotNull($response);
        self::assertCount(2, $response->inference->result->splits);

        self::assertInstanceof(SplitResponse::class, $response);
        $extractedSplits = $response->inference->result->extractFromInputSource($inputSource);
        $extractedSplit0 = $response->inference->result->splits[0]->extractFromInputSource($inputSource);
        self::assertSame($extractedSplit0, $extractedSplits[0]);

        self::assertCount(2, $extractedSplits);
        self::assertSame('default_sample_001-001.pdf', $extractedSplits[0]->filename);
        self::assertSame('default_sample_002-002.pdf', $extractedSplits[1]->filename);

        $inferenceInput = $extractedSplits[0]->asInputSource();
        $findocParams = new ExtractionParameters($this->findocModelId);

        $invoice0 = $this->client->enqueueAndGetResult(ExtractionResponse::class, $inferenceInput, $findocParams);

        $this->checkFindocReturn($invoice0);

        $extractedSplits->saveAllToDisk($this->outputDir);

        for ($i = 0; $i < count($extractedSplits); $i++) {
            $fileName = sprintf('split_%03d.pdf', $i + 1);
            $filePath = $this->outputDir . '/' . $fileName;

            self::assertFileExists($filePath);
            self::assertGreaterThan(0, filesize($filePath));

            $localInput = new PathInput($filePath);
            self::assertSame($extractedSplits[$i]->getPageCount(), $localInput->getPageCount());
        }
    }
}

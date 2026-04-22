<?php

namespace V2\FileOperations;

use Mindee\ClientV2;
use Mindee\Input\InferenceParameters;
use Mindee\Input\PathInput;
use Mindee\Parsing\V2\InferenceResponse;
use Mindee\V2\FileOperations\Split;
use Mindee\V2\Product\Split\SplitResponse;
use Mindee\V2\Product\Split\Params\SplitParameters;
use PHPUnit\Framework\TestCase;

class SplitFunctional extends TestCase
{
    private ClientV2 $client;
    private string $splitModelId;
    private string $findocModelId;
    private string $outputDir;

    protected function setUp(): void
    {
        $apiKey = getenv('MINDEE_V2_API_KEY') ?: '';
        $this->client = new ClientV2($apiKey);
        $this->splitModelId = getenv('MINDEE_V2_SPLIT_MODEL_ID') ?: '';
        $this->findocModelId = getenv('MINDEE_V2_FINDOC_MODEL_ID') ?: '';

        $this->outputDir = getcwd() . '/output';
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0777, true);
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

    private function checkFindocReturn(InferenceResponse $findocResponse): void
    {
        $this->assertGreaterThan(0, strlen($findocResponse->inference->model->id));

        $totalAmount = $findocResponse->inference->result->fields['total_amount'];
        $this->assertNotNull($totalAmount);
        $this->assertGreaterThan(0, $totalAmount->value);
    }

    public function testExtractSplitsFromPdfCorrectly(): void
    {
        $inputSource = new PathInput(\TestingUtilities::getV2ProductDir() . '/split/default_sample.pdf');
        $splitParams = new SplitParameters($this->splitModelId);

        $response = $this->client->enqueueAndGetResult(SplitResponse::class, $inputSource, $splitParams);

        $this->assertNotNull($response);
        $this->assertCount(2, $response->inference->result->splits);

        $splitOperation = new Split($inputSource);
        $extractedSplits = $splitOperation->extractSplits(
            array_map(fn($s) => $s->pageRange, $response->inference->result->splits)
        );

        $this->assertCount(2, $extractedSplits);
        $this->assertEquals('default_sample_001-001.pdf', $extractedSplits[0]->filename);
        $this->assertEquals('default_sample_002-002.pdf', $extractedSplits[1]->filename);

        $inferenceInput = $extractedSplits[0]->asInputSource();
        $findocParams = new InferenceParameters($this->findocModelId);

        $invoice0 = $this->client->enqueueAndGetResult(InferenceResponse::class, $inferenceInput, $findocParams);

        $this->checkFindocReturn($invoice0);

        $extractedSplits->saveAllToDisk($this->outputDir);

        for ($i = 0; $i < count($extractedSplits); $i++) {
            $fileName = sprintf('split_%03d.pdf', $i + 1);
            $filePath = $this->outputDir . '/' . $fileName;

            $this->assertFileExists($filePath);
            $this->assertGreaterThan(0, filesize($filePath));

            $localInput = new PathInput($filePath);
            $this->assertEquals($extractedSplits[$i]->getPageCount(), $localInput->getPageCount());
        }
    }
}
<?php

declare(strict_types=1);

namespace V2\FileOperations;

use Mindee\Error\MindeePdfException;
use Mindee\Input\PathInput;
use Mindee\V2\Client;
use Mindee\V2\Product\Extraction\ExtractionResponse;
use Mindee\V2\Product\Extraction\Params\ExtractionParameters;
use Mindee\V2\Product\Split\Params\SplitParameters;
use Mindee\V2\Product\Split\SplitResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

use function count;
use function sprintf;
use function strlen;

/**
 * @group bernard-ledit
 */
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
    private const VOLATILE_PDF_PATTERN
        = '#/ID\s*\[<[0-9A-Fa-f]*>\s*<[0-9A-Fa-f]*>\]|/CreationDate\s*\([^)]*\)|/ModDate\s*\([^)]*\)#';

    /**
     * Strips volatile metadata (document ID, creation/modification dates) so that
     * two PDFs generated from the same pages can be compared deterministically.
     */
    private static function pdfFingerprint(string $pdfBytes): string
    {
        $fingerprint = preg_replace(self::VOLATILE_PDF_PATTERN, '', $pdfBytes);
        self::assertNotNull($fingerprint, 'Failed to compute PDF fingerprint: ' . preg_last_error_msg());

        return $fingerprint;
    }

    /**
     * @throws MindeePdfException
     */
    public function testExtractSplitsFromPdfCorrectly(): void
    {
        $inputSource = new PathInput(TestingUtilities::getV2ProductDir() . '/split/default_sample.pdf');
        $splitParams = new SplitParameters($this->splitModelId);

        $response = $this->client->enqueueAndGetResult(SplitResponse::class, $inputSource, $splitParams);

        self::assertNotNull($response);
        self::assertInstanceOf(SplitResponse::class, $response);
        self::assertCount(2, $response->inference->result->splits);
        self::assertSame(2, $inputSource->pageCount);

        $extractedSplits = $response->inference->result->extractFromInputSource($inputSource);
        self::assertCount(2, $extractedSplits);

        foreach ($response->inference->result->splits as $i => $split) {
            $extractedSingle = $split->extractFromInputSource($inputSource);
            self::assertSame($extractedSingle->filename, $extractedSplits[$i]->filename);
            self::assertSame($extractedSingle->pageCount, $extractedSplits[$i]->pageCount);
            self::assertSame(
                self::pdfFingerprint($extractedSingle->getPdfBytes()),
                self::pdfFingerprint($extractedSplits[$i]->getPdfBytes())
            );
        }

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
            self::assertSame($extractedSplits[$i]->pageCount, $localInput->pageCount);
        }
    }
}

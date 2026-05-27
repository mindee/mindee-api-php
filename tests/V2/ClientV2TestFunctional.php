<?php

declare(strict_types=1);

namespace V2;

use Mindee\Error\MindeeV2HttpException;
use Mindee\Input\PathInput;
use Mindee\Input\UrlInputSource;
use Mindee\V2\Client;
use Mindee\V2\Product\Extraction\ExtractionResponse;
use Mindee\V2\Product\Extraction\Params\ExtractionParameters;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class ClientV2TestFunctional extends TestCase
{
    private Client $mindeeClient;
    private string $modelId;

    protected function setUp(): void
    {
        $apiKey = getenv('MINDEE_V2_API_KEY');
        $this->modelId = getenv('MINDEE_V2_FINDOC_MODEL_ID');

        $this->mindeeClient = new Client($apiKey);
    }

    public function testParseFileEmptyMultiPageMustSucceed(): void
    {
        $source = new PathInput(TestingUtilities::getFileTypesDir() . '/pdf/multipage_cut-2.pdf');
        $inferenceParams = new ExtractionParameters($this->modelId, rag: false, rawText: true);

        $response = $this->mindeeClient->enqueueAndGetResult(ExtractionResponse::class, $source, $inferenceParams);
        self::assertNotNull($response);
        $inference = $response->inference;
        self::assertNotNull($inference);

        $file = $inference->file;
        self::assertNotNull($file);
        self::assertSame('multipage_cut-2.pdf', $file->name);
        self::assertSame(2, $file->pageCount);

        self::assertNotNull($inference->model);
        self::assertSame($this->modelId, $inference->model->id);

        $activeOptions = $inference->activeOptions;
        self::assertTrue($activeOptions->rawText, "Raw text must be enabled");
        self::assertFalse($activeOptions->polygon, "Polygon must be disabled by default");
        self::assertFalse($activeOptions->confidence, "Confidence must be disabled by default");
        self::assertFalse($activeOptions->rag, "RAG must be disabled by default");

        $result = $inference->result;
        self::assertNotNull($result);

        $rawText = $result->rawText;
        self::assertNotNull($rawText);
        self::assertCount(2, $rawText->pages);
    }

    /**
     * Filled, single-page image – enqueue & parse must succeed
     */
    public function testParseFileFilledSinglePageMustSucceed(): void
    {
        $source = new PathInput(
            TestingUtilities::getV1DataDir() . '/products/financial_document/default_sample.jpg'
        );

        $inferenceParams = new ExtractionParameters($this->modelId, rag: false, textContext: 'this is an invoice');

        $response = $this->mindeeClient->enqueueAndGetResult(ExtractionResponse::class, $source, $inferenceParams);
        self::assertNotNull($response);
        $inference = $response->inference;
        self::assertNotNull($inference);

        $file = $inference->file;
        self::assertNotNull($file);
        self::assertSame('default_sample.jpg', $file->name);
        self::assertSame(1, $file->pageCount);

        self::assertNotNull($inference->model);
        self::assertSame($this->modelId, $inference->model->id);

        $result = $inference->result;
        self::assertNotNull($result);

        self::assertNotNull($result->fields);
        self::assertNotNull($result->fields['supplier_name'] ?? null);

        $supplierName = $result->fields['supplier_name']->value ?? null;
        self::assertSame(
            'John Smith',
            $supplierName
        );
    }

    public function testInvalidUUIDMustThrowError(): void
    {

        $source = new PathInput(TestingUtilities::getFileTypesDir() . '/pdf/blank_1.pdf');

        $inferenceParams = new ExtractionParameters('INVALID MODEL ID');

        try {
            $this->mindeeClient->enqueueInference($source, $inferenceParams);
        } catch (MindeeV2HttpException $e) {
            self::assertStringStartsWith('422-', $e->errorCode);
            self::assertNotEmpty($e->title);
            self::assertIsArray($e->errors);
        }
    }

    public function testUnknownModelMustThrowError(): void
    {
        $source = new PathInput(TestingUtilities::getFileTypesDir() . '/pdf/multipage_cut-2.pdf');

        $inferenceParams = new ExtractionParameters('fc405e37-4ba4-4d03-aeba-533a8d1f0f21', textContext: 'this is invalid');

        try {
            $this->mindeeClient->enqueueInference($source, $inferenceParams);
        } catch (MindeeV2HttpException $e) {
            self::assertStringStartsWith('404-', $e->errorCode);
            self::assertNotEmpty($e->title);
            self::assertIsArray($e->errors);
        }
    }


    public function testInvalidJobMustThrowError(): void
    {
        try {
            $this->mindeeClient->getResult(ExtractionResponse::class, 'fc405e37-4ba4-4d03-aeba-533a8d1f0f21');
        } catch (MindeeV2HttpException $e) {
            self::assertStringStartsWith('404-', $e->errorCode);
            self::assertNotEmpty($e->title);
            self::assertIsArray($e->errors);
        }
    }

    public function testInvalidWebhookIDsMustThrowError(): void
    {
        $source = new PathInput(TestingUtilities::getFileTypesDir() . '/pdf/multipage_cut-2.pdf');

        $inferenceParams = new ExtractionParameters(
            $this->modelId,
            null,
            null,
            null,
            null,
            null,
            ['fc405e37-4ba4-4d03-aeba-533a8d1f0f21', 'fc405e37-4ba4-4d03-aeba-533a8d1f0f21'],
            null
        );

        try {
            $this->mindeeClient->enqueueInference($source, $inferenceParams);
        } catch (MindeeV2HttpException $e) {
            self::assertStringStartsWith('422-', $e->errorCode);
            self::assertNotEmpty($e->title);
            self::assertIsArray($e->errors);
        }
    }

    public function testUrlInputSourceMustNotRaiseErrors(): void
    {
        $urlSource = new UrlInputSource(getenv('MINDEE_V2_SE_TESTS_BLANK_PDF_URL'));

        $inferenceParams = new ExtractionParameters($this->modelId);

        $response = $this->mindeeClient->enqueueAndGetResult(ExtractionResponse::class, $urlSource, $inferenceParams);
        self::assertNotNull($response);
        $inference = $response->inference;
        self::assertNotNull($inference);

        $file = $inference->file;
        self::assertNotNull($file);

        $result = $inference->result;
        self::assertNotNull($result);
    }

    public function testDataSchemaMustSucceed(): void
    {

        $source = new PathInput(
            TestingUtilities::getFileTypesDir() . '/pdf/blank_1.pdf'
        );
        $dataSchemaReplace = file_get_contents(
            TestingUtilities::getV2DataDir() . '/products/extraction/data_schema_replace_param.json'
        );

        $inferenceParams = new ExtractionParameters($this->modelId, dataSchema: $dataSchemaReplace);

        $response = $this->mindeeClient->enqueueAndGetResult(ExtractionResponse::class, $source, $inferenceParams);
        self::assertNotNull($response);
        $inference = $response->inference;
        self::assertNotNull($inference);

        $file = $inference->file;
        self::assertNotNull($file);
        self::assertSame('blank_1.pdf', $file->name);
        self::assertSame(1, $file->pageCount);

        self::assertNotNull($inference->model);
        self::assertSame($this->modelId, $inference->model->id);
        self::assertNotNull($inference->activeOptions);
        self::assertTrue($inference->activeOptions->dataSchema->replace);

        $result = $inference->result;
        self::assertNotNull($result);

        self::assertNotNull($result->fields);
        self::assertNotNull($result->fields['test_replace'] ?? null);

        self::assertSame(
            'a test value',
            $result->fields['test_replace']->value
        );
    }

    public function testMultipleWebhooksMustSucceed(): void
    {
        $source = new PathInput(
            TestingUtilities::getFileTypesDir() . '/pdf/blank_1.pdf'
        );

        $inferenceParams = new ExtractionParameters(
            $this->modelId,
            webhookIds: [
                getenv('MINDEE_V2_FAILURE_WEBHOOK_ID'),
                getenv('MINDEE_V2_SE_TESTS_FAILURE_WEBHOOK_ID')]
        );
        $response = $this->mindeeClient->enqueue($source, $inferenceParams);
        self::assertCount(2, $response->job->webhooks);
    }
}

<?php

namespace V2;

use Mindee\ClientV2;
use Mindee\Error\MindeeV2HttpException;
use Mindee\Input\InferenceParameters;
use Mindee\Input\PathInput;
use Mindee\Input\URLInputSource;
use PHPUnit\Framework\TestCase;

class ClientV2TestFunctional extends TestCase
{
    private ClientV2 $mindeeClient;
    private string $modelId;

    protected function setUp(): void
    {
        $apiKey = getenv('MINDEE_V2_API_KEY');
        $this->modelId = getenv('MINDEE_V2_FINDOC_MODEL_ID');

        $this->mindeeClient = new ClientV2($apiKey);
    }

    public function testParseFileEmptyMultiPageMustSucceed(): void
    {
        $source = new PathInput(\TestingUtilities::getFileTypesDir() . '/pdf/multipage_cut-2.pdf');
        $inferenceParams = new InferenceParameters($this->modelId, rag: false, rawText: true);

        $response = $this->mindeeClient->enqueueAndGetInference($source, $inferenceParams);
        $this->assertNotNull($response);
        $inference = $response->inference;
        $this->assertNotNull($inference);

        $file = $inference->file;
        $this->assertNotNull($file);
        $this->assertEquals('multipage_cut-2.pdf', $file->name);
        $this->assertEquals(2, $file->pageCount);

        $this->assertNotNull($inference->model);
        $this->assertEquals($this->modelId, $inference->model->id);

        $activeOptions = $inference->activeOptions;
        $this->assertTrue($activeOptions->rawText, "Raw text must be enabled");
        $this->assertFalse($activeOptions->polygon, "Polygon must be disabled by default");
        $this->assertFalse($activeOptions->confidence, "Confidence must be disabled by default");
        $this->assertFalse($activeOptions->rag, "RAG must be disabled by default");

        $result = $inference->result;
        $this->assertNotNull($result);

        $rawText = $result->rawText;
        $this->assertNotNull($rawText);
        $this->assertCount(2, $rawText->pages);
    }

    /**
     * Filled, single-page image – enqueue & parse must succeed
     */
    public function testParseFileFilledSinglePageMustSucceed(): void
    {
        $source = new PathInput(
            \TestingUtilities::getV1DataDir() . '/products/financial_document/default_sample.jpg'
        );

        $inferenceParams = new InferenceParameters($this->modelId, rag: false, textContext: 'this is an invoice');

        $response = $this->mindeeClient->enqueueAndGetInference($source, $inferenceParams);
        $this->assertNotNull($response);
        $inference = $response->inference;
        $this->assertNotNull($inference);

        $file = $inference->file;
        $this->assertNotNull($file);
        $this->assertEquals('default_sample.jpg', $file->name);
        $this->assertEquals(1, $file->pageCount);

        $this->assertNotNull($inference->model);
        $this->assertEquals($this->modelId, $inference->model->id);

        $result = $inference->result;
        $this->assertNotNull($result);

        $this->assertNotNull($result->fields);
        $this->assertNotNull($result->fields['supplier_name'] ?? null);

        $supplierName = $result->fields['supplier_name']->value ?? null;
        $this->assertEquals(
            'John Smith',
            $supplierName
        );
    }

    public function testInvalidUUIDMustThrowError(): void
    {

        $source = new PathInput(\TestingUtilities::getFileTypesDir() . '/pdf/blank_1.pdf');

        $inferenceParams = new InferenceParameters('INVALID MODEL ID');

        try {
            $this->mindeeClient->enqueueInference($source, $inferenceParams);
        } catch (MindeeV2HttpException $e) {
            $this->assertStringStartsWith('422-', $e->errorCode);
            $this->assertNotEmpty($e->title);
            $this->assertIsArray($e->errors);
        }
    }

    public function testUnknownModelMustThrowError(): void
    {
        $source = new PathInput(\TestingUtilities::getFileTypesDir() . '/pdf/multipage_cut-2.pdf');

        $inferenceParams = new InferenceParameters('fc405e37-4ba4-4d03-aeba-533a8d1f0f21', textContext: 'this is invalid');

        try {
            $this->mindeeClient->enqueueInference($source, $inferenceParams);
        } catch (MindeeV2HttpException $e) {
            $this->assertStringStartsWith('404-', $e->errorCode);
            $this->assertNotEmpty($e->title);
            $this->assertIsArray($e->errors);
        }
    }


    public function testInvalidJobMustThrowError(): void
    {
        try {
            $this->mindeeClient->getInference('fc405e37-4ba4-4d03-aeba-533a8d1f0f21');
        } catch (MindeeV2HttpException $e) {
            $this->assertStringStartsWith('404-', $e->errorCode);
            $this->assertNotEmpty($e->title);
            $this->assertIsArray($e->errors);
        }
    }

    public function testInvalidWebhookIDsMustThrowError()
    {
        $source = new PathInput(\TestingUtilities::getFileTypesDir() . '/pdf/multipage_cut-2.pdf');

        $inferenceParams = new InferenceParameters(
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
            $this->assertStringStartsWith('422-', $e->errorCode);
            $this->assertNotEmpty($e->title);
            $this->assertIsArray($e->errors);
        }
    }

    public function testUrlInputSourceMustNotRaiseErrors(): void
    {
        $urlSource = new URLInputSource(getenv('MINDEE_V2_SE_TESTS_BLANK_PDF_URL'));

        $inferenceParams = new InferenceParameters($this->modelId);

        $response = $this->mindeeClient->enqueueAndGetInference($urlSource, $inferenceParams);
        $this->assertNotNull($response);
        $inference = $response->inference;
        $this->assertNotNull($inference);

        $file = $inference->file;
        $this->assertNotNull($file);

        $result = $inference->result;
        $this->assertNotNull($result);
    }
}

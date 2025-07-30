
<?php

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
        $source = new PathInput(__DIR__ . '/resources/file_types/pdf/multipage_cut-2.pdf');

        $options = new InferenceParameters($this->modelId);

        $response = $this->mindeeClient->enqueueAndGetInference($source, $options);

        $this->assertNotNull($response);
        $this->assertNotNull($response->inference);

        $this->assertNotNull($response->inference->file);
        $this->assertEquals('multipage_cut-2.pdf', $response->inference->file->name);

        $this->assertNotNull($response->inference->model);
        $this->assertEquals($this->modelId, $response->inference->model->id);

        $this->assertNotNull($response->inference->result);
        $this->assertNull($response->inference->result->options ?? null);
    }

    /**
     * Filled, single-page image – enqueue & parse must succeed
     */
    public function testParseFileFilledSinglePageMustSucceed(): void
    {
        $source = new PathInput(__DIR__ . '/resources/products/financial_document/default_sample.jpg');

        $options = new InferenceParameters($this->modelId, false);

        $response = $this->mindeeClient->enqueueAndGetInference($source, $options);

        $this->assertNotNull($response);
        $this->assertNotNull($response->inference);

        $this->assertNotNull($response->inference->file);
        $this->assertEquals('default_sample.jpg', $response->inference->file->name);

        $this->assertNotNull($response->inference->model);
        $this->assertEquals($this->modelId, $response->inference->model->id);

        $this->assertNotNull($response->inference->result);
        $this->assertNotNull($response->inference->result->fields);
        $this->assertNotNull($response->inference->result->fields['supplier_name'] ?? null);

        $supplierName = $response->inference->result->fields['supplier_name']->value ?? null;
        $this->assertEquals(
            'John Smith',
            $supplierName
        );
    }

    public function testInvalidModelMustThrowError(): void
    {
        $source = new PathInput(__DIR__ . '/resources/file_types/pdf/multipage_cut-2.pdf');

        $options = new InferenceParameters('INVALID MODEL ID');

        $this->expectException(MindeeV2HttpException::class);
        $this->expectExceptionMessage('422');

        $this->mindeeClient->enqueueInference($source, $options);
    }

    public function testInvalidJobMustThrowError(): void
    {
        $this->expectException(MindeeV2HttpException::class);
        $this->expectExceptionMessage('422');

        $this->mindeeClient->getInference('not-a-valid-job-ID');
    }

    public function testUrlInputSourceMustNotRaiseErrors(): void
    {
        $urlSource = new URLInputSource(getenv('MINDEE_V2_SE_TESTS_BLANK_PDF_URL'));

        $options = new InferenceParameters($this->modelId);

        $response = $this->mindeeClient->enqueueAndGetInference($urlSource, $options);

        $this->assertNotNull($response);
        $this->assertNotNull($response->inference);
    }
}

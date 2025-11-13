<?php

namespace V1\Workflow;

use Mindee\Client;
use Mindee\Input\PredictMethodOptions;
use Mindee\Input\WorkflowOptions;
use Mindee\Product\FinancialDocument\FinancialDocumentV1;
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . "/../../TestingUtilities.php");

class WorkflowTestFunctional extends TestCase
{
    private $workflowId;
    private $mindeeClient;
    private $inputSource;
    private $predictionType;

    protected function setUp(): void
    {
        $this->mindeeClient = new Client();
        $this->workflowId = getenv('WORKFLOW_ID') ?: '';
        $this->predictionType = FinancialDocumentV1::class;
        $this->inputSource = $this->mindeeClient->sourceFromPath(
            \TestingUtilities::getV1DataDir() . "/products/financial_document/default_sample.jpg"
        );
    }

    public function testWorkflow() {
        $currentDateTime = date('Y-m-d-H:i:s');
        $options = new WorkflowOptions(
            "php-" . $currentDateTime,
            "low",
            false,
            null,
            true
        );
        $response = $this->mindeeClient->executeWorkflow(
            $this->inputSource, $this->workflowId, $options
        );
        $this->assertEquals(202, $response->apiRequest->statusCode);
        $this->assertEquals("php-$currentDateTime", $response->execution->file->alias);
        $this->assertEquals("low", $response->execution->priority);
    }

    public function testWorkflowPollingWithRAG() {
        $options = new PredictMethodOptions();
        $options->setRAG(true)->setWorkflowId($this->workflowId);
        $response = $this->mindeeClient->enqueueAndParse(
            $this->predictionType,
            $this->inputSource,
            $options
        );
        $this->assertNotEmpty(strval($response->document));
        $this->assertNotEmpty($response->document->inference->extras);
        $this->assertNotEmpty($response->document->inference->extras->rag->matchingDocumentId);
    }

    public function testWorkflowPollingWithoutRAG() {
        $options = new PredictMethodOptions();
        $options->setWorkflowId($this->workflowId);
        $response = $this->mindeeClient->enqueueAndParse(
            $this->predictionType,
            $this->inputSource,
            $options
        );
        $this->assertNotEmpty(strval($response->document));
        $this->assertObjectHasProperty('rag', $response->document->inference->extras);
        $this->assertFalse(isset($response->document->inference->extras->rag));
    }
}

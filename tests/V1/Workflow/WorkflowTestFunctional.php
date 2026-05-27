<?php

declare(strict_types=1);

namespace V1\Workflow;

use Mindee\Input\PathInput;
use Mindee\V1\Client;
use Mindee\V1\ClientOptions\PredictMethodOptions;
use Mindee\V1\ClientOptions\WorkflowOptions;
use Mindee\V1\Product\FinancialDocument\FinancialDocumentV1;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

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
        $this->inputSource = new PathInput(
            TestingUtilities::getV1DataDir() . "/products/financial_document/default_sample.jpg"
        );
    }

    public function testWorkflow(): void
    {
        $currentDateTime = date('Y-m-d-H:i:s');
        $options = new WorkflowOptions(
            "php-" . $currentDateTime,
            "low",
            false,
            null,
            true
        );
        $response = $this->mindeeClient->executeWorkflow(
            $this->inputSource,
            $this->workflowId,
            $options
        );
        self::assertSame(202, $response->apiRequest->statusCode);
        self::assertSame("php-$currentDateTime", $response->execution->file->alias);
        self::assertSame("low", $response->execution->priority);
    }

    public function testWorkflowPollingWithRAG(): void
    {
        $options = new PredictMethodOptions();
        $options->setRAG(true)->setWorkflowId($this->workflowId);
        $response = $this->mindeeClient->enqueueAndParse(
            $this->predictionType,
            $this->inputSource,
            $options
        );
        self::assertNotEmpty((string) ($response->document));
        self::assertNotEmpty($response->document->inference->extras);
        self::assertNotEmpty($response->document->inference->extras->rag->matchingDocumentId);
    }

    public function testWorkflowPollingWithoutRAG(): void
    {
        $options = new PredictMethodOptions();
        $options->setWorkflowId($this->workflowId);
        $response = $this->mindeeClient->enqueueAndParse(
            $this->predictionType,
            $this->inputSource,
            $options
        );
        self::assertNotEmpty((string) ($response->document));
        self::assertObjectHasProperty('rag', $response->document->inference->extras);
        self::assertFalse(isset($response->document->inference->extras->rag));
    }
}

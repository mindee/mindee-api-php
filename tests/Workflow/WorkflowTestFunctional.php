<?php

namespace Workflow;

use Mindee\Client;
use Mindee\Input\WorkflowOptions;
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . "/../TestingUtilities.php");

class WorkflowTestFunctional extends TestCase
{
    private $workflowId;
    private $mindeeClient;

    protected function setUp(): void
    {
        $this->mindeeClient = new Client();
        $this->workflowId = getenv('WORKFLOW_ID') ?: '';
    }

    public function testWorkflow() {
        $inputSource = $this->mindeeClient->sourceFromPath(
            (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/financial_document/default_sample.jpg"
        );
        $currentDateTime = date('Y-m-d-H:i:s');
        $options = new WorkflowOptions(
            "php-" . $currentDateTime,
            "low",
            false,
            null,
            true
        );
        $response = $this->mindeeClient->executeWorkflow($inputSource, $this->workflowId, $options);
        $this->assertEquals(202, $response->apiRequest->statusCode);
        $this->assertEquals("php-$currentDateTime", $response->execution->file->alias);
        $this->assertEquals("low", $response->execution->priority);
    }
}

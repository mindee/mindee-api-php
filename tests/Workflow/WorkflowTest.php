<?php

namespace Workflow;

use Mindee\Parsing\Common\WorkflowResponse;
use Mindee\Product\Generated\GeneratedV1;
use PHPUnit\Framework\TestCase;

class WorkflowTest extends TestCase
{
    private string $findocSamplePath;
    private string $workflowDir;

    protected function setUp(): void
    {
        $this->findocSamplePath = (
            getenv('GITHUB_WORKSPACE') ?: "."
            ) . "/tests/resources/products/financial_document/default_sample.jpg";
        $this->workflowDir = (
            getenv('GITHUB_WORKSPACE') ?: "."
            ) . "/tests/resources/workflows/";
    }

    public function testDeserializeWorkflow()
    {
        $json = file_get_contents($this->workflowDir . "success.json");
        $constructedWorkflow = new WorkflowResponse(GeneratedV1::class, json_decode($json, true));
        $this->assertNotNull($constructedWorkflow);
        $this->assertNotNull($constructedWorkflow->apiRequest);
        $this->assertNull($constructedWorkflow->execution->batchName);
        $this->assertNull($constructedWorkflow->execution->createdAt);
        $this->assertNull($constructedWorkflow->execution->file->alias);
        $this->assertEquals("default_sample.jpg", $constructedWorkflow->execution->file->name);
        $this->assertEquals(
            "8c75c035-e083-4e77-ba3b-7c3598bd1d8a",
            $constructedWorkflow->execution->id
        );
        $this->assertNull($constructedWorkflow->execution->inference);
        $this->assertEquals("medium", $constructedWorkflow->execution->priority);
        $this->assertNull($constructedWorkflow->execution->reviewedAt);
        $this->assertNull($constructedWorkflow->execution->reviewedPrediction);
        $this->assertEquals("processing", $constructedWorkflow->execution->status);
        $this->assertEquals("manual", $constructedWorkflow->execution->type);
        $this->assertEquals(
            "2024-11-13T13:02:31.699190",
            $constructedWorkflow->execution->uploadedAt->format('Y-m-d\TH:i:s.u')
        );
        $this->assertEquals(
            "07ebf237-ff27-4eee-b6a2-425df4a5cca6",
            $constructedWorkflow->execution->workflowId
        );
    }

    public function testDeserializeWorkflowWithPriorityAndAlias()
    {
        $json = file_get_contents($this->workflowDir . "success_low_priority.json");
        $constructedWorkflow = new WorkflowResponse(GeneratedV1::class, json_decode($json, true));
        $this->assertNotNull($constructedWorkflow);
        $this->assertNotNull($constructedWorkflow->apiRequest);
        $this->assertNull($constructedWorkflow->execution->batchName);
        $this->assertNull($constructedWorkflow->execution->createdAt);
        $this->assertEquals(
            "low-priority-sample-test",
            $constructedWorkflow->execution->file->alias
        );
        $this->assertEquals("default_sample.jpg", $constructedWorkflow->execution->file->name);
        $this->assertEquals(
            "b743e123-e18c-4b62-8a07-811a4f72afd3",
            $constructedWorkflow->execution->id
        );
        $this->assertNull($constructedWorkflow->execution->inference);
        $this->assertEquals("low", $constructedWorkflow->execution->priority);
        $this->assertNull($constructedWorkflow->execution->reviewedAt);
        $this->assertNull($constructedWorkflow->execution->reviewedPrediction);
        $this->assertEquals("processing", $constructedWorkflow->execution->status);
        $this->assertEquals("manual", $constructedWorkflow->execution->type);
        $this->assertEquals(
            "2024-11-13T13:17:01.315179",
            $constructedWorkflow->execution->uploadedAt->format('Y-m-d\TH:i:s.u')
        );
        $this->assertEquals(
            "07ebf237-ff27-4eee-b6a2-425df4a5cca6",
            $constructedWorkflow->execution->workflowId
        );
    }
}

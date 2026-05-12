<?php

declare(strict_types=1);

namespace V1\Workflow;

use Mindee\V1\Parsing\Common\WorkflowResponse;
use Mindee\V1\Product\Generated\GeneratedV1;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class WorkflowTest extends TestCase
{
    private string $findocSamplePath;
    private string $workflowDir;

    protected function setUp(): void
    {
        $this->findocSamplePath = (
            TestingUtilities::getV1DataDir() . "/products/financial_document/default_sample.jpg"
        );
        $this->workflowDir = (
            TestingUtilities::getV1DataDir() . "/workflows/"
        );
    }

    public function testDeserializeWorkflow(): void
    {
        $json = file_get_contents($this->workflowDir . "success.json");
        $constructedWorkflow = new WorkflowResponse(GeneratedV1::class, json_decode($json, true));
        self::assertNotNull($constructedWorkflow);
        self::assertNotNull($constructedWorkflow->apiRequest);
        self::assertNull($constructedWorkflow->execution->batchName);
        self::assertNull($constructedWorkflow->execution->createdAt);
        self::assertNull($constructedWorkflow->execution->file->alias);
        self::assertSame("default_sample.jpg", $constructedWorkflow->execution->file->name);
        self::assertSame(
            "8c75c035-e083-4e77-ba3b-7c3598bd1d8a",
            $constructedWorkflow->execution->id
        );
        self::assertNull($constructedWorkflow->execution->inference);
        self::assertSame("medium", $constructedWorkflow->execution->priority);
        self::assertNull($constructedWorkflow->execution->reviewedAt);
        self::assertNull($constructedWorkflow->execution->reviewedPrediction);
        self::assertSame("processing", $constructedWorkflow->execution->status);
        self::assertSame("manual", $constructedWorkflow->execution->type);
        self::assertSame(
            "2024-11-13T13:02:31.699190",
            $constructedWorkflow->execution->uploadedAt->format('Y-m-d\TH:i:s.u')
        );
        self::assertSame(
            "07ebf237-ff27-4eee-b6a2-425df4a5cca6",
            $constructedWorkflow->execution->workflowId
        );
    }

    public function testDeserializeWorkflowWithPriorityAndAlias(): void
    {
        $json = file_get_contents($this->workflowDir . "success_low_priority.json");
        $constructedWorkflow = new WorkflowResponse(GeneratedV1::class, json_decode($json, true));
        self::assertNotNull($constructedWorkflow);
        self::assertNotNull($constructedWorkflow->apiRequest);
        self::assertNull($constructedWorkflow->execution->batchName);
        self::assertNull($constructedWorkflow->execution->createdAt);
        self::assertSame(
            "low-priority-sample-test",
            $constructedWorkflow->execution->file->alias
        );
        self::assertSame("default_sample.jpg", $constructedWorkflow->execution->file->name);
        self::assertSame(
            "b743e123-e18c-4b62-8a07-811a4f72afd3",
            $constructedWorkflow->execution->id
        );
        self::assertNull($constructedWorkflow->execution->inference);
        self::assertSame("low", $constructedWorkflow->execution->priority);
        self::assertNull($constructedWorkflow->execution->reviewedAt);
        self::assertNull($constructedWorkflow->execution->reviewedPrediction);
        self::assertSame("processing", $constructedWorkflow->execution->status);
        self::assertSame("manual", $constructedWorkflow->execution->type);
        self::assertSame(
            "2024-11-13T13:17:01.315179",
            $constructedWorkflow->execution->uploadedAt->format('Y-m-d\TH:i:s.u')
        );
        self::assertSame(
            "07ebf237-ff27-4eee-b6a2-425df4a5cca6",
            $constructedWorkflow->execution->workflowId
        );
    }
}

<?php

namespace Mindee\Input;

use Mindee\Http\BaseEndpoint;
use Mindee\Http\Endpoint;
use Mindee\Http\WorkflowEndpoint;

/**
 * Handles options tied to prediction method.
 */
class PredictMethodOptions
{
    /**
     * @var PredictOptions Prediction options.
     */
    public PredictOptions $predictOptions;
    /**
     * @var WorkflowOptions Workflow options.
     */
    public WorkflowOptions $workflowOptions;
    /**
     * @var PageOptions Page options.
     */
    public PageOptions $pageOptions;
    /**
     * @var Endpoint|WorkflowEndpoint|null Endpoint.
     */
    public $endpoint;

    /**
     * @var boolean If set, will enable Retrieval-Augmented Generation (only works if a valid WorkflowId is set).
     */
    public bool $rag;

    /**
     * @var string|null Workflow ID.
     */
    public ?string $workflowId;

    /**
     * Prediction method options.
     */
    public function __construct()
    {
        $this->predictOptions = new PredictOptions();
        $this->pageOptions = new PageOptions();
        $this->endpoint = null;
        $this->rag = false;
        $this->workflowId = null;
    }

    /**
     * @param PredictOptions $predictOptions Prediction Options.
     * @return $this
     */
    public function setPredictOptions(PredictOptions $predictOptions): PredictMethodOptions
    {
        $this->predictOptions = $predictOptions;
        return $this;
    }

    /**
     * @param WorkflowOptions $workflowOptions Prediction Options.
     * @return $this
     */
    public function setWorkflowOptions(WorkflowOptions $workflowOptions): PredictMethodOptions
    {
        $this->workflowOptions = $workflowOptions;
        return $this;
    }

    /**
     * @param PageOptions $pageOptions Page Options.
     * @return $this
     */
    public function setPageOptions(PageOptions $pageOptions): PredictMethodOptions
    {
        $this->pageOptions = $pageOptions;
        return $this;
    }

    /**
     * @param Endpoint $customEndpoint Endpoint.
     * @return $this
     */
    public function setEndpoint(Endpoint $customEndpoint): PredictMethodOptions
    {
        $this->endpoint = $customEndpoint;
        return $this;
    }

    /**
     * @param boolean $rag Whether to enable Retrieval-Augmented Generation.
     * @return $this
     */
    public function setRag(bool $rag): PredictMethodOptions
    {
        $this->rag = $rag;
        return $this;
    }

    /**
     * Sets the workflow ID for the prediction method options.
     *
     * @param string $workflowId The unique workflow ID to be set.
     * @return $this
     */
    public function setWorkflowId(string $workflowId): PredictMethodOptions
    {
        $this->workflowId = $workflowId;
        return $this;
    }
}

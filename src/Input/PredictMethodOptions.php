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
     * @var boolean Whether to close the file after parsing it.
     */
    public bool $closeFile;


    /**
     * Prediction method options.
     */
    public function __construct()
    {
        $this->predictOptions = new PredictOptions();
        $this->pageOptions = new PageOptions();
        $this->endpoint = null;
        $this->closeFile = false;
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
     * @param boolean $closeFile Close file.
     * @return $this
     */
    public function setCloseFile(bool $closeFile): PredictMethodOptions
    {
        $this->closeFile = $closeFile;
        return $this;
    }
}

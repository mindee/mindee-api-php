<?php

namespace Mindee\Input;

use Mindee\Http\Endpoint;

/**
 * Handles options tied to prediction method.
 */
class PredictMethodOptions
{
    /**
     * @var \Mindee\Input\PredictOptions Prediction options.
     */
    public PredictOptions $predictOptions;
    /**
     * @var \Mindee\Input\PageOptions Page options.
     */
    public PageOptions $pageOptions;
    /**
     * @var \Mindee\Http\Endpoint|null Endpoint.
     */
    public ?Endpoint $endpoint;

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
     * @param \Mindee\Input\PredictOptions $predict_options Prediction Options.
     * @return $this
     */
    public function setPredictOptions(PredictOptions $predict_options): PredictMethodOptions
    {
        $this->predictOptions = $predict_options;
        return $this;
    }

    /**
     * @param \Mindee\Input\PageOptions $page_options Page Options.
     * @return $this
     */
    public function setPageOptions(PageOptions $page_options): PredictMethodOptions
    {
        $this->pageOptions = $page_options;
        return $this;
    }

    /**
     * @param \Mindee\Http\Endpoint $custom_endpoint Endpoint.
     * @return $this
     */
    public function setEndpoint(Endpoint $custom_endpoint): PredictMethodOptions
    {
        $this->endpoint = $custom_endpoint;
        return $this;
    }

    /**
     * @param boolean $close_file Close file.
     * @return $this
     */
    public function setCloseFile(bool $close_file): PredictMethodOptions
    {
        $this->closeFile = $close_file;
        return $this;
    }
}

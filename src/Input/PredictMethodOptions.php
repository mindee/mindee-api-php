<?php

namespace Mindee\Input;

use Mindee\Http\Endpoint;

class PredictMethodOptions
{
    public PredictOptions $predictOptions;
    public PageOptions $pageOptions;
    public ?Endpoint $customEndpoint;

    public bool $closeFile;


    function __construct()
    {
        $this->predictOptions = new PredictOptions();
        $this->pageOptions = new PageOptions();
        $this->customEndpoint = null;
        $this->closeFile = false;
    }

    public function setPredictOptions(PredictOptions $predict_options): PredictMethodOptions
    {
        $this->predictOptions = $predict_options;
        return $this;
    }

    public function setPageOptions(PageOptions $page_options): PredictMethodOptions
    {
        $this->pageOptions = $page_options;
        return $this;
    }

    public function setCustomEndpoint(Endpoint $custom_endpoint): PredictMethodOptions
    {
        $this->customEndpoint = $custom_endpoint;
        return $this;
    }

    public function setCloseFile(bool $close_file): PredictMethodOptions
    {
        $this->closeFile = $close_file;
        return $this;
    }

}
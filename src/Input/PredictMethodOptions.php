<?php

namespace Mindee\Input;

use Mindee\Http\Endpoint;

class PredictMethodOptions
{
    public PredictOptions $predictOptions;
    public PageOptions $pageOptions;
    public ?Endpoint $endpoint;

    public bool $closeFile;


    public function __construct()
    {
        $this->predictOptions = new PredictOptions();
        $this->pageOptions = new PageOptions();
        $this->endpoint = null;
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

    public function setEndpoint(Endpoint $custom_endpoint): PredictMethodOptions
    {
        $this->endpoint = $custom_endpoint;
        return $this;
    }

    public function setCloseFile(bool $close_file): PredictMethodOptions
    {
        $this->closeFile = $close_file;
        return $this;
    }
}

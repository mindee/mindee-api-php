<?php

namespace Mindee\Input;

class PredictOptions
{
    public bool $include_words;
    public bool $cropper;

    public function __construct()
    {
        $this->include_words = false;
        $this->cropper = false;
    }

    public function setIncludeWords(bool $include_words): PredictOptions
    {
        $this->include_words = $include_words;
        return $this;
    }
    public function setCropper(bool $cropper): PredictOptions
    {
        $this->cropper = $cropper;
        return $this;
    }
}

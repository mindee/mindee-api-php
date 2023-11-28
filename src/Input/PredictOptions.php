<?php

namespace Mindee\Input;

/**
 * Handles options tied to Predictions.
 */
class PredictOptions
{
    /**
     * @var boolean Whether to include the full text for each page.
     * This performs a full OCR operation on the server and will increase response time.
     */
    public bool $include_words;
    /**
     * @var boolean Whether to include cropper results for each page.
     * This performs a cropping operation on the server and will increase response time.
     */
    public bool $cropper;

    /**
     * Prediction options.
     */
    public function __construct()
    {
        $this->include_words = false;
        $this->cropper = false;
    }

    /**
     * @param boolean $include_words Whether to include the full text.
     * @return $this
     */
    public function setIncludeWords(bool $include_words): PredictOptions
    {
        $this->include_words = $include_words;
        return $this;
    }

    /**
     * @param boolean $cropper Whether to include the Cropper.
     * @return $this
     */
    public function setCropper(bool $cropper): PredictOptions
    {
        $this->cropper = $cropper;
        return $this;
    }
}

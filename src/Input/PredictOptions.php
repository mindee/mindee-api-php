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
    public bool $includeWords;
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
        $this->includeWords = false;
        $this->cropper = false;
    }

    /**
     * @param boolean $includeWords Whether to include the full text.
     * @return $this
     */
    public function setIncludeWords(bool $includeWords): PredictOptions
    {
        $this->includeWords = $includeWords;
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

<?php

namespace Mindee\Input;

/**
 * Handles options tied to Predictions.
 */
class PredictOptions extends CommonOptions
{
    /**
     * @var boolean Whether to include the full text for each page.
     * This performs a full OCR operation on the server and will increase response time.
     */
    public bool $includeWords;

    /**
     * @var boolean Whether to include cropper results for each page.
     * This performs a cropping operation on the server and may increase response time.
     */
    public bool $cropper;

    /**
     * Prediction options.
     * @param boolean $fullText     Whether to include the full OCR text response in compatible APIs.
     *          This performs a full OCR operation on the server and will increase response time.
     * @param boolean $includeWords Whether to include the full text for each page.
     *     This performs a full OCR operation on the server and will increase response time.
     * @param boolean $cropper      Whether to include cropper results for each page.
     *          This performs a cropping operation on the server and may increase response time.
     */
    public function __construct(
        bool $fullText = false,
        bool $includeWords = false,
        bool $cropper = false
    ) {
        parent::__construct($fullText);
        $this->includeWords = $includeWords;
        $this->cropper = $cropper;
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

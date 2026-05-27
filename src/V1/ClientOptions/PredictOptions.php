<?php

declare(strict_types=1);

namespace Mindee\V1\ClientOptions;

/**
 * Handles options tied to Predictions.
 */
class PredictOptions extends CommonOptions
{
    /**
     * Prediction options.
     * @param boolean $fullText Whether to include the full Ocr text response in compatible APIs.
     *                          This performs a full Ocr operation on the server and will increase response time.
     * @param boolean $includeWords Whether to include the full text for each page.
     *                              This performs a full Ocr operation on the server and will increase response time.
     * @param boolean $cropper Whether to include cropper results for each page.
     *                         This performs a cropping operation on the server and may increase response time.
     */
    public function __construct(
        bool $fullText = false,
        public bool $includeWords = false,
        public bool $cropper = false
    ) {
        parent::__construct($fullText);
    }

    /**
     * @param boolean $includeWords Whether to include the full text.
     * @return $this
     */
    public function setIncludeWords(bool $includeWords): self
    {
        $this->includeWords = $includeWords;
        return $this;
    }

    /**
     * @param boolean $cropper Whether to include the Cropper.
     * @return $this
     */
    public function setCropper(bool $cropper): self
    {
        $this->cropper = $cropper;
        return $this;
    }
}

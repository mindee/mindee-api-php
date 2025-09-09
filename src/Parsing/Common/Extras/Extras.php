<?php

namespace Mindee\Parsing\Common\Extras;

use function PHPUnit\Framework\isEmpty;

/**
 * Extras collection wrapper class.
 *
 * Is roughly equivalent to an array of Extras, with a bit more utility.
 */
class Extras
{
    /**
     * @var CropperExtra|null Cropper extra.
     */
    public ?CropperExtra $cropper;
    /**
     * @var FullTextOcrExtra|null Full text OCR extra.
     */
    public ?FullTextOcrExtra $fullTextOcr;
    /**
     * @var RagExtra|null Rag Extra.
     */
    public ?RagExtra $rag;
    /**
     * @var array Other extras.
     */
    private array $data;

    /**
     * Sets a field.
     *
     * @param string $varName Name of the field to set.
     * @param mixed  $value   Value to set the field with.
     * @return void
     */
    public function __set(string $varName, mixed $value)
    {
        $this->data[$varName] = $value;
    }

    /**
     * @param array $rawPrediction Raw prediction array.
     */
    public function __construct(array $rawPrediction)
    {
        foreach ($rawPrediction as $key => $extra) {
            if ($key == 'cropper' && isset($rawPrediction['cropper'])) {
                $this->cropper = new CropperExtra($rawPrediction['cropper']);
            } elseif ($key == 'full_text_ocr' && isset($rawPrediction['full_text_ocr'])) {
                $this->fullTextOcr = new FullTextOcrExtra($rawPrediction['full_text_ocr']);
            } elseif ($key = 'rag' && isset($rawPrediction['rag'])) {
                $this->rag = new RAGExtra($rawPrediction['rag']);
            } else {
                $this->__set($key, $extra);
            }
        }
    }

    /**
     * Adds artificial extra data for reconstructed extras.
     * Currently only used for full_text_ocr.
     *
     * @param array $rawPrediction Raw HTTP response.
     * @return void
     */
    public function addArtificialExtra(array $rawPrediction)
    {
        if (isset($rawPrediction["full_text_ocr"]) && !isEmpty($rawPrediction['full_text_ocr'])) {
            $this->fullTextOcr = new FullTextOcrExtra($rawPrediction['full_text_ocr']);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $resStr = '';
        foreach ($this->data as $key => $extra) {
            $resStr .= $key . ': ' . $extra;
            $resStr .= "\n";
        }
        if ($this->cropper) {
            $resStr .= ":cropper:" . $this->cropper . "\n";
        }
        if ($this->fullTextOcr) {
            $resStr .= ":full_text_ocr:" . $this->fullTextOcr . "\n";
        }
        if ($this->rag) {
            $resStr .= ":rag:" . $this->rag . "\n";
        }
        return $resStr;
    }
}

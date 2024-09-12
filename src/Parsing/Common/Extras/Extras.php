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
     * @var \Mindee\Parsing\Common\Extras\CropperExtra|null Cropper extra.
     */
    public ?CropperExtra $cropper;
    /**
     * @var \Mindee\Parsing\Common\Extras\CropperExtra|null Cropper extra.
     */
    public ?FullTextOcrExtra $fullTextOcr;
    /**
     * @var array Other extras.
     */
    private array $data;


    /**
     * Sets a field.
     *
     * @param mixed $varName Name of the field to set.
     * @param mixed $value   Value to set the field with.
     * @return void
     */
    public function __set($varName, $value)
    {
        $this->data[$varName] = $value;
    }

    /**
     * @param array $rawPrediction Raw prediction array.
     */
    public function __construct(array $rawPrediction)
    {
        foreach ($rawPrediction as $key => $extra) {
            if ($key != 'cropper') {
                $this->__set($key, $extra);
            } else {
                $this->cropper = new CropperExtra($rawPrediction['cropper']);
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
        if (isset($rawPrediction["full_text_ocr"]) && !isEmpty($rawPrediction['$rawPrediction'])) {
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
            $resStr .= ":cropper:" . strval($this->cropper) . "\n";
        }
        if ($this->fullTextOcr) {
            $resStr .= ":full_text_ocr:" . strval($this->fullTextOcr) . "\n";
        }
        return $resStr;
    }
}

<?php

namespace Mindee\Parsing\Common\Extras;

use Mindee\Parsing\Common\Extras\CropperExtra;

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
     * @var array Other extras.
     */
    private array $data;


    /**
     * Sets a field.
     *
     * @param mixed $varName Name of the field to set.
     * @param mixed $value Value to set the field with.
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
     * @return string String representation.
     */
    public function __toString(): string
    {
        return implode('', $this->data);
    }
}

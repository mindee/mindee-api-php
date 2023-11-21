<?php

namespace Mindee\Parsing\Common\Extras;

use Mindee\Parsing\Common\Extras\CropperExtra;

class Extras
{
    public ?CropperExtra $cropper;
    private array $data;


    public function __set($varName, $value)
    {
        $this->data[$varName] = $value;
    }

    public function __construct($raw_prediction)
    {
        foreach ($raw_prediction as $key => $extra) {
            if ($key != 'cropper') {
                $this->__set($key, $extra);
            } else {
                $this->__set('cropper', new CropperExtra($raw_prediction['cropper']));
            }
        }
    }

    public function __toString(): string
    {
        return implode('', $this->data);
    }
}

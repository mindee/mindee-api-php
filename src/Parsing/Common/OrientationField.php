<?php

namespace Mindee\Parsing\Common;

use Mindee\Parsing\Standard\BaseField;

/**
 * The clockwise rotation to apply (in degrees) to make the image upright.
 */
class OrientationField extends BaseField
{
    /**
     * @var integer Degrees as an integer.
     */
    public $value;

    /**
     * @param array        $raw_prediction Raw prediction array.
     * @param integer|null $page_id        Page number for multi pages PDF.
     * @param boolean      $reconstructed  Whether the field was reconstructed.
     * @param string       $value_key      Key to use for the value.
     */
    public function __construct(
        array $raw_prediction,
        ?int $page_id = null,
        bool $reconstructed = false,
        string $value_key = 'value'
    ) {
        parent::__construct($raw_prediction, $page_id, $reconstructed, $value_key);
        $this->value = 0;
        if (array_key_exists($value_key, $raw_prediction) && is_numeric($raw_prediction[$value_key])) {
            $this->value = intval($raw_prediction[$value_key]);
            if (!in_array($this->value, [0, 90, 180, 270])) {
                $this->value = 0;
            }
        }
    }
}

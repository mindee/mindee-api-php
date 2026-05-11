<?php

namespace Mindee\V1\Parsing\Common;

use Mindee\V1\Parsing\Standard\BaseField;

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
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     * @param boolean      $reconstructed Whether the field was reconstructed.
     * @param string       $valueKey      Key to use for the value.
     */
    public function __construct(
        array $rawPrediction,
        ?int $pageId = null,
        bool $reconstructed = false,
        string $valueKey = 'value'
    ) {
        parent::__construct($rawPrediction, $pageId, $reconstructed, $valueKey);
        $this->value = 0;
        if (array_key_exists($valueKey, $rawPrediction) && is_numeric($rawPrediction[$valueKey])) {
            $this->value = intval($rawPrediction[$valueKey]);
            if (!in_array($this->value, [0, 90, 180, 270])) {
                $this->value = 0;
            }
        }
    }
}

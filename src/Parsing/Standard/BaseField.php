<?php

namespace Mindee\Parsing\Standard;

use Mindee\Geometry\BBox;
use Mindee\Geometry\Point;
use Mindee\Geometry\Polygon;

use function Mindee\Geometry\createBoundingBoxFrom;

/**
 * Base class for most fields.
 */
abstract class BaseField
{
    use FieldConfidenceMixin;

    /**
     * @var mixed|null Raw field value.
     */
    public $value;
    /**
     * @var boolean Whether the field was reconstructed from other fields.
     */
    public bool $reconstructed;
    /**
     * @var integer|mixed|null The document page on which the information was found.
     */
    public ?int $pageId;

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
        if (!isset($page_id) && (array_key_exists('page_id', $raw_prediction) && isset($raw_prediction['page_id']))) {
            $this->pageId = $raw_prediction['page_id'];
        } else {
            $this->pageId = $page_id;
        }
        $this->reconstructed = $reconstructed;
        if (array_key_exists($value_key, $raw_prediction) && $raw_prediction[$value_key] != 'N/A') {
            $this->value = $raw_prediction[$value_key];
            $this->setConfidence($raw_prediction);
        } else {
            $this->value = null;
        }
    }

    /**
     * Compares with the value of another field.
     *
     * @param \Mindee\Parsing\Standard\BaseField $obj Field to compare.
     * @return boolean
     */
    public function __compare(BaseField $obj): bool
    {
        return $this->value == $obj->value;
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return isset($this->value) ? strval($this->value) : '';
    }
}

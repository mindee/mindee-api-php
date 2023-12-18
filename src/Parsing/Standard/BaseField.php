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
        if (!isset($pageId) && (array_key_exists('page_id', $rawPrediction) && isset($rawPrediction['page_id']))) {
            $this->pageId = $rawPrediction['page_id'];
        } else {
            $this->pageId = $pageId;
        }
        $this->reconstructed = $reconstructed;
        if (array_key_exists($valueKey, $rawPrediction) && $rawPrediction[$valueKey] != 'N/A') {
            $this->value = $rawPrediction[$valueKey];
            $this->setConfidence($rawPrediction);
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

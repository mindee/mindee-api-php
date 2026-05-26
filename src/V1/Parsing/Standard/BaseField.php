<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Standard;

use Mindee\Geometry\Polygon;

use function array_key_exists;

/**
 * Base class for most fields.
 * @template T Generic typing for value type handling.
 */
abstract class BaseField
{
    use FieldConfidenceMixin;

    /**
     * @var T|null Raw field value.
     */
    public mixed $value;
    /**
     * @var boolean Whether the field was reconstructed from other fields.
     */
    public bool $reconstructed;
    /**
     * @var integer|null The document page on which the information was found.
     */
    public ?int $pageId;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     * @param integer|null $pageId Page number for multi pages document.
     * @param boolean $reconstructed Whether the field was reconstructed.
     * @param string $valueKey Key to use for the value.
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
        if (array_key_exists($valueKey, $rawPrediction) && $rawPrediction[$valueKey] !== 'N/A') {
            $this->value = $rawPrediction[$valueKey];
            $this->setConfidence($rawPrediction);
        } else {
            $this->value = null;
        }
    }

    /**
     * Compares with the value of another field.
     *
     * @param BaseField<string|float|integer|boolean|Polygon> $obj Field to compare.
     * @return boolean
     */
    public function __compare(self $obj): bool
    {
        return $this->value === $obj->value;
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return isset($this->value) ? (string) ($this->value) : '';
    }
}

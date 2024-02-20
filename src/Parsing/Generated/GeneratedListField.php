<?php

namespace Mindee\Parsing\Generated;

use Mindee\Parsing\Standard\StringField;

/**
 * A list of value or words for generated APIs.
 */
class GeneratedListField
{
    /** @var integer|null Id of the page the object was found on */
    private ?int $pageId;

    /** @var array List of values */
    private array $values = [];

    /**
     * Constructor.
     *
     * @param array        $rawPrediction Raw prediction data.
     * @param integer|null $pageId        Id of the page.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->pageId = $pageId;

        foreach ($rawPrediction as $value) {
            if (isset($value['page_id'])) {
                $this->pageId = $value['page_id'];
            }

            if ($this->isGeneratedObject($value)) {
                $this->values[] = new GeneratedObjectField($value, $this->pageId);
            } else {
                $valueStr = $value;
                if (isset($valueStr['value'])) {
                    $valueStr['value'] = (string) $valueStr['value'];
                }
                $this->values[] = new StringField($valueStr, $this->pageId);
            }
        }
    }

    /**
     * Check if the given value is a generated object.
     *
     * @param mixed $value Value to check.
     * @return boolean Whether the value is a generated object.
     */
    private function isGeneratedObject($value): bool
    {
        // Implement is_generated_object logic here
        // This is a placeholder
        return false;
    }

    /**
     * Get a list of contents.
     *
     * @return array List of contents.
     */
    public function getContentsList(): array
    {
        return array_map(function ($v) {
            return (string) ($v ?: "");
        }, $this->values);
    }

    /**
     * Get a string representation of all values.
     *
     * @param string $separator Separator to use when concatenating fields.
     * @return string String representation of all values.
     */
    public function getContentsString(string $separator = " "): string
    {
        return implode($separator, $this->getContentsList());
    }

    /**
     * Get a string representation of the object.
     *
     * @return string String representation of the object.
     */
    public function __toString(): string
    {
        return $this->getContentsString();
    }
}

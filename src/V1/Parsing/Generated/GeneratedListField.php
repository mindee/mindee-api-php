<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Generated;

use Mindee\V1\Parsing\Standard\StringField;
use Stringable;

use function is_float;
use function is_int;

/**
 * A list of value or words for generated APIs.
 */
class GeneratedListField implements Stringable
{
    /** @var array<GeneratedObjectField|StringField> List of values */
    public array $values = [];

    /**
     * Constructor.
     *
     * @param list<array<string, integer|float|string|bool|null|array<mixed>>> $rawPrediction Array containing the list elements.
     * @param integer|null $pageId ID of the page.
     */
    public function __construct(array $rawPrediction, public ?int $pageId = null)
    {
        foreach ($rawPrediction as $value) {
            if (isset($value['page_id']) && is_int($value['page_id'])) {
                $this->pageId = $value['page_id'];
            }

            if (GeneratedObjectField::isGeneratedObject($value)) {
                $this->values[] = new GeneratedObjectField($value, $this->pageId);
            } else {
                $valueStr = $value;
                if (isset($valueStr['value'])) {
                    if (
                        (
                            is_int($valueStr['value'])
                            || (is_float($valueStr['value']) && floor($valueStr['value']) === $valueStr['value'])
                        )
                        && (float) $value['value'] !== 0.0
                    ) {
                        $valueStr['value'] = $value['value'] . ".0";
                    } else {
                        $valueStr['value'] = (string) ($value['value']);
                    }
                }
                $this->values[] = new StringField($valueStr, $this->pageId);
            }
        }
    }

    /**
     * Get a list of contents.
     *
     * @return array<string> List of contents.
     */
    public function getContentsList(): array
    {
        return array_map(static fn($v) => (string) $v, $this->values);
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

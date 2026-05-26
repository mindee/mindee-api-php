<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Inference\Field;

use Mindee\Error\MindeeApiException;

use function array_key_exists;
use function is_array;
use function sprintf;

/**
 * A field containing a list of other fields.
 */
class ListField extends BaseField
{
    /**
     * @var array<BaseField|null> Items contained in the list.
     */
    public array $items;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     * @param integer $indentLevel Level of indentation for rst display.
     * @throws MindeeApiException Throws if deserialization fails.
     */
    public function __construct(array $rawResponse, int $indentLevel = 0)
    {
        parent::__construct($rawResponse, $indentLevel);

        if (!array_key_exists('items', $rawResponse) || !is_array($rawResponse['items'])) {
            throw new MindeeApiException(
                sprintf('Expected "items" to be an array in %s.', json_encode($rawResponse))
            );
        }

        $this->items = [];
        foreach ($rawResponse['items'] as $item) {
            $this->items[] = BaseField::createField($item, $indentLevel + 1);
        }
    }

    /**
     */
    public function __toString(): string
    {
        if (empty($this->items)) {
            return "\n";
        }

        $parts = [''];
        foreach ($this->items as $item) {
            if (null === $item) {
                continue;
            }

            if ($item instanceof ObjectField) {
                $parts[] = $item->toStringFromList();
            } else {
                $parts[] = $item->__toString();
            }
        }

        return implode("\n  * ", $parts);
    }
}

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
     * @var array<SimpleField|ObjectField|ListField> Items contained in the list, prefer getSimpleItems() or getObjectItems().
     */
    public array $items;

    /**
     * @var array<SimpleField>|null Cached list of simple field items.
     */
    private ?array $simpleItemsCache = null;

    /**
     * @var array<ObjectField>|null Cached list of object field items.
     */
    private ?array $objectItemsCache = null;

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
     * List of simple fields.
     *
     * @return array<SimpleField>
     */
    public function getSimpleItems(): array
    {
        if ($this->simpleItemsCache !== null) {
            return $this->simpleItemsCache;
        }

        $this->simpleItemsCache = [];
        foreach ($this->items as $item) {
            if ($item instanceof SimpleField) {
                $this->simpleItemsCache[] = $item;
            }
        }

        return $this->simpleItemsCache;
    }

    /**
     * List of object fields.
     *
     * @return array<ObjectField>
     */
    public function getObjectItems(): array
    {
        if ($this->objectItemsCache !== null) {
            return $this->objectItemsCache;
        }

        $this->objectItemsCache = [];
        foreach ($this->items as $item) {
            if ($item instanceof ObjectField) {
                $this->objectItemsCache[] = $item;
            }
        }

        return $this->objectItemsCache;
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
            if ($item instanceof ObjectField) {
                $parts[] = $item->toStringFromList();
            } else {
                $parts[] = $item->__toString();
            }
        }

        return implode("\n  * ", $parts);
    }
}

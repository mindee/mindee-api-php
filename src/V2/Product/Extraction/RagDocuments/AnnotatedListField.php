<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Extraction\RagDocuments;

/**
 * A ListField with additional configuration for annotation.
 */
class AnnotatedListField extends AnnotatedBaseField
{
    /**
     * @var array<AnnotatedSimpleField|AnnotatedObjectField|AnnotatedListField> List of fields, prefer getSimpleItems() or getObjectItems().
     */
    public array $items = [];

    /**
     * @var array<AnnotatedSimpleField>|null Cached list of simple field items.
     */
    private ?array $simpleItemsCache = null;

    /**
     * @var array<AnnotatedObjectField>|null Cached list of object field items.
     */
    private ?array $objectItemsCache = null;

    /**
     */
    public function __construct(bool $selected, ?string $guidelines)
    {
        parent::__construct($selected, $guidelines);
    }

    /**
     * @param array<string, mixed> $rawResponse Raw server response array.
     */
    public static function fromArray(array $rawResponse): self
    {
        $selected = (bool) ($rawResponse['selected'] ?? false);
        $guidelines = isset($rawResponse['guidelines']) ? (string) $rawResponse['guidelines'] : null;
        $listField = new self($selected, $guidelines);

        foreach ($rawResponse['items'] ?? [] as $itemData) {
            $listField->items[] = AnnotatedBaseField::createField($itemData);
        }

        return $listField;
    }

    /**
     * List of simple fields.
     *
     * @return array<AnnotatedSimpleField>
     */
    public function getSimpleItems(): array
    {
        if ($this->simpleItemsCache !== null) {
            return $this->simpleItemsCache;
        }

        $this->simpleItemsCache = [];
        foreach ($this->items as $item) {
            if ($item instanceof AnnotatedSimpleField) {
                $this->simpleItemsCache[] = $item;
            }
        }

        return $this->simpleItemsCache;
    }

    /**
     * List of object fields.
     *
     * @return array<AnnotatedObjectField>
     */
    public function getObjectItems(): array
    {
        if ($this->objectItemsCache !== null) {
            return $this->objectItemsCache;
        }

        $this->objectItemsCache = [];
        foreach ($this->items as $item) {
            if ($item instanceof AnnotatedObjectField) {
                $this->objectItemsCache[] = $item;
            }
        }

        return $this->objectItemsCache;
    }

    /**
     * @return array<string, mixed> Array representation for serialization.
     */
    public function toArray(): array
    {
        return [
            'selected' => $this->selected,
            'guidelines' => $this->guidelines,
            'items' => array_map(static fn(AnnotatedBaseField $f) => $f->toArray(), $this->items),
        ];
    }
}

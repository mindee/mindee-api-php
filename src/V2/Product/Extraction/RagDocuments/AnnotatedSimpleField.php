<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Extraction\RagDocuments;

use function is_int;

/**
 * A SimpleField with additional configuration for annotation.
 */
class AnnotatedSimpleField extends AnnotatedBaseField
{
    /**
     */
    public function __construct(bool $selected, ?string $guidelines, /**
     * @var string|float|bool|null Field value, one of: string, bool, float, null.
     */
        public string|float|bool|null $value)
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
        $rawValue = $rawResponse['value'] ?? null;

        if (is_int($rawValue)) {
            $rawValue = (float) $rawValue;
        }

        return new self($selected, $guidelines, $rawValue);
    }

    /**
     * @return array<string, mixed> Array representation for serialization.
     */
    public function toArray(): array
    {
        return [
            'selected' => $this->selected,
            'guidelines' => $this->guidelines,
            'value' => $this->value,
        ];
    }
}

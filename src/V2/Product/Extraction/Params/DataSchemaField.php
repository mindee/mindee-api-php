<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Extraction\Params;

/**
 * Data Schema Field.
 */
class DataSchemaField
{
    /**
     * @var string Name of the field in the data schema.
     */
    public string $name;
    /**
     * @var string Display name for the field. Also impacts inference results.
     */
    public string $title;
    /**
     * @var boolean Whether this field can contain multiple values.
     */
    public bool $isArray;
    /**
     * @var string Data type of the field.
     */
    public string $type;
    /**
     * @var string|null Detailed description of what this field represents.
     */
    public ?string $description;
    /**
     * @var string|null Optional extraction guidelines.
     */
    public ?string $guidelines;
    /**
     * @var boolean|null Whether to remove duplicate values in the array.
     */
    public ?bool $uniqueValues;
    /**
     * @var array<string, int|float|string|bool|null|array<array-key, mixed>>|null Subfields when type is `nested_object`. Leave empty for other types.
     */
    public ?array $nestedFields;
    /**
     * @var array<string>|null Allowed values when type is `classification`. Leave empty for other types.
     */
    public ?array $classificationValues;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->name = $rawResponse['name'];
        $this->title = $rawResponse['title'];
        $this->isArray = $rawResponse['is_array'];
        $this->type = $rawResponse['type'];
        $this->description = $rawResponse['description'];
        $this->guidelines = $rawResponse['guidelines'];
        if (isset($rawResponse['unique_values'])) {
            $this->uniqueValues = $rawResponse['unique_values'];
        }
        if (isset($rawResponse['nested_fields'])) {
            $this->nestedFields = $rawResponse['nested_fields'];
        }
        if (isset($rawResponse['classification_values'])) {
            $this->classificationValues = $rawResponse['classification_values'];
        }
    }

    /**
     * @return array<string, int|float|string|bool|null|array<array-key, mixed>> JSON representation.
     */
    public function toJson(): array
    {
        $out = [
            'name' => $this->name,
            'title' => $this->title,
            'is_array' => $this->isArray,
            'type' => $this->type,
        ];
        if (isset($this->description)) {
            $out['description'] = $this->description;
        }
        if (isset($this->guidelines)) {
            $out['guidelines'] = $this->guidelines;
        }
        if (isset($this->uniqueValues)) {
            $out['unique_values'] = $this->uniqueValues;
        }
        if (isset($this->nestedFields)) {
            $out['nested_fields'] = $this->nestedFields;
        }
        if (isset($this->classificationValues)) {
            $out['classification_values'] = $this->classificationValues;
        }
        return $out;
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {

        return json_encode(
            $this->toJson(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }
}

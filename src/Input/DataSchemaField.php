<?php

namespace Mindee\Input;

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
     * @var array|null Subfields when type is `nested_object`. Leave empty for other types.
     */
    public ?array $nestedFields;
    /**
     * @var array|null Allowed values when type is `classification`. Leave empty for other types.
     */
    public ?array $classificationValues;

    /**
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        $this->name = $serverResponse['name'];
        $this->title = $serverResponse['title'];
        $this->isArray = $serverResponse['is_array'];
        $this->type = $serverResponse['type'];
        $this->description = $serverResponse['description'];
        $this->guidelines = $serverResponse['guidelines'];
        if (isset($serverResponse['unique_values'])) {
            $this->uniqueValues = $serverResponse['unique_values'];
        }
        if (isset($serverResponse['nested_fields'])) {
            $this->nestedFields = $serverResponse['nested_fields'];
        }
        if (isset($serverResponse['classification_values'])) {
            $this->classificationValues = $serverResponse['classification_values'];
        }
    }

    /**
     * @return array JSON representation.
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

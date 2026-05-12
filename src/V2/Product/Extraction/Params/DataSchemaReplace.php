<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Extraction\Params;

use InvalidArgumentException;

use function count;
use function is_array;

/**
 * The structure to completely replace the data schema of the model.
 */
class DataSchemaReplace
{
    /**
     * @var DataSchemaField[] Fields to replace in the data schema.
     */
    public array $fields;

    /**
     * @param array $serverResponse Raw server response array.
     * @throws InvalidArgumentException Throws if the fields array is empty or the Data schema is incorrect.
     */
    public function __construct(array $serverResponse)
    {
        if (
            !isset($serverResponse['fields'])
            || !is_array($serverResponse['fields'])
            || count($serverResponse['fields']) === 0
        ) {
            throw new InvalidArgumentException('Data Schema replacement fields cannot be empty.');
        }
        $this->fields = array_map(static fn($field) => new DataSchemaField($field), $serverResponse['fields']);
    }

    /**
     * @return array JSON representation.
     */
    public function toJson(): array
    {
        return [ 'fields' => array_map(static fn($field) => $field->toJson(), $this->fields)];
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

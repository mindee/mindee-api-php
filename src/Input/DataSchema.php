<?php

namespace Mindee\Input;

use InvalidArgumentException;

/**
 * Modify the Data Schema.
 */
class DataSchema
{
    /**
     * @var DataSchemaReplace|null If set, completely replaces the data schema of the model.
     */
    public ?DataSchemaReplace $replace;

    /**
     * @param array|string|DataSchema $dataSchema Raw server response array.
     * @throws InvalidArgumentException Throws if the data schema is invalid.
     */
    public function __construct(DataSchema|array|string $dataSchema)
    {
        if (gettype($dataSchema) == 'string') {
            $jsonData = json_decode($dataSchema, true);
        } elseif (gettype($dataSchema) == 'array') {
            $jsonData = $dataSchema;
        } else {
            if (get_class($dataSchema) == DataSchema::class) {
                $this->replace = $dataSchema->replace;
                return;
            }
            throw new InvalidArgumentException('Unrecognized data schema format.');
        }
        $this->replace = new DataSchemaReplace($jsonData['replace']);
    }

    /**
     * @return array JSON representation.
     */
    public function toJson(): array
    {
        return ['replace' => $this->replace->toJson()];
    }

    /**
     * Doubles the number of spaces in front of each line if it has at least two.
     * @param string $line Line to fix.
     * @return string Fixed line.
     */
    private static function fixLineSpaces(string $line): string
    {
        if (!str_starts_with($line, "  ")) {
            return $line;
        }
        $i = 0;
        foreach (str_split($line) as $char) {
            if ($char == ' ') {
                $i++;
                continue;
            }
            break;
        }
        return substr($line, $i / 2);
    }

    /**
     * Ensures proper spacing in JSON string.
     * @return string Properly spaced JSON string.
     */
    private function toJsonStringProperSpacing(): string
    {
        $jsonStr = json_encode($this->toJson(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $lines = explode("\n", $jsonStr);
        return implode("\n", array_map('self::fixLineSpaces', $lines)) . "\n";
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return $this->toJsonStringProperSpacing();
    }
}

<?php

namespace Mindee\Parsing\V2;

use Mindee\Parsing\V2\Field\InferenceFields;

/**
 * Inference result class.
 */
class InferenceResult
{
    /**
     * @var InferenceFields Fields contained in the inference.
     */
    public InferenceFields $fields;

    /**
     * @var InferenceResultOptions|null Potential options retrieved alongside the inference.
     */
    public ?InferenceResultOptions $options;

    /**
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        $this->fields = new InferenceFields($serverResponse['fields']);
        $this->options = isset($serverResponse['options'])
            ? new InferenceResultOptions($serverResponse['options'])
            : null;
    }

    /**
     * @return string String representation.
     */
    public function toString(): string
    {
        $parts = [
            "Fields",
            "======",
            $this->fields->toString(),
        ];

        if ($this->options) {
            $parts[] = "Options";
            $parts[] = "=======";
            $parts[] = $this->options->toString();
        }

        return implode("\n", $parts);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}

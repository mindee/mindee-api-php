<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Inference;

/**
 * Inference result file class.
 */
class InferenceFile
{
    /**
     * @var string Name of the file.
     */
    public string $name;

    /**
     * @var string|null Optional alias for the file.
     */
    public ?string $alias;

    /**
     * @var integer Page count.
     */
    public int $pageCount;

    /**
     * @var string MIME type.
     */
    public string $mimeType;

    /**
     * @param array<string,mixed> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->name = $rawResponse['name'];
        $this->alias = $rawResponse['alias'];
        $this->pageCount = $rawResponse['page_count'];
        $this->mimeType = $rawResponse['mime_type'];
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return "File\n====\n"
            . ":Name: $this->name\n"
            . ":Alias:" . ($this->alias ? " $this->alias" : '') . "\n"
            . ":Page Count: $this->pageCount\n"
            . ":MIME Type: $this->mimeType";
    }
}

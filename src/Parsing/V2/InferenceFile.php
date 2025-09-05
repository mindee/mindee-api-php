<?php

namespace Mindee\Parsing\V2;

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
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        $this->name = $serverResponse['name'];
        $this->alias = $serverResponse['alias'];
        $this->pageCount = $serverResponse['page_count'];
        $this->mimeType = $serverResponse['mime_type'];
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
            . ":MIME Type: $this->mimeType\n";
    }
}

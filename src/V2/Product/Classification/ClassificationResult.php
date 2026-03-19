<?php

namespace Mindee\V2\Product\Classification;

/**
 * Result of the document classifier inference.
 */
class ClassificationResult
{
    /**
     * @var ClassificationClassifier Classification of document type from the source file.
     */
    public ClassificationClassifier $classification;

    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->classification = new ClassificationClassifier($rawResponse['classification']);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return "Classification\n==============\n" . $this->classification;
    }
}

<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Split;

/**
 * Result of a split utility inference.
 */
class SplitResult
{
    /**
     * @var SplitRange[] A single document as identified when splitting a multi-document source file.
     */
    public array $splits;

    /**
     * @param array<string, mixed> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->splits = array_map(static fn($split) => new SplitRange($split), $rawResponse['splits']);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $splitsStr = implode("\n", $this->splits);

        return "Splits\n======\n$splitsStr";
    }
}

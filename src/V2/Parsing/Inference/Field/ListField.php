<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Inference\Field;

use Mindee\Error\MindeeApiException;

use function array_key_exists;
use function is_array;
use function sprintf;

/**
 * A field containing a list of other fields.
 */
class ListField extends BaseField
{
    /**
     * Items contained in the list.
     *
     * @var ObjectField
     */
    public array $items;

    /**
     * @param array $serverResponse Raw server response array.
     * @param integer $indentLevel Level of indentation for rst display.
     * @throws MindeeApiException Throws if deserialization fails.
     */
    public function __construct(array $serverResponse, int $indentLevel = 0)
    {
        parent::__construct($serverResponse, $indentLevel);

        if (!array_key_exists('items', $serverResponse) || !is_array($serverResponse['items'])) {
            throw new MindeeApiException(
                sprintf('Expected "items" to be an array in %s.', json_encode($serverResponse))
            );
        }

        $this->items = [];
        foreach ($serverResponse['items'] as $item) {
            $this->items[] = BaseField::createField($item, $indentLevel + 1);
        }
    }

    /**
     */
    public function __toString(): string
    {
        if (empty($this->items)) {
            return "\n";
        }

        $parts = [''];
        foreach ($this->items as $item) {
            if ($item === null) {
                continue;
            }

            if ($item instanceof ObjectField) {
                $parts[] = $item->toStringFromList();
            } else {
                $parts[] = $item->__toString();
            }
        }

        return implode("\n  * ", $parts);
    }
}

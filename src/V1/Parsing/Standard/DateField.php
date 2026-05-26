<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Standard;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeAPIException;

/**
 * A field containing a date value.
 * @extends BaseField<string>
 */
class DateField extends BaseField
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var DateTimeImmutable|null Date as a standard object.
     */
    public ?DateTimeImmutable $dateObject;

    /**
     * @var boolean|null Whether the field was computed or retrieved directly from the document.
     */
    public ?bool $isComputed;


    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     * @param integer|null $pageId Page number for multi pages document.
     * @param boolean $reconstructed Whether the field was reconstructed.
     * @param string $valueKey Key to use for the value.
     * @throws MindeeAPIException Throws if the date can't be created from the given value.
     */
    public function __construct(
        array $rawPrediction,
        ?int $pageId = null,
        bool $reconstructed = false,
        string $valueKey = 'value'
    ) {
        parent::__construct($rawPrediction, $pageId, $reconstructed, $valueKey);
        $this->setPosition($rawPrediction);

        if (isset($rawPrediction['is_computed'])) {
            $this->isComputed = $rawPrediction['is_computed'];
        }
        if (isset($this->value)) {
            if ($this->value) {
                try {
                    $this->dateObject = new DateTimeImmutable($this->value, new DateTimeZone('UTC'));
                } catch (Exception) {
                    try {
                        $timestamp = strtotime($this->value);
                        if ($timestamp === false) {
                            throw new Exception("Invalid date format");
                        }
                        $this->dateObject = new DateTimeImmutable('@' . $timestamp);
                    } catch (Exception $e) {
                        throw new MindeeAPIException(
                            "Couldn't create date field from value '" . $this->value . "'",
                            ErrorCode::API_UNPROCESSABLE_ENTITY,
                            $e
                        );
                    }
                }
            } else {
                $this->dateObject = null;
                $this->confidence = 0.0;
            }
        }
    }
}

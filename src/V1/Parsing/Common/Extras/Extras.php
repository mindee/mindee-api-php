<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common\Extras;

use function is_scalar;

/**
 * Extras collection wrapper class.
 *
 * Is roughly equivalent to an array of Extras, with a bit more utility.
 */
class Extras
{
    /**
     * @var CropperExtra|null Cropper extra.
     */
    public ?CropperExtra $cropper;
    /**
     * @var FullTextOCRExtra|null Full text OCR extra.
     */
    public ?FullTextOCRExtra $fullTextOcr;
    /**
     * @var RAGExtra|null Rag Extra.
     */
    public ?RAGExtra $rag;
    /**
     * @var array<string, int|float|string|bool|null|array<array-key, mixed>> Other extras.
     */
    private array $data;

    /**
     * Sets a field.
     *
     * @param string $varName Name of the field to set.
     * @param integer|float|string|boolean|null|array<mixed> $value Value to set the field with.
     */
    public function __set(string $varName, int|float|string|bool|array|null $value): void
    {
        $this->data[$varName] = $value;
    }

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     */
    public function __construct(array $rawPrediction)
    {
        foreach ($rawPrediction as $key => $extra) {
            if ($key === 'cropper' && isset($rawPrediction['cropper'])) {
                $this->cropper = new CropperExtra($rawPrediction['cropper']);
            } elseif ($key === 'full_text_ocr' && isset($rawPrediction['full_text_ocr'])) {
                $this->fullTextOcr = new FullTextOCRExtra($rawPrediction['full_text_ocr']);
            } elseif ($key === 'rag' && isset($rawPrediction['rag'])) {
                $this->rag = new RAGExtra($rawPrediction['rag']);
            } else {
                $this->__set($key, $extra);
            }
        }
    }

    /**
     * Adds artificial extra data for reconstructed extras.
     * Currently only used for full_text_ocr.
     *
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw HTTP response.
     */
    public function addArtificialExtra(array $rawPrediction): void
    {
        if (!empty($rawPrediction['full_text_ocr'])) {
            $this->fullTextOcr = new FullTextOCRExtra($rawPrediction['full_text_ocr']);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $resStr = '';
        foreach ($this->data as $key => $extra) {
            $safeExtra = is_scalar($extra) ? $extra : json_encode($extra);
            $resStr .= $key . ': ' . $safeExtra . "\n";
        }
        if ($this->cropper) {
            $resStr .= ":cropper:" . $this->cropper . "\n";
        }
        if ($this->fullTextOcr) {
            $resStr .= ":full_text_ocr:" . $this->fullTextOcr . "\n";
        }
        if ($this->rag) {
            $resStr .= ":rag:" . $this->rag . "\n";
        }
        return $resStr;
    }
}

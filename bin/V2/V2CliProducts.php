<?php

declare(strict_types=1);

namespace Mindee\Cli\V2;

use Mindee\V2\Product\Classification\ClassificationResponse;
use Mindee\V2\Product\Classification\Params\ClassificationParameters;
use Mindee\V2\Product\Crop\CropResponse;
use Mindee\V2\Product\Crop\Params\CropParameters;
use Mindee\V2\Product\Extraction\ExtractionResponse;
use Mindee\V2\Product\Extraction\Params\ExtractionParameters;
use Mindee\V2\Product\Ocr\OcrResponse;
use Mindee\V2\Product\Ocr\Params\OcrParameters;
use Mindee\V2\Product\Split\Params\SplitParameters;
use Mindee\V2\Product\Split\SplitResponse;

/**
 * V2 CLI product registry.
 *
 * Mirrors the canonical .NET CLI shape:
 * `extraction` exposes the full option set (rag, raw-text, confidence,
 * polygon, text-context); the other inferences expose only the common
 * `--alias`, `--api-key`, `--model-id` and `--output` options.
 */
class V2CliProducts
{
    /**
     * @return array<string, V2CliCommandConfig> V2 product specs keyed by slug.
     */
    public static function getSpecs(): array
    {
        return [
            'classification' => new V2CliCommandConfig(
                'classification',
                'Classification utility.',
                ClassificationResponse::class,
                ClassificationParameters::class,
                rag: false,
                rawText: false,
                confidence: false,
                polygon: false,
                textContext: false,
            ),
            'crop' => new V2CliCommandConfig(
                'crop',
                'Crop utility.',
                CropResponse::class,
                CropParameters::class,
                rag: false,
                rawText: false,
                confidence: false,
                polygon: false,
                textContext: false,
            ),
            'extraction' => new V2CliCommandConfig(
                'extraction',
                'Generic all-purpose extraction.',
                ExtractionResponse::class,
                ExtractionParameters::class,
                rag: true,
                rawText: true,
                confidence: true,
                polygon: true,
                textContext: true,
            ),
            'ocr' => new V2CliCommandConfig(
                'ocr',
                'OCR utility.',
                OcrResponse::class,
                OcrParameters::class,
                rag: false,
                rawText: false,
                confidence: false,
                polygon: false,
                textContext: false,
            ),
            'split' => new V2CliCommandConfig(
                'split',
                'Split utility.',
                SplitResponse::class,
                SplitParameters::class,
                rag: false,
                rawText: false,
                confidence: false,
                polygon: false,
                textContext: false,
            ),
        ];
    }
}

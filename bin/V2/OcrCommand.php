<?php

declare(strict_types=1);

namespace Mindee\Cli\V2;

use Mindee\V2\ClientOptions\BaseProductParameters;
use Mindee\V2\Product\Ocr\OcrResponse;
use Mindee\V2\Product\Ocr\Params\OcrParameters;
use Symfony\Component\Console\Input\InputInterface;

/**
 * V2 CLI command for the OCR utility.
 */
class OcrCommand extends BaseInferenceCommand
{
    public function __construct()
    {
        parent::__construct('ocr');
    }

    /**
     * @return void Configure command options/arguments.
     */
    protected function configure(): void
    {
        $this->setDescription('OCR utility.');
        parent::configure();
    }

    protected function getResponseClass(): string
    {
        return OcrResponse::class;
    }

    protected function buildParameters(
        InputInterface $input,
        string $modelId,
        ?string $alias
    ): BaseProductParameters {
        return new OcrParameters($modelId, $alias);
    }
}

<?php

declare(strict_types=1);

namespace Mindee\Cli\V2;

use Mindee\V2\ClientOptions\BaseParameters;
use Mindee\V2\Product\Crop\CropResponse;
use Mindee\V2\Product\Crop\Params\CropParameters;
use Symfony\Component\Console\Input\InputInterface;

/**
 * V2 CLI command for the crop utility.
 */
class CropCommand extends BaseInferenceCommand
{
    public function __construct()
    {
        parent::__construct('crop');
    }

    /**
     * @return void Configure command options/arguments.
     */
    protected function configure(): void
    {
        $this->setDescription('Crop utility.');
        parent::configure();
    }

    protected function getResponseClass(): string
    {
        return CropResponse::class;
    }

    protected function buildParameters(
        InputInterface $input,
        string $modelId,
        ?string $alias
    ): BaseParameters {
        return new CropParameters($modelId, $alias);
    }
}

<?php

declare(strict_types=1);

namespace Mindee\Cli\V2;

use Mindee\V2\ClientOptions\BaseParameters;
use Mindee\V2\Product\Classification\ClassificationResponse;
use Mindee\V2\Product\Classification\Params\ClassificationParameters;
use Symfony\Component\Console\Input\InputInterface;

/**
 * V2 CLI command for the classification utility.
 */
class ClassificationCommand extends BaseInferenceCommand
{
    public function __construct()
    {
        parent::__construct('classification');
    }

    /**
     * @return void Configure command options/arguments.
     */
    protected function configure(): void
    {
        $this->setDescription('Classification utility.');
        parent::configure();
    }

    protected function getResponseClass(): string
    {
        return ClassificationResponse::class;
    }

    protected function buildParameters(
        InputInterface $input,
        string $modelId,
        ?string $alias
    ): BaseParameters {
        return new ClassificationParameters($modelId, $alias);
    }
}

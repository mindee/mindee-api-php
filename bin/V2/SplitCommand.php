<?php

declare(strict_types=1);

namespace Mindee\Cli\V2;

use Mindee\V2\ClientOptions\BaseProductParameters;
use Mindee\V2\Product\Split\Params\SplitParameters;
use Mindee\V2\Product\Split\SplitResponse;
use Symfony\Component\Console\Input\InputInterface;

/**
 * V2 CLI command for the split utility.
 */
class SplitCommand extends BaseInferenceCommand
{
    public function __construct()
    {
        parent::__construct('split');
    }

    /**
     * @return void Configure command options/arguments.
     */
    protected function configure(): void
    {
        $this->setDescription('Split utility.');
        parent::configure();
    }

    protected function getResponseClass(): string
    {
        return SplitResponse::class;
    }

    protected function buildParameters(
        InputInterface $input,
        string $modelId,
        ?string $alias
    ): BaseProductParameters {
        return new SplitParameters($modelId, $alias);
    }
}

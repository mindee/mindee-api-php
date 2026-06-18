<?php

declare(strict_types=1);

namespace Mindee\Cli\V2;

use Mindee\V2\ClientOptions\BaseParameters;
use Mindee\V2\Parsing\Inference\BaseResponse;
use Mindee\V2\Product\Extraction\ExtractionResponse;
use Mindee\V2\Product\Extraction\Params\ExtractionParameters;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * V2 CLI command for the generic all-purpose extraction utility.
 *
 * Mirrors `mindee-api-java/src/main/java/com/mindee/v2/cli/ExtractionCommand.java`.
 */
class ExtractionCommand extends BaseInferenceCommand
{
    public function __construct()
    {
        parent::__construct('extraction');
    }

    /**
     * @return void Configure command options/arguments.
     */
    protected function configure(): void
    {
        $this->setDescription('Generic all-purpose extraction.');
        parent::configure();
    }

    protected function configureProductOptions(): void
    {
        $this
            ->addOption(
                'rag',
                'g',
                InputOption::VALUE_NONE,
                "Enable Retrieval-Augmented Generation. Only valid for 'extraction'."
            )
            ->addOption(
                'raw-text',
                'r',
                InputOption::VALUE_NONE,
                'Extract the full text of the document.'
            )
            ->addOption(
                'confidence',
                'c',
                InputOption::VALUE_NONE,
                'Retrieve confidence scores from the extraction.'
            )
            ->addOption(
                'polygon',
                'p',
                InputOption::VALUE_NONE,
                'Retrieve bounding polygons from the extraction.'
            )
            ->addOption(
                'text-context',
                't',
                InputOption::VALUE_REQUIRED,
                'Add text context to your API call.'
            );
    }

    protected function getResponseClass(): string
    {
        return ExtractionResponse::class;
    }

    protected function buildParameters(
        InputInterface $input,
        string $modelId,
        ?string $alias
    ): BaseParameters {
        $rag = (bool) $input->getOption('rag');
        $rawText = (bool) $input->getOption('raw-text');
        $confidence = (bool) $input->getOption('confidence');
        $polygon = (bool) $input->getOption('polygon');
        $textContext = $input->getOption('text-context');

        return new ExtractionParameters(
            $modelId,
            rag: $rag ? true : null,
            rawText: $rawText ? true : null,
            polygon: $polygon ? true : null,
            confidence: $confidence ? true : null,
            alias: $alias,
            textContext: $textContext,
        );
    }

    protected function getFullOutput(InputInterface $input, BaseResponse $response): string
    {
        $inference = $response->inference ?? null;
        if ($inference === null) {
            return '';
        }

        $rawText = (bool) $input->getOption('raw-text');
        $rag = (bool) $input->getOption('rag');

        $sections = [];

        if (
            $rawText
            && property_exists($inference, 'activeOptions')
            && $inference->activeOptions->rawText
            && property_exists($inference->result, 'rawText')
            && $inference->result->rawText !== null
        ) {
            $rawTextStr = (string) $inference->result->rawText;
            $sections[] = "#############\nRaw Text\n#############\n::";
            $sections[] = '  ' . str_replace("\n", "\n  ", $rawTextStr);
            $sections[] = '';
        }

        if (
            $rag
            && property_exists($inference, 'activeOptions')
            && $inference->activeOptions->rag
            && property_exists($inference->result, 'rag')
            && $inference->result->rag !== null
        ) {
            $ragStr = (string) ($inference->result->rag->retrievedDocumentId ?? '');
            $sections[] = "#############\nRetrieval-Augmented Generation\n#############\n::";
            $sections[] = '  ' . str_replace("\n", "\n  ", $ragStr);
            $sections[] = '';
        }

        $sections[] = (string) $inference;
        return implode("\n", $sections);
    }
}

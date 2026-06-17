<?php

declare(strict_types=1);

namespace Mindee\Cli\V2;

use Exception;
use Mindee\Input\PathInput;
use Mindee\Input\UrlInputSource;
use Mindee\V2\Client;
use Mindee\V2\ClientOptions\BaseParameters;
use Mindee\V2\Error\MindeeV2HttpException;
use Mindee\V2\Parsing\Inference\BaseResponse;
use Mindee\V2\Product\Extraction\Params\ExtractionParameters;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function in_array;

/**
 * V2 inference CLI command.
 *
 * One instance is registered per V2 product (extraction, classification,
 * crop, ocr, split). The set of options exposed depends on the product
 * spec passed at construction time.
 *
 * Mirrors the canonical .NET implementation in
 * `mindee-api-dotnet/src/Mindee.Cli/Commands/V2/InferenceCommand.cs`.
 */
class InferenceCommand extends Command
{
    /**
     * @var V2CliCommandConfig Product configuration.
     */
    private V2CliCommandConfig $spec;

    /**
     * @param V2CliCommandConfig $spec Product configuration.
     */
    public function __construct(V2CliCommandConfig $spec)
    {
        $this->spec = $spec;
        parent::__construct($spec->name);
    }

    /**
     * @return void Configure command options/arguments.
     */
    protected function configure(): void
    {
        $this
            ->setName($this->spec->name)
            ->setDescription($this->spec->description)
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Path or HTTPS URL of the file to parse.'
            )
            ->addOption(
                'model-id',
                'm',
                InputOption::VALUE_REQUIRED,
                'ID of the model to use.'
            )
            ->addOption(
                'api-key',
                'k',
                InputOption::VALUE_REQUIRED,
                'Mindee V2 API key. Falls back to the MINDEE_V2_API_KEY environment variable.'
            )
            ->addOption(
                'alias',
                'a',
                InputOption::VALUE_REQUIRED,
                'Optional alias for the file.'
            )
            ->addOption(
                'output',
                'o',
                InputOption::VALUE_REQUIRED,
                "Specify how to output the data:\n"
                . "- summary: a basic summary (default)\n"
                . "- full: detailed extraction results, including options\n"
                . "- raw: full JSON object\n",
                'summary'
            );

        if ($this->spec->rag) {
            $this->addOption(
                'rag',
                'g',
                InputOption::VALUE_NONE,
                "Enable Retrieval-Augmented Generation. Only valid for 'extraction'."
            );
        }
        if ($this->spec->rawText) {
            $this->addOption(
                'raw-text',
                'r',
                InputOption::VALUE_NONE,
                'Extract the full text of the document.'
            );
        }
        if ($this->spec->confidence) {
            $this->addOption(
                'confidence',
                'c',
                InputOption::VALUE_NONE,
                'Retrieve confidence scores from the extraction.'
            );
        }
        if ($this->spec->polygon) {
            $this->addOption(
                'polygon',
                'p',
                InputOption::VALUE_NONE,
                'Retrieve bounding polygons from the extraction.'
            );
        }
        if ($this->spec->textContext) {
            $this->addOption(
                'text-context',
                't',
                InputOption::VALUE_REQUIRED,
                'Add text context to your API call.'
            );
        }
    }

    /**
     * @param InputInterface $input CLI input.
     * @param OutputInterface $output CLI output.
     * @return integer Exit code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $modelId = $input->getOption('model-id');
        if (!$modelId) {
            $output->writeln('<error>The "--model-id" (-m) option is required.</error>');
            return Command::FAILURE;
        }

        $apiKey = $input->getOption('api-key');
        if (!$apiKey && !getenv('MINDEE_V2_API_KEY')) {
            $output->writeln(
                '<error>The Mindee V2 API key is missing. '
                . "Please provide it via the '--api-key' option or the MINDEE_V2_API_KEY environment variable.</error>"
            );
            return Command::FAILURE;
        }

        $outputType = (string) ($input->getOption('output') ?? 'summary');
        if (!in_array($outputType, ['summary', 'full', 'raw'], true)) {
            $output->writeln(
                "<error>Invalid output type '$outputType'. Valid values: summary, full, raw.</error>"
            );
            return Command::FAILURE;
        }

        $path = (string) $input->getArgument('path');
        try {
            $source = $this->resolveInputSource($path);
        } catch (Exception $e) {
            $output->writeln("<error>Invalid path or URL provided '$path': " . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
        if ($source === null) {
            $output->writeln("<error>Invalid path or URL provided '$path'.</error>");
            return Command::FAILURE;
        }

        $alias = $input->getOption('alias');
        $rag = $this->spec->rag && (bool) $input->getOption('rag');
        $rawText = $this->spec->rawText && (bool) $input->getOption('raw-text');
        $confidence = $this->spec->confidence && (bool) $input->getOption('confidence');
        $polygon = $this->spec->polygon && (bool) $input->getOption('polygon');
        $textContext = $this->spec->textContext ? $input->getOption('text-context') : null;

        try {
            $params = $this->buildParameters($modelId, $alias, $rag, $rawText, $confidence, $polygon, $textContext);
        } catch (Exception $e) {
            $output->writeln('<error>Failed to build parameters: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $client = new Client($apiKey ?: null);

        try {
            $response = $client->enqueueAndGetResult(
                $this->spec->responseClass,
                $source,
                $params
            );
        } catch (MindeeV2HttpException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        } catch (Exception $e) {
            $output->writeln("<error>Something went wrong, '" . $e->getMessage() . "' was raised.</error>");
            return Command::FAILURE;
        }

        $this->printResponse($response, $outputType, $rag, $rawText, $output);
        return Command::SUCCESS;
    }

    /**
     * Resolves the input source from the given path or URL.
     *
     * @param string $path Path or HTTPS URL.
     * @return PathInput|UrlInputSource|null Input source, or null if invalid.
     */
    private function resolveInputSource(string $path): PathInput|UrlInputSource|null
    {
        if (str_starts_with($path, 'https://')) {
            return new UrlInputSource($path);
        }
        if (@file_exists($path)) {
            return new PathInput($path);
        }
        return null;
    }

    /**
     * Builds the V2 inference parameters for the current product.
     *
     * @param string $modelId Model identifier.
     * @param string|null $alias Optional alias.
     * @param boolean $rag Whether to enable RAG.
     * @param boolean $rawText Whether to enable raw text extraction.
     * @param boolean $confidence Whether to enable confidence scores.
     * @param boolean $polygon Whether to enable polygons.
     * @param string|null $textContext Optional text context.
     * @return BaseParameters Parameters object for the V2 client.
     */
    private function buildParameters(
        string $modelId,
        ?string $alias,
        bool $rag,
        bool $rawText,
        bool $confidence,
        bool $polygon,
        ?string $textContext
    ): BaseParameters {
        $paramsClass = $this->spec->parametersClass;
        if ($paramsClass === ExtractionParameters::class) {
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
        return new $paramsClass($modelId, $alias);
    }

    /**
     * Prints the response according to the chosen output mode.
     *
     * @param BaseResponse $response Inference response.
     * @param string $outputType One of `summary`, `full`, `raw`.
     * @param boolean $rag Whether RAG was requested by the caller.
     * @param boolean $rawText Whether raw text was requested by the caller.
     * @param OutputInterface $output CLI output.
     */
    private function printResponse(
        BaseResponse $response,
        string $outputType,
        bool $rag,
        bool $rawText,
        OutputInterface $output
    ): void {
        if ($outputType === 'raw') {
            $output->writeln($response->getRawHttp());
            return;
        }

        $inference = $response->inference ?? null;
        if ($inference === null) {
            return;
        }

        if ($outputType === 'full') {
            if ($rawText
                && property_exists($inference, 'activeOptions')
                && $inference->activeOptions->rawText
                && property_exists($inference->result, 'rawText')
                && $inference->result->rawText !== null
            ) {
                $rawTextStr = (string) $inference->result->rawText;
                $output->writeln("#############\nRaw Text\n#############\n::");
                $output->writeln('  ' . str_replace("\n", "\n  ", $rawTextStr));
                $output->writeln('');
            }
            if ($rag
                && property_exists($inference, 'activeOptions')
                && $inference->activeOptions->rag
                && property_exists($inference->result, 'rag')
                && $inference->result->rag !== null
            ) {
                $ragStr = (string) ($inference->result->rag->retrievedDocumentId ?? '');
                $output->writeln("#############\nRetrieval-Augmented Generation\n#############\n::");
                $output->writeln('  ' . str_replace("\n", "\n  ", $ragStr));
                $output->writeln('');
            }
            $output->write((string) $inference);
            return;
        }

        // summary (default)
        $output->write((string) $inference->result);
    }
}

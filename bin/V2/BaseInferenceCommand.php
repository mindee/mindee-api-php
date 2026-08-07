<?php

declare(strict_types=1);

namespace Mindee\Cli\V2;

use Exception;
use Mindee\Input\PathInput;
use Mindee\Input\UrlInputSource;
use Mindee\V2\Client;
use Mindee\V2\ClientOptions\BaseProductParameters;
use Mindee\V2\Error\MindeeV2HttpException;
use Mindee\V2\Parsing\Inference\BaseResponse;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function in_array;

/**
 * Abstract base class for V2 inference CLI commands.
 *
 * Handles the options shared by every V2 product (`path`, `--model-id`,
 * `--api-key`, `--alias`, `--output`), input source resolution, client
 * invocation and output formatting. Each concrete subclass owns its
 * product-specific options, builds the right `BaseParameters` instance
 * and customizes the human-readable output.
 *
 * Mirrors the canonical Java implementation in
 * `mindee-api-java/src/main/java/com/mindee/v2/cli/BaseInferenceCommand.java`.
 */
abstract class BaseInferenceCommand extends Command
{
    /**
     * @return void Configures the options common to every V2 product.
     */
    protected function configure(): void
    {
        $this
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

        $this->configureProductOptions();
    }

    /**
     * Hook for subclasses to add product-specific options on top of the
     * common ones (e.g. extraction's `--rag`, `--raw-text`, ...).
     *
     */
    protected function configureProductOptions(): void {}

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

        try {
            $params = $this->buildParameters($input, (string) $modelId, $alias);
        } catch (Exception $e) {
            $output->writeln('<error>Failed to build parameters: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $client = new Client($apiKey ?: null);

        try {
            $response = $client->enqueueAndGetResult(
                $this->getResponseClass(),
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

        $this->printResponse($input, $response, $outputType, $output);
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
     * Builds the V2 inference parameters for this product.
     *
     * @param InputInterface $input CLI input, used to read product-specific options.
     * @param string $modelId Model identifier.
     * @param string|null $alias Optional alias.
     * @return BaseProductParameters Parameters object for the V2 client.
     */
    abstract protected function buildParameters(
        InputInterface $input,
        string $modelId,
        ?string $alias
    ): BaseProductParameters;

    /**
     * @return class-string<BaseResponse> Fully-qualified product response class.
     */
    abstract protected function getResponseClass(): string;

    /**
     * Default human-readable representation of an inference response.
     *
     * @param BaseResponse $response Inference response.
     * @return string Summary string (result only).
     */
    protected function getSummary(BaseResponse $response): string
    {
        $inference = $response->inference ?? null;
        if ($inference === null) {
            return '';
        }
        return (string) $inference->result;
    }

    /**
     * Detailed representation of an inference response. Defaults to the
     * full inference dump; override to add product-specific sections
     * (raw text, RAG, ...).
     *
     * @param InputInterface $input CLI input, used to read product-specific options.
     * @param BaseResponse $response Inference response.
     * @return string Full string.
     */
    protected function getFullOutput(InputInterface $input, BaseResponse $response): string
    {
        $inference = $response->inference ?? null;
        if ($inference === null) {
            return '';
        }
        return (string) $inference;
    }

    /**
     * Prints the response according to the chosen output mode.
     *
     * @param InputInterface $input CLI input.
     * @param BaseResponse $response Inference response.
     * @param string $outputType One of `summary`, `full`, `raw`.
     * @param OutputInterface $output CLI output.
     */
    private function printResponse(
        InputInterface $input,
        BaseResponse $response,
        string $outputType,
        OutputInterface $output
    ): void {
        switch ($outputType) {
            case 'raw':
                $output->writeln($response->getRawHttp());
                return;
            case 'full':
                $output->write($this->getFullOutput($input, $response));
                return;
            default:
                $output->write($this->getSummary($response));
                return;
        }
    }
}

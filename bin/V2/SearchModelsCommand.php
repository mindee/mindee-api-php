<?php

declare(strict_types=1);

namespace Mindee\Cli\V2;

use Exception;
use Mindee\V2\Client;
use Mindee\V2\Error\MindeeV2HttpException;
use Mindee\V2\Parsing\Search\ModelSearchResponse;
use Mindee\V2\Search\Models\ModelSearchParameters;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * V2 `search-models` CLI command.
 *
 * Mirrors the canonical .NET implementation in
 * `mindee-api-dotnet/src/Mindee.Cli/Commands/V2/SearchModelsCommand.cs`.
 */
class SearchModelsCommand extends Command
{
    /**
     * @var array<int, string> Available V2 model types.
     */
    private const AVAILABLE_MODELS = ['extraction', 'crop', 'classification', 'ocr', 'split'];

    /**
     * @return void Configure command options/arguments.
     */
    protected function configure(): void
    {
        $this
            ->setName('search-models')
            ->setDescription('Search available models.')
            ->addOption(
                'api-key',
                'k',
                InputOption::VALUE_REQUIRED,
                'Mindee V2 API key. Falls back to the MINDEE_V2_API_KEY environment variable.'
            )
            ->addOption(
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'Filter by model name partial match (case insensitive).'
            )
            ->addOption(
                'model-type',
                'm',
                InputOption::VALUE_REQUIRED,
                "Filter by exact model type (case sensitive). Available options:\n - "
                . implode("\n - ", self::AVAILABLE_MODELS)
            )
            ->addOption(
                'raw-json',
                'r',
                InputOption::VALUE_NONE,
                'Whether to output the raw JSON response.'
            );
    }

    /**
     * @param InputInterface $input CLI input.
     * @param OutputInterface $output CLI output.
     * @return integer Exit code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $apiKey = $input->getOption('api-key');
        if (!$apiKey && !getenv('MINDEE_V2_API_KEY')) {
            $output->writeln(
                '<error>The Mindee V2 API key is missing. '
                . "Please provide it via the '--api-key' option or the MINDEE_V2_API_KEY environment variable.</error>"
            );
            return Command::FAILURE;
        }

        $name = $input->getOption('name');
        $modelType = $input->getOption('model-type');
        $raw = (bool) $input->getOption('raw-json');

        $client = new Client($apiKey ?: null);

        try {
            $response = $client->search(
                ModelSearchResponse::class,
                new ModelSearchParameters($name ?: null, $modelType ?: null)
            );
        } catch (MindeeV2HttpException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        } catch (Exception $e) {
            $output->writeln("<error>Something went wrong, '" . $e->getMessage() . "' was raised.</error>");
            return Command::FAILURE;
        }

        if ($raw) {
            $output->writeln($response->getRawHttp());
        } else {
            $output->write((string) $response);
        }
        return Command::SUCCESS;
    }
}

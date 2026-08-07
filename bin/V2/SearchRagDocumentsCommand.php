<?php

declare(strict_types=1);

namespace Mindee\Cli\V2;

use Exception;
use Mindee\V2\Client;
use Mindee\V2\Error\MindeeV2HttpException;
use Mindee\V2\Search\RagDocuments\RagDocumentSearchParameters;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * V2 `search-rag-docs` CLI command.
 *
 * Mirrors the canonical .NET implementation in
 * `mindee-api-dotnet/src/Mindee.Cli/Commands/V2/SearchRagDocumentsCommand.cs`.
 */
class SearchRagDocumentsCommand extends Command
{
    /**
     * @return void Configure command options/arguments.
     */
    protected function configure(): void
    {
        $this
            ->setName('search-rag-docs')
            ->setDescription('Search available RAG documents for a given model.')
            ->addOption(
                'api-key',
                'k',
                InputOption::VALUE_REQUIRED,
                'Mindee V2 API key. Falls back to the MINDEE_V2_API_KEY environment variable.'
            )
            ->addOption(
                'model-id',
                'm',
                InputOption::VALUE_REQUIRED,
                'Filter by model ID.'
            )
            ->addOption(
                'filename',
                'f',
                InputOption::VALUE_REQUIRED,
                'Filter by file name partial match (case insensitive).'
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

        $modelId = $input->getOption('model-id');
        if (!$modelId) {
            $output->writeln('<error>The --model-id option is required.</error>');
            return Command::FAILURE;
        }
        $filename = $input->getOption('filename');
        $raw = (bool) $input->getOption('raw-json');

        $client = new Client($apiKey ?: null);

        try {
            $response = $client->searchRagDocuments(
                new RagDocumentSearchParameters($modelId, $filename ?: null)
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

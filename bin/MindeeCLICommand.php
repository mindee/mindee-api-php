<?php

namespace Mindee\CLI;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/version.php';

use Mindee\Client;
use Mindee\Http\Endpoint;
use Mindee\Input\PageOptions;
use Mindee\Input\PredictMethodOptions;
use Mindee\Input\PredictOptions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use const Mindee\Input\KEEP_ONLY;
use const Mindee\Input\REMOVE;
use const Mindee\VERSION;

const JSON_PRINT_RECURSION_DEPTH = 20;

class MindeeCLICommand extends Command
{
    private array $documentList;
    private array $acceptableDocuments;
    public function __construct(array $documentList)
    {
        $this->apiKey = null;
        $this->documentList = $documentList;

        $this->acceptableDocuments = [];
        foreach ($this->documentList as $documentName => $document) {
            $this->acceptableDocuments[] = $documentName;
        }
        parent::__construct('mindee');
    }

    protected function formatHelp($product = null)
    {
        $helpCondensed = "";
        if (!$product) {
            $helpCondensed = "Mindee Command-Line interface.
Usage:
  mindee [options] [--] <product> <method> <file_path_or_url>

Available products:";
            foreach ($this->documentList as $documentName => $document) {
                $helpCondensed .= "\n  " . str_pad($documentName, 65 - strlen($document->help)) . $document->help;
            }
        } else {
            // Handle the case where a specific product help is needed
        }
        return $helpCondensed;
    }

    protected function configure()
    {
        $this
            ->setName('Mindee')
            ->setDescription('Mindee client.')
            ->addArgument(
                'product',
                InputArgument::REQUIRED,
                'Specify which product to use. Available products are :' . implode("\n  ", $this->acceptableDocuments)
            )
            ->addArgument(
                'method',
                InputArgument::REQUIRED,
                'Specify which polling method to use from: parse, enqueue-and-parse'
            )
            ->addArgument(
                'file_path_or_url',
                InputArgument::REQUIRED,
                'Path or URL of the file to be processed.'
            )
            ->setHelp("Processes a document.")
        ;  // Set the help message here

        $this->configureMainOptions();
        $this->configureCustomOptions();
    }

    private function configureMainOptions()
    {
        $this->addOption('pages_remove', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Indexes of the pages to remove from the document.')
            ->addOption('pages_keep', 'p', InputOption::VALUE_REQUIRED, 'Indexes of the pages to keep in the document.')
            ->addOption('key', 'k', InputOption::VALUE_OPTIONAL, 'API key for the account. Is retrieved from environment if not provided.')
            ->addOption('output_type', 'o', InputOption::VALUE_REQUIRED, "Specify how to output the data.\n - summary: a basic summary (default)\n - raw: the raw HTTP response\n - parsed: the validated and parsed data fields\n")
            ->addOption('full_text', 't', InputOption::VALUE_NONE, "Include full document text in response.")
        ;
    }

    private function configureCustomOptions()
    {
        $this
            ->addOption('account_name', 'a', InputOption::VALUE_REQUIRED, 'API account name for the endpoint')
            ->addOption('endpoint_name', 'e', InputOption::VALUE_REQUIRED, 'API endpoint name for the endpoint')
            ->addOption('endpoint_version', 'd', InputOption::VALUE_OPTIONAL, 'Version for the endpoint. If not set, use the latest version of the model');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $args = $input->getArguments();
        $opts = $input->getOptions();

        if (count(array_filter($args)) <= 0 && count(array_filter($opts)) <= 0) {
            $output->writeln($this->formatHelp(), OutputInterface::OUTPUT_NORMAL);
            exit(Command::FAILURE);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $version = $input->getOption('version');
        if ($version) {
            $output->writeln(VERSION);
        } else {
            $product = $input->getArgument('product');
            if (!in_array($product, $this->acceptableDocuments)) {
                $output->writeln("<error>Invalid product: $product</error>");
                $output->writeln('<error>Available products are: ' .
                    implode(', ', $this->acceptableDocuments) .
                    '</error>');
                return Command::FAILURE;
            }
            $key = $input->getOption('key');
            $outputType = $input->getOption('output_type');
            if ($outputType && !in_array($outputType, ['raw', 'summary', 'parsed'])) {
                $output->writeln("<error>Invalid output type: $outputType</error>");
                $output->writeln("<error>Available output types: 'raw', 'summary', 'parsed'</error>");
                return Command::FAILURE;
            }
            $mindeeClient = new Client($key);
            $pollingMethod = $input->getArgument('method');
            if ($pollingMethod && !in_array($pollingMethod, ['parse', 'enqueue-and-parse'])) {
                $output->writeln("<error>Invalid polling: $pollingMethod</error>");
                $output->writeln("<error>Available methods: 'parse', 'enqueue-and-parse'</error>");
                return Command::FAILURE;
            }
            $pagesRemove = $input->getOption('pages_remove');
            $pagesKeep = $input->getOption('pages_keep');
            if ($pagesKeep && $pagesRemove) {
                $output->writeln("<error>Page cut & page keep operations are mutually exclusive.</error>");
                return Command::FAILURE;
            }
            $filePathOrUrl = $input->getArgument('file_path_or_url');
            if ((substr($filePathOrUrl, 0, 8) !== 'https://')) {
                if (@file_exists($filePathOrUrl) || @file_get_contents($filePathOrUrl)) {
                    $file = $mindeeClient->sourceFromPath($filePathOrUrl);
                } else {
                    $output->writeln("<error>Invalid path or url provided.</error>");
                    return Command::FAILURE;
                }
            } else {
                $file = $mindeeClient->sourceFromUrl($filePathOrUrl);
            }
            $pageOptions = new PageOptions();
            if ($pagesRemove) {
                $pageOptions = new PageOptions($pagesRemove, REMOVE, 0);
            } elseif ($pagesKeep) {
                $pageOptions = new PageOptions($pagesKeep, KEEP_ONLY, 0);
            }
            $predictOptions = new PredictOptions();
            if ($input->getOption('full_text')) {
                $predictOptions->setIncludeWords(true);
            }
            $predictMethodOptions = new PredictMethodOptions();
            $predictMethodOptions->setPredictOptions($predictOptions);
            $predictMethodOptions->setPageOptions($pageOptions);
            if ($product === "custom" || $product === "generated") {
                $accountName = $input->getOption('account_name');
                $endpointName = $input->getOption('endpoint_name');
                $endpointVersion = $input->getOption('endpoint_version');
                if (!$accountName) {
                    $output->writeln("<error>Please specify the name of the account for the custom endpoint.</error>");
                    return Command::FAILURE;
                }
                if (!$endpointName) {
                    $output->writeln("<error>Please specify the name of the endpoint.</error>");
                    return Command::FAILURE;
                }
                if (!$endpointVersion) {
                    $endpointVersion = '1';
                    $output->writeln("<comment>No version provided for custom endpoint, version 1 will be used by default.</comment>");
                }
                $endpoint = new Endpoint($accountName, $endpointName, $endpointVersion);
                $predictMethodOptions->setEndpoint($endpoint);
            }
            if ($pollingMethod === "parse") {
                $result = $mindeeClient->parse(
                    $this->documentList[$product]->docClass,
                    $file,
                    $predictMethodOptions
                );
            } elseif ($pollingMethod === "enqueue-and-parse") {
                $result = $mindeeClient->enqueueAndParse(
                    $this->documentList[$product]->docClass,
                    $file,
                    $predictMethodOptions
                );
            } else {
                $output->writeln("<error>Unhandled polling method $pollingMethod.</error>");
                return Command::FAILURE;
            }
            if ($outputType === "raw") {
                echo(
                    json_encode(
                        json_decode(
                            $result->getRawHttp(),
                            true,
                            JSON_PRINT_RECURSION_DEPTH,
                            JSON_UNESCAPED_SLASHES
                        ),
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                    )
                );
            } elseif ($outputType == "parsed") {
                echo(
                    json_encode(
                        json_decode(
                            $result->getRawHttp(),
                            true,
                            JSON_PRINT_RECURSION_DEPTH,
                            JSON_UNESCAPED_SLASHES
                        )['document'],
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                    )
                );
            } else {
                echo($result->document);
            }

            // Your logic goes here based on the input options
            return Command::SUCCESS;
        }
    }
}

<?php

namespace Mindee\CLI;

use Mindee\Client;
use Mindee\Error\MindeeHttpException;
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

/**
 * Configuration Class for CLI.
 */
class MindeeCLICommand extends Command
{
    /**
     * @var array $documentList Array of document configurations.
     */
    private array $documentList;
    /**
     * @var array $acceptableDocuments Array of acceptable documents.
     */
    private array $acceptableDocuments;

    /**
     * @param array $documentList Array of document configurations.
     */
    public function __construct(array $documentList)
    {
        require __DIR__ . '/../src/version.php';
        $this->documentList = $documentList;

        $this->acceptableDocuments = [];
        foreach ($this->documentList as $documentName => $document) {
            $this->acceptableDocuments[] = $documentName;
        }
        parent::__construct('mindee');
    }

    /**
     * @param string|null $product Selected product, for customisation of the help section.
     * @return string
     */
    protected function formatHelp(string $product = null): string
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
            $helpCondensed .= $this->documentList[$product]->help;
        }
        return $helpCondensed;
    }

    /**
     * @return void sets the main CLI properties.
     */
    protected function configure()
    {
        $this
            ->setName('mindee')
            ->setDescription('Mindee client.')
            ->addArgument(
                'product',
                InputArgument::REQUIRED,
                'Specify which product to use. Available products are :' . implode("\n  ", $this->acceptableDocuments)
            )
            ->addArgument(
                'file_path_or_url',
                InputArgument::REQUIRED,
                'Path or URL of the file to be processed.'
            );

        $this->configureMainOptions();
        $this->configureCustomOptions();
    }

    /**
     * @return void Sets main properties regarding polling/parsing.
     */
    private function configureMainOptions()
    {
        $this->addOption(
            'async_polling',
            'A',
            InputOption::VALUE_NONE,
            'When enabled, enqueues and parses the document asynchronously.'
        )
            ->addOption(
                'pages_remove',
                'r',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Indexes of the pages to remove from the document.'
            )
            ->addOption(
                'pages_keep',
                'p',
                InputOption::VALUE_REQUIRED,
                'Indexes of the pages to keep in the document.'
            )
            ->addOption(
                'key',
                'k',
                InputOption::VALUE_OPTIONAL,
                'API key for the account. Is retrieved from environment if not provided.'
            )
            ->addOption(
                'output_type',
                'o',
                InputOption::VALUE_REQUIRED,
                'Specify how to output the data.
 - summary: a basic summary (default)
 - raw: the raw HTTP response
 - parsed: the validated and parsed data fields
'
            )
            ->addOption(
                'full_text',
                't',
                InputOption::VALUE_NONE,
                "Include full document text in response."
            )
            ->addOption(
                'cropper',
                'c',
                InputOption::VALUE_NONE,
                "Apply cropper operation to the document (if available)."
            )
            ->addOption(
                'debug',
                'D',
                InputOption::VALUE_NONE,
                'Debug mode (dry-run).'
            );
    }

    /**
     * @return void Sets custom options.
     */
    private function configureCustomOptions()
    {
        $this
            ->addOption(
                'account_name',
                'a',
                InputOption::VALUE_REQUIRED,
                'API account name for the endpoint'
            )
            ->addOption(
                'endpoint_name',
                'e',
                InputOption::VALUE_REQUIRED,
                'API endpoint name for the endpoint'
            )
            ->addOption(
                'endpoint_version',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Version for the endpoint. If not set, use the latest version of the model'
            );
    }

    /**
     * Initializes the CLI runner, writes the help section if no argument nor option is given.
     *
     * @param InputInterface  $input  Input interface given to the CLI.
     * @param OutputInterface $output Output interface.
     * @return void
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $args = $input->getArguments();
        $opts = $input->getOptions();

        if (count(array_filter($args)) <= 0 && count(array_filter($opts)) <= 0) {
            $output->writeln($this->formatHelp(), OutputInterface::OUTPUT_NORMAL);
            exit(Command::FAILURE);
        }
    }

    /**
     * Runs a command (overload).
     *
     * @param InputInterface  $input  Input interface given to the CLI.
     * @param OutputInterface $output Output interface.
     * @return integer Command execution code return.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
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
            $isAsync = $input->getOption('async_polling');
            if ($isAsync && !$this->documentList[$product]->isAsync) {
                $output->writeln("<error>Invalid polling method for $product</error>");
                $output->writeln("<comment>Asynchronous mode is not supported.</comment>");
                return Command::FAILURE;
            }
            if (!$isAsync && !$this->documentList[$product]->isSync) {
                $output->writeln("<error>Invalid polling method for $product</error>");
                $output->writeln("<comment>Synchronous mode is not supported.</comment>");
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
                    $output->writeln("<error>Invalid path or url provided '$filePathOrUrl'.</error>");
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
                    $output->writeln("<error>Please specify the name of the account for $product endpoint.</error>");
                    return Command::FAILURE;
                }
                if (!$endpointName) {
                    $output->writeln("<error>Please specify the name of $product endpoint.</error>");
                    return Command::FAILURE;
                }
                if (!$endpointVersion) {
                    $endpointVersion = '1';
                    $output->writeln(
                        "<comment>No version provided for \"" .
                        $endpointName .
                        "\", version 1 will be used by default.</comment>"
                    );
                }
                $endpoint = $mindeeClient->createEndpoint($endpointName, $accountName, $endpointVersion);
                $predictMethodOptions->setEndpoint($endpoint);
            }
            $debug = $input->getOption('debug');
            if (!$debug) {
                if (!$isAsync) {
                    try {
                        $result = $mindeeClient->parse(
                            $this->documentList[$product]->docClass,
                            $file,
                            $predictMethodOptions
                        );
                    } catch (MindeeHttpException $e) {
                        if ($e->getMessage()) {
                            $output->writeln($e->getMessage());
                        }
                        return Command::FAILURE;
                    } catch (\Exception $e) {
                        $output->writeln("Something went wrong, '" . $e->getMessage() . "' was raised.");
                        return Command::FAILURE;
                    }
                } else {
                    try {
                        $result = $mindeeClient->enqueueAndParse(
                            $this->documentList[$product]->docClass,
                            $file,
                            $predictMethodOptions
                        );
                    } catch (MindeeHttpException $e) {
                        if ($e->getMessage()) {
                            $output->writeln($e->getMessage());
                        }
                        return Command::FAILURE;
                    } catch (\Exception $e) {
                        $output->writeln("Something went wrong, '" . $e->getMessage() . "' was raised.");
                        return Command::FAILURE;
                    }
                }
            } else {
                $output->writeln("Command executed successfully.");
                return Command::SUCCESS;
            }
            if ($outputType === "raw") {
                $output->writeln(
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
                $output->writeln(
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
        }
        return Command::SUCCESS;
    }
}

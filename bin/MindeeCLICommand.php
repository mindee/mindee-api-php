<?php

namespace Mindee\CLI;

use Mindee\Client;
use Mindee\Error\MindeeHttpException;
use Mindee\Input\InputSource;
use Mindee\Input\PageOptions;
use Mindee\Input\PathInput;
use Mindee\Input\PredictMethodOptions;
use Mindee\Input\PredictOptions;
use Mindee\Input\URLInputSource;
use Mindee\Parsing\Common\AsyncPredictResponse;
use Mindee\Parsing\Common\PredictResponse;
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
            'async',
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
        if ($this->handleVersionOption($input, $output)) {
            return Command::SUCCESS;
        }

        $product = $input->getArgument('product');
        if (!$this->isValidProduct($product, $output)) {
            return Command::FAILURE;
        }

        $outputType = $input->getOption('output_type');

        $client = new Client($input->getOption('key'));
        if ($this->documentList[$product]->isAsync && !$this->documentList[$product]->isSync) {
            $isAsync = true;
        } else {
            $isAsync = $input->getOption('async');
        }

        if (!$this->isValidPollingMethod($product, $isAsync, $output)) {
            return Command::FAILURE;
        }

        if ($this->areMutuallyExclusivePagesOptions($input, $output)) {
            return Command::FAILURE;
        }

        $filePathOrUrl = $input->getArgument('file_path_or_url');
        $file = $this->getFileSource($filePathOrUrl, $client, $output);
        if (!$file) {
            return Command::FAILURE;
        }

        $pageOptions = $this->getPageOptions($input);
        $predictOptions = $this->getPredictOptions($input);
        $predictMethodOptions = $this->getPredictMethodOptions($predictOptions, $pageOptions);

        if (!$this->handleCustomOrGeneratedProduct($input, $output, $client, $predictMethodOptions, $product)) {
            return Command::FAILURE;
        }

        return $this->executePrediction(
            $client,
            $product,
            $file,
            $predictMethodOptions,
            $isAsync,
            $input,
            $output,
            $outputType
        );
    }

    /**
     * Checks whether the version was requested.
     *
     * @param InputInterface  $input  Input interface of the CLI.
     * @param OutputInterface $output Output interface of the CLI.
     * @return boolean True if options are valid.
     */
    private function handleVersionOption(InputInterface $input, OutputInterface $output): bool
    {
        if ($input->getOption('version')) {
            $output->writeln(VERSION);
            return true;
        }
        return false;
    }

    /**
     * Checks whether a given product is valid for CLI use.
     *
     * @param string          $product Product class used.
     * @param OutputInterface $output  Output interface of the CLI.
     * @return boolean True if a product is valid.
     */
    private function isValidProduct(string $product, OutputInterface $output): bool
    {
        if (!in_array($product, $this->acceptableDocuments)) {
            $output->writeln("<error>Invalid product: $product</error>");
            $output->writeln('<error>Available products are: ' .
                implode(', ', $this->acceptableDocuments) . '</error>');
            return false;
        }
        return true;
    }

    /**
     * Checks whether a polling method is valid for the current poll.
     *
     * @param string          $product Product class used.
     * @param boolean         $isAsync Whether the polling will be asynchronous.
     * @param OutputInterface $output  Output interface of the CLI.
     * @return boolean True if the polling method exists for a given product.
     */
    private function isValidPollingMethod(string $product, bool $isAsync, OutputInterface $output): bool
    {
        if ($isAsync && !$this->documentList[$product]->isAsync) {
            $output->writeln("<error>Invalid polling method for $product</error>");
            $output->writeln("<comment>Asynchronous mode is not supported.</comment>");
            return false;
        }
        if (!$isAsync && !$this->documentList[$product]->isSync) {
            $output->writeln("<error>Invalid polling method for $product</error>");
            $output->writeln("<comment>Synchronous mode is not supported.</comment>");
            return false;
        }
        return true;
    }

    /**
     * Checks whether PageOptions for the current polling are possible.
     *
     * @param InputInterface  $input  Input interface of the CLI.
     * @param OutputInterface $output Output interface of the CLI.
     * @return boolean True if the operations are possible.
     */
    private function areMutuallyExclusivePagesOptions(InputInterface $input, OutputInterface $output): bool
    {
        $pagesRemove = $input->getOption('pages_remove');
        $pagesKeep = $input->getOption('pages_keep');
        if ($pagesKeep && $pagesRemove) {
            $output->writeln("<error>Page cut & page keep operations are mutually exclusive.</error>");
            return true;
        }
        return false;
    }

    /**
     * Retrieves a source file from a URL or a path.
     *
     * @param string          $filePathOrUrl Path of the file, or URL if it's remote.
     * @param Client          $client        Mindee Client.
     * @param OutputInterface $output        Output interface of the CLI.
     * @return PathInput|URLInputSource|null A valid InputSource.
     */
    private function getFileSource(string $filePathOrUrl, Client $client, OutputInterface $output)
    {
        if (substr($filePathOrUrl, 0, 8) !== 'https://') {
            if (@file_exists($filePathOrUrl) || @file_get_contents($filePathOrUrl)) {
                return $client->sourceFromPath($filePathOrUrl);
            } else {
                $output->writeln("<error>Invalid path or url provided '$filePathOrUrl'.</error>");
                return null;
            }
        }
        return $client->sourceFromUrl($filePathOrUrl);
    }

    /**
     * Retrieves the PageOptions for the current poll.
     *
     * @param InputInterface $input Input interface of the CLI.
     * @return PageOptions Valid PageOptions.
     */
    private function getPageOptions(InputInterface $input): PageOptions
    {
        $pagesRemove = $input->getOption('pages_remove');
        $pagesKeep = $input->getOption('pages_keep');
        if ($pagesRemove) {
            return new PageOptions($pagesRemove, REMOVE, 0);
        }
        if ($pagesKeep) {
            return new PageOptions($pagesKeep, KEEP_ONLY, 0);
        }
        return new PageOptions();
    }

    /**
     * Retrieves the PredictOptions for the current poll.
     *
     * @param InputInterface $input Input interface of the CLI.
     * @return PredictOptions Valid PredictOptions.
     */
    private function getPredictOptions(InputInterface $input): PredictOptions
    {
        $predictOptions = new PredictOptions();
        if ($input->getOption('full_text')) {
            $predictOptions->setIncludeWords(true);
        }
        return $predictOptions;
    }

    /**
     * Generates a valid PredictMethodOptions object for parsing.
     *
     * @param PredictOptions $predictOptions Valid PredictOptions.
     * @param PageOptions    $pageOptions    Valid PageOptions.
     * @return PredictMethodOptions Valid PredictMethod Options.
     */
    private function getPredictMethodOptions(
        PredictOptions $predictOptions,
        PageOptions $pageOptions
    ): PredictMethodOptions {
        $predictMethodOptions = new PredictMethodOptions();
        $predictMethodOptions->setPredictOptions($predictOptions);
        $predictMethodOptions->setPageOptions($pageOptions);
        return $predictMethodOptions;
    }

    /**
     * Handles options specific to Custom & Generated Products.
     *
     * @param InputInterface       $input                Input interface of the CLI.
     * @param OutputInterface      $output               Output interface of the CLI.
     * @param Client               $client               Mindee Client.
     * @param PredictMethodOptions $predictMethodOptions Valid PredictMethodOptions.
     * @param string               $product              Product class used.
     * @return boolean Whether the setting of options for custom/generated are valid.
     */
    private function handleCustomOrGeneratedProduct(
        InputInterface $input,
        OutputInterface $output,
        Client $client,
        PredictMethodOptions $predictMethodOptions,
        string $product
    ): bool {
        if (in_array($product, ["custom", "generated"])) {
            $accountName = $input->getOption('account_name');
            $endpointName = $input->getOption('endpoint_name');
            $endpointVersion = $input->getOption('endpoint_version') ?? '1';

            if (!$accountName) {
                $output->writeln("<error>Please specify the name of the account for $product endpoint.</error>");
                return false;
            }
            if (!$endpointName) {
                $output->writeln("<error>Please specify the name of $product endpoint.</error>");
                return false;
            }
            if (!$endpointVersion) {
                $output->writeln(
                    "<comment>No version provided for \"$endpointName\", version 1 will be used by default.</comment>"
                );
            }

            $endpoint = $client->createEndpoint($endpointName, $accountName, $endpointVersion);
            $predictMethodOptions->setEndpoint($endpoint);
        }
        return true;
    }

    /**
     * @param Client               $client               Mindee Client.
     * @param string               $product              Product class used.
     * @param InputSource          $file                 Input File.
     * @param PredictMethodOptions $predictMethodOptions Options for the polling.
     * @param boolean              $isAsync              Whether the polling will be asynchronous.
     * @param InputInterface       $input                Input interface of the CLI.
     * @param OutputInterface      $output               Output interface of the CLI.
     * @param string|null          $outputType           Type of output (raw, parsed or summary).
     * @return integer Return code for the CLI
     */
    private function executePrediction(
        Client $client,
        string $product,
        InputSource $file,
        PredictMethodOptions $predictMethodOptions,
        bool $isAsync,
        InputInterface $input,
        OutputInterface $output,
        ?string $outputType
    ): int {
        $debug = $input->getOption('debug');
        try {
            $result = $this->runClientPrediction($client, $product, $file, $predictMethodOptions, $isAsync, $debug);
        } catch (MindeeHttpException $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        } catch (\Exception $e) {
            $output->writeln("Something went wrong, '" . $e->getMessage() . "' was raised.");
            return Command::FAILURE;
        }

        return $this->outputResult($result, $outputType, $output);
    }

    /**
     * Runs the prediction call.
     *
     * @param Client               $client               Mindee client.
     * @param string               $product              Product class used.
     * @param InputSource          $file                 Input File.
     * @param PredictMethodOptions $predictMethodOptions Prediction method options.
     * @param boolean              $isAsync              Whether the polling is asynchronous.
     * @param boolean              $debug                Whether the command is running in debug mode.
     *
     * @return AsyncPredictResponse|PredictResponse|string Either a valid prediction response, or a message if the
     * command is in debug mode.
     */
    private function runClientPrediction(
        Client $client,
        string $product,
        InputSource $file,
        PredictMethodOptions $predictMethodOptions,
        bool $isAsync,
        bool $debug
    ) {
        if ($debug) {
            return "Command executed successfully.";
        }

        if ($isAsync) {
            return $client->enqueueAndParse($this->documentList[$product]->docClass, $file, $predictMethodOptions);
        }

        return $client->parse($this->documentList[$product]->docClass, $file, $predictMethodOptions);
    }

    /**
     * @param PredictResponse|AsyncPredictResponse|string $result     Result of the parsing (or message if in debug
     *     mode).
     * @param string|null                                 $outputType Type of output (raw, parsed or summary).
     * @param OutputInterface                             $output     Output interface for the CLI.
     * @return integer Command execution code return.
     */
    private function outputResult(
        $result,
        ?string $outputType,
        OutputInterface $output
    ): int {
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
        } elseif ($outputType === "parsed") {
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
            echo $result->document;
        }

        return Command::SUCCESS;
    }
}

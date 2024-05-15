<?php

namespace Mindee\CLI;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/version.php';

use Mindee\Client;
use Mindee\Input\PredictOptions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use const Mindee\VERSION;

class MindeeCLICommand extends Command
{
    private array $documentList;
    private array $acceptableDocuments;
    private ?string $apiKey;
    private ?string $cutPages;
    private ?string $keepPages;

    private Client $mindeeClient;
    private string $pollingMethod;
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
        if (!$product) {
            $helpCondensed = "Mindee Command-Line interface.
Usage:
  mindee <product> [options]

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
                'Specify which product to use. Available products are :'.implode("\n  ", $this->acceptableDocuments)
            )
            ->addArgument(
                'method',
                InputArgument::REQUIRED,
                'Specify which polling method to use from: parse, enqueue, enqueue-and-parse')
            ->setHelp($this->formatHelp())
        ;  // Set the help message here

        $this->configureMainOptions();
        $this->configureCustomOptions();
        $this->addArgument(
            'file_path_or_url',
            InputArgument::REQUIRED,
            'Path or URL of the file to be processed.'
        );
    }

    private function configureMainOptions()
    {
        $this->addOption('cut_doc', 'c', InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, 'Cuts to apply to document')
            ->addOption('pages_keep', 'p', InputOption::VALUE_REQUIRED, 'Pages to keep, default: 5')
            ->addOption('key', 'k', InputOption::VALUE_OPTIONAL, 'API key for the account. Is retrieved from environment if not provided.');
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
            exit(Command::SUCCESS);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $version = $input->getOption('version');
        if ($version) {
            $output->writeln(VERSION);
        } else {
            $product = $input->getArgument('product');
            if (!in_array($product, $this->acceptableDocuments))
            {
                $output->writeln("<error>Invalid product: $product</error>");
                $output->writeln('<error>Available products: ' . implode(', ', $this->acceptableDocuments). '</error>');
                return Command::FAILURE;
            }
            $key = $input->getOption('key');
            $mindeeClient = new Client($key);
            $pollingMethod = $input->getArgument('method');
            if (!in_array($pollingMethod, ['parse', 'enqueue', 'enqueue-and-parse'])) {
                $output->writeln("<error>Invalid polling: $pollingMethod</error>");
                $output->writeln("<error>Available methods: 'parse', 'enqueue', 'enqueue-and-parse'</error>");
                return Command::FAILURE;
            }
            $cutDoc = $input->getOption('cut_doc');
            $filePathOrUrl = $input->getOption('file_path_or_url');
            if ((substr($filePathOrUrl, 0, 8) !== 'https://')) {
                if (@file_exists($filePathOrUrl) || file_get_contents($filePathOrUrl)){
                    $file = $mindeeClient->sourceFromFile($filePathOrUrl);
                }
            } else {
                $file = $mindeeClient->sourceFromUrl($filePathOrUrl);
            }
            $predictOptions = new PredictOptions();
            echo "Product: $product\n";
            echo "Method: $pollingMethod\n";
            echo "Key: $key\n";
            echo "Cut doc: ".implode(', ', $cutDoc)."\n";
            if ($product==="custom" || $product==="generated"){
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
                echo "Account Name: $accountName\n";
                echo "Endpoint Name: $endpointName\n";
                echo "Endpoint Version: $endpointVersion\n";
            } else {
                if ($pollingMethod==="parse"){
                    $result = $mindeeClient->parse(
                        $this->documentList[$product]->docClass,
                        $file
                    );
                }
            }


            // Your logic goes here based on the input options
            return Command::SUCCESS;
        }
    }
}

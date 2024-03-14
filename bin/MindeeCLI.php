<?php

namespace Mindee\CLI;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/version.php';

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use const Mindee\VERSION;

class MindeeCLI extends Command
{
    private array $documentList;
    private ?string $apiKey;
    private ?string $cutPages;
    private ?string $keepPages;

    public function __construct(array $documentList)
    {
        parent::__construct();
        $this->apiKey = null;
        $this->documentList = $documentList;
        // $this->setHelp($this->formatHelp());
    }

    protected function formatHelp($product = null)
    {
        if (!$product) {
            $helpCondensed = "Mindee Command-Line interface.
Usage:
  mindee product [options]

Available Products:";
            foreach ($this->documentList as $documentName => $document) {
                $helpCondensed .= "\n  " . str_pad($documentName, 65 - strlen($document->help)) . $document->help;
            }
        } else {

        }
        return $helpCondensed;
    }

    protected function configure()
    {
        $this
            ->setName('mindee')
            ->setDescription('Mindee client.');

        $this->configureMainOptions();
        $this->configureCustomOptions();
        // Add more configuration if needed for OTS options and display options
    }

    private function configureMainOptions()
    {
        $this
            ->addArgument('product', InputArgument::IS_ARRAY, 'Specify which product to use')
            ->addOption('cut_doc', 'c', InputOption::VALUE_REQUIRED, 'Cuts to apply to document')
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

    // Implement your logic here
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $version = $input->getOption('version');

        if ($version) {
            $output->writeln(VERSION);
        } else {
            $key = $input->getOption('key') !== false;
            $cutDoc = $input->getOption('cut_doc') !== false;
            $accountName = $input->getOption('account_name') !== false;
            $endpointName = $input->getOption('endpoint_name') !== false;
            $endpointVersion = $input->getOption('endpoint_version') !== false;

            echo $key;
            // Handle other options similarly

            // Your logic goes here based on the input options
            return Command::SUCCESS;
        }
    }
}

// References for later:
// Options:
//  -c, --cut_doc CUT_DOC Specify how to handle the input.
//  -p, --pages_keep PAGES_KEEP
//  -k, --key [KEY]
//  -a, --account_name ACCOUNT_NAME
//  -e, --endpoint_name ENDPOINT_NAME
//  -d, --endpoint_version [ENDPOINT_VERSION] [--]
//  -A, --async Parses asynchronously
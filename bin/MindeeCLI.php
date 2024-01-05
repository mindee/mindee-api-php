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
        $this->setHelp($this->formatHelp());
    }

    protected function formatHelp()
    {
        $helpCondensed = "Mindee Command-Line interface.
Usage:
  mindee product [options]

Arguments:";
        foreach ($this->documentList as $documentName => $document) {
            $helpCondensed .= "\n  " . str_pad($documentName, 65 - strlen($document->help)) . $document->help;
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
            ->addArgument('input_type', InputArgument::OPTIONAL, 'Specify how to handle the input.')
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
        // Check if no arguments are provided
        if (!isset($input->getArguments()["input_type"]) || is_null($input->getArguments()["input_type"])) {
            $output->writeln($this->getApplication()->find('mindee')->getHelp());
            return Command::SUCCESS;
        } else {
            foreach ($input->getArguments() as $ipt) {
                $output->writeln($ipt);
            }
            $output->writeln(sizeof($input->getArguments()));

        }
        if ($version) {
            $output->writeln(VERSION);
        } else {
            $key = $input->getOption('key');
            $cutDoc = $input->getOption('cut_doc');
            // Handle other options similarly

            $output->writeln('This is the output of the command');
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
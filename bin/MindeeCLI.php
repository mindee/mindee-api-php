<?php

namespace Mindee\CLI;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/../src/version.php';

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

use const Mindee\VERSION;

class MindeeCLI extends CLI
{
    private array $documentList;
    private ?string $apiKey;
    private ?string $cutPages;
    private ?string $keepPages;

    protected function registerMainOptions(Options $options)
    {
        $options->registerCommand("parse", "Parses a document.");
        $options->registerOption('input-type', 'Input Type', 'i', "input_type");//TODO add values in post
        $options->registerArgument(
            'input_type',
            "Specify how to handle the input.\n" .
            "- path: open a path (default).\n" .
            "- file: open as a file handle.\n" .
            "- base64: open a base64 encoded text file.\n" .
            "- bytes: open the contents as raw bytes.\n" .
            "- url: open an URL.",
            false,
        );
        $options->registerOption("cut-doc", "Cut Document pages", "c", "cut_doc");
        $options->registerArgument("cut_doc", "Cuts to apply to document", true, "parse");
        $options->registerOption("pages-keep", "Number of document pages to keep, default: 5.", "p", "pages_keep");
        $options->registerArgument("pages_keep", "Pages to keep, default: 5.", true, "parse");

        $options->registerOption("key", "API key for the account. Is retrieved from environment if not provided.", "k", "api_key");
        $options->registerArgument("api_key", "API key.", false);
    }

    protected function registerCustomOptions(Options $options)
    {
        $options->registerOption("account", "API account name for the endpoint", "a", "account_name", "custom");
        $options->registerArgument("account_name", "Account name.", true, "custom");

        $options->registerOption("endpoint", "API endpoint name for the endpoint", "e", "endpoint_name", "custom");
        $options->registerArgument("endpoint_name", "Endpoint name.", true, "custom");

        $options->registerOption(
            "version",
            "Version for the endpoint. If not set, use the latest version of the model.",
            "v",
            "endpoint_version",
            "custom"
        );
        $options->registerArgument("endpoint_version", "Endpoint version.", false, "custom");
    }

    protected function registerOTSOptions(Options $options, string $commandName, CommandConfig $commandConfig)
    {
        $options->registerOption("full-text", "Include full document text in response.", "t", false, $commandName);
        if ($commandConfig->isAsync && $commandConfig->isSync) {
            $options->registerOption("asynchronous", "Parse asynchronously.", "A", false, $commandName);
        }
    }

    protected function registerDisplayOptions(Options $options)
    {
        $options->registerOption('output-type', 'Output Type', 'o', "output_type");//TODO add values in post
        $options->registerArgument(
            'output_type',
            "Specify how to output the data.\n" .
            "- summary: a basic summary (default)\n" .
            "- raw: the raw HTTP response\n" .
            "- parsed: the validated and parsed data fields\n",
            false,
            "parse"
        );
    }

    protected function setup(Options $options)
    {
        $options->setHelp('Mindee client.');
        $this->registerMainOptions($options);
        $this->registerDisplayOptions($options);
        foreach ($this->documentList as $documentName => $documentAttributes) {
            $options->registerCommand($documentName, $documentAttributes->help);
            if ($documentAttributes->isSync) {
                if ($documentName == "custom") {
                    $this->registerCustomOptions($options);
                } else {
                    $this->registerOTSOptions($options, $documentName, $documentAttributes);
                }
            }
        }
    }

    protected function callParse(){

    }

    // implement your code
    protected function main(Options $options)
    {
        $options->useCompactHelp();
        if ($options->getOpt('version')) {
            $this->info(VERSION);
        } elseif ($options->getOpt('help')) {
            echo $options->help();
        } else {
            if($options->getOpt('key')){
                $this->apiKey = $options->getOpt('key');
            }
            if($options->getOpt('cut-doc')){
                $this->apiKey = $options->getOpt('cut-doc');
            }
        }
    }

    public function __construct(
        array $documentList
    ) {
        parent::__construct(true);
        $this->apiKey = null;
        $this->documentList = $documentList;
    }
}

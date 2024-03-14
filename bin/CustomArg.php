<?php

namespace Mindee\CLI;

class CustomArg
{
    public string $longName;
    public string $help;
    public string $destination;
    public bool $required;

    public function __construct(
        string $longName,
        string $help,
        string $destination,
        bool $required = false,
        ?array $choices = null,
        $defaultValue = null
    ) {
        $this->longName = $longName;
        $this->help = $help;
        $this->destination = $destination;
        $this->required = $required;
    }
}

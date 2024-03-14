<?php

namespace Mindee\CLI;

class CommandConfig
{
    public string $help;
    public string $docClass;
    public bool $isSync;
    public bool $isAsync;

    public function __construct(string $help, string $docClass, bool $isSync, bool $isAsync = false)
    {
// Constructor implementation
        $this->help = $help;
        $this->docClass = $docClass;
        $this->isSync = $isSync;
        $this->isAsync = $isAsync;
    }
}

<?php

namespace Mindee\CLI;

/**
 * Document configuration class for CLI usage.
 */
class DocumentCommandConfig
{
    /**
     * @var string Custom help message (currently not in use).
     */
    public string $help;
    /**
     * @var string Document class, as defined in the Mindee\Product namespace.
     */
    public string $docClass;
    /**
     * @var boolean Whether the document supports synchronous usage.
     */
    public bool $isSync;
    /**
     * @var boolean Whether the document supports asynchronous usage.
     */
    public bool $isAsync;

    /**
     * @param string  $help     Custom help message (currently not in use).
     * @param string  $docClass Document class, as defined in the Mindee\Product namespace.
     * @param boolean $isSync   Whether the document supports synchronous usage.
     * @param boolean $isAsync  Whether the document supports asynchronous usage.
     */
    public function __construct(string $help, string $docClass, bool $isSync, bool $isAsync = false)
    {
        $this->help = $help;
        $this->docClass = $docClass;
        $this->isSync = $isSync;
        $this->isAsync = $isAsync;
    }
}

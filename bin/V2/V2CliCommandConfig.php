<?php

declare(strict_types=1);

namespace Mindee\Cli\V2;

/**
 * Configuration for a V2 CLI inference command.
 *
 * Mirrors the canonical .NET `InferenceCommandOptions` struct
 * (`mindee-api-dotnet/src/Mindee.Cli/Commands/V2/InferenceCommand.cs`).
 */
class V2CliCommandConfig
{
    /**
     * @param string $name CLI command name (matches the V2 product slug).
     * @param string $description Short command description displayed in help.
     * @param class-string $responseClass Fully-qualified V2 response class.
     * @param class-string $parametersClass Fully-qualified V2 parameters class.
     * @param boolean $rag Whether to expose the `--rag/-g` option.
     * @param boolean $rawText Whether to expose the `--raw-text/-r` option.
     * @param boolean $confidence Whether to expose the `--confidence/-c` option.
     * @param boolean $polygon Whether to expose the `--polygon/-p` option.
     * @param boolean $textContext Whether to expose the `--text-context/-t` option.
     */
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly string $responseClass,
        public readonly string $parametersClass,
        public readonly bool $rag = false,
        public readonly bool $rawText = false,
        public readonly bool $confidence = false,
        public readonly bool $polygon = false,
        public readonly bool $textContext = false,
    ) {}
}

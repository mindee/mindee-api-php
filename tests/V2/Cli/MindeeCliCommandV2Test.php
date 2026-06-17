<?php

declare(strict_types=1);

namespace V2\Cli;

use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . '/MindeeCliV2TestingUtilities.php');
require_once(__DIR__ . '/../../TestingUtilities.php');

/**
 * Unit-level CLI tests for the V2 commands. They never reach the
 * Mindee V2 API: every assertion is on argument validation, error
 * handling or argv dispatching.
 */
class MindeeCliCommandV2Test extends TestCase
{
    private string $filePath;

    protected function setUp(): void
    {
        $this->filePath = TestingUtilities::getFileTypesDir() . '/pdf/blank_1.pdf';
    }

    /**
     * @return array<string, array{0: string}> Inference command name provider.
     */
    public static function provideInferenceCommandNames(): iterable
    {
        return [
            'classification' => ['classification'],
            'crop' => ['crop'],
            'extraction' => ['extraction'],
            'ocr' => ['ocr'],
            'split' => ['split'],
        ];
    }

    public function testListShouldShowAllV2Commands(): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(['list']);
        self::assertSame(0, $cmdOutput['code']);
        $stdout = implode("\n", $cmdOutput['output']);
        self::assertStringContainsString('classification', $stdout);
        self::assertStringContainsString('crop', $stdout);
        self::assertStringContainsString('extraction', $stdout);
        self::assertStringContainsString('ocr', $stdout);
        self::assertStringContainsString('split', $stdout);
        self::assertStringContainsString('search-models', $stdout);
        self::assertStringContainsString('v1', $stdout);
    }

    /**
     * @dataProvider provideInferenceCommandNames
     */
    public function testInferenceMissingModelIdMustFail(string $command): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(
            [$command, $this->filePath, '-k', 'fake-key'],
            ['MINDEE_V2_API_KEY' => false]
        );
        self::assertSame(1, $cmdOutput['code']);
        self::assertStringContainsString(
            '--model-id',
            implode("\n", $cmdOutput['output']),
            "Command '$command' must complain about missing --model-id"
        );
    }

    /**
     * @dataProvider provideInferenceCommandNames
     */
    public function testInferenceMissingApiKeyMustFail(string $command): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(
            [$command, '-m', 'some-model-id', $this->filePath],
            ['MINDEE_V2_API_KEY' => false]
        );
        self::assertSame(1, $cmdOutput['code']);
        self::assertStringContainsString(
            'API key is missing',
            implode("\n", $cmdOutput['output']),
            "Command '$command' must complain about missing API key"
        );
    }

    /**
     * @dataProvider provideInferenceCommandNames
     */
    public function testInferenceInvalidPathMustFail(string $command): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(
            [$command, '-m', 'some-model-id', '-k', 'fake-key', 'invalid-file-path'],
            ['MINDEE_V2_API_KEY' => false]
        );
        self::assertSame(1, $cmdOutput['code']);
        self::assertStringContainsString(
            "Invalid path or URL provided 'invalid-file-path'",
            implode("\n", $cmdOutput['output']),
            "Command '$command' must complain about an invalid path"
        );
    }

    /**
     * @dataProvider provideInferenceCommandNames
     */
    public function testInferenceInvalidOutputTypeMustFail(string $command): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(
            [$command, '-m', 'some-model-id', '-k', 'fake-key', '-o', 'garbage', $this->filePath],
            ['MINDEE_V2_API_KEY' => false]
        );
        self::assertSame(1, $cmdOutput['code']);
        self::assertStringContainsString(
            "Invalid output type 'garbage'",
            implode("\n", $cmdOutput['output']),
            "Command '$command' must complain about an invalid output type"
        );
    }

    public function testExtractionExposesAllExtractionOptions(): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(['extraction', '--help']);
        self::assertSame(0, $cmdOutput['code']);
        $stdout = implode("\n", $cmdOutput['output']);
        self::assertStringContainsString('--rag', $stdout);
        self::assertStringContainsString('--raw-text', $stdout);
        self::assertStringContainsString('--confidence', $stdout);
        self::assertStringContainsString('--polygon', $stdout);
        self::assertStringContainsString('--text-context', $stdout);
        self::assertStringContainsString('--alias', $stdout);
        self::assertStringContainsString('--model-id', $stdout);
        self::assertStringContainsString('--api-key', $stdout);
        self::assertStringContainsString('--output', $stdout);
    }

    /**
     * Sibling V2 commands must NOT expose extraction-only options.
     *
     * @return array<string, array{0: string}> Non-extraction inference commands.
     */
    public static function provideNonExtractionCommandsHideExtractionOnlyOptionsCases(): iterable
    {
        return [
            'classification' => ['classification'],
            'crop' => ['crop'],
            'ocr' => ['ocr'],
            'split' => ['split'],
        ];
    }

    /**
     * @dataProvider provideNonExtractionCommandsHideExtractionOnlyOptionsCases
     */
    public function testNonExtractionCommandsHideExtractionOnlyOptions(string $command): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest([$command, '--help']);
        self::assertSame(0, $cmdOutput['code']);
        $stdout = implode("\n", $cmdOutput['output']);
        self::assertStringNotContainsString('--rag', $stdout);
        self::assertStringNotContainsString('--raw-text', $stdout);
        self::assertStringNotContainsString('--confidence', $stdout);
        self::assertStringNotContainsString('--polygon', $stdout);
        self::assertStringNotContainsString('--text-context', $stdout);
        // Common options must still be present.
        self::assertStringContainsString('--alias', $stdout);
        self::assertStringContainsString('--model-id', $stdout);
        self::assertStringContainsString('--api-key', $stdout);
        self::assertStringContainsString('--output', $stdout);
    }

    public function testSearchModelsHelpExposesExpectedOptions(): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(['search-models', '--help']);
        self::assertSame(0, $cmdOutput['code']);
        $stdout = implode("\n", $cmdOutput['output']);
        self::assertStringContainsString('--api-key', $stdout);
        self::assertStringContainsString('--name', $stdout);
        self::assertStringContainsString('--model-type', $stdout);
        self::assertStringContainsString('--raw-json', $stdout);
    }

    public function testSearchModelsMissingApiKeyMustFail(): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(
            ['search-models'],
            ['MINDEE_V2_API_KEY' => false]
        );
        self::assertSame(1, $cmdOutput['code']);
        self::assertStringContainsString(
            'API key is missing',
            implode("\n", $cmdOutput['output'])
        );
    }

    public function testV1BackwardCompatibilityDispatch(): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(
            ['financial-document', 'invalid-file-path', '-k', 'fake-key', '-D'],
            ['MINDEE_API_KEY' => false]
        );
        self::assertSame(1, $cmdOutput['code']);
        self::assertStringContainsString(
            "Invalid path or url provided 'invalid-file-path'",
            implode("\n", $cmdOutput['output']),
            'Legacy `mindee <v1product> ...` dispatch must keep working'
        );
    }

    public function testCliHidesErrorLogOutputByDefault(): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(
            ['financial-document', $this->filePath, '-k', 'fake-key', '-D'],
            ['MINDEE_API_KEY' => false]
        );
        self::assertSame(0, $cmdOutput['code']);
        self::assertStringNotContainsString(
            'PHP Warning',
            implode("\n", $cmdOutput['output'])
        );
    }

    public function testCliDisplaysErrorLogOutputWithVerbosity(): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(
            ['-v', 'financial-document', $this->filePath, '-k', 'fake-key', '-D'],
            ['MINDEE_API_KEY' => false]
        );
        self::assertSame(0, $cmdOutput['code']);
        self::assertStringContainsString(
            'PHP Warning',
            implode("\n", $cmdOutput['output'])
        );
    }

    public function testCliDisplaysErrorLogOutputWhenRequested(): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(
            ['--error-log', 'financial-document', $this->filePath, '-k', 'fake-key', '-D'],
            ['MINDEE_API_KEY' => false]
        );
        self::assertSame(0, $cmdOutput['code']);
        self::assertStringContainsString(
            'PHP Warning',
            implode("\n", $cmdOutput['output'])
        );
    }

    public function testV1ExplicitGroupInvocation(): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(
            ['v1', 'financial-document', 'invalid-file-path', '-k', 'fake-key', '-D'],
            ['MINDEE_API_KEY' => false]
        );
        self::assertSame(1, $cmdOutput['code']);
        self::assertStringContainsString(
            "Invalid path or url provided 'invalid-file-path'",
            implode("\n", $cmdOutput['output'])
        );
    }
}

<?php

declare(strict_types=1);

namespace V2\Cli;

require_once(__DIR__ . '/../../../vendor/autoload.php');
require_once(__DIR__ . '/MindeeCliV2TestingUtilities.php');
require_once(__DIR__ . '/../../TestingUtilities.php');

use PHPUnit\Framework\TestCase;
use TestingUtilities;

/**
 * Functional / integration CLI tests for the V2 commands. These tests
 * actually call the Mindee V2 API and require credentials and model
 * IDs to be present in the environment.
 */
class MindeeCliCommandV2TestFunctional extends TestCase
{
    private string $apiKey;
    private string $filePath;

    protected function setUp(): void
    {
        $this->filePath = TestingUtilities::getFileTypesDir() . '/pdf/blank_1.pdf';
        $this->apiKey = (string) getenv('MINDEE_V2_API_KEY');
        if ($this->apiKey === '') {
            self::markTestSkipped('MINDEE_V2_API_KEY is not set; skipping V2 functional CLI tests.');
        }
    }

    /**
     * @return iterable<int, array{0: string, 1: string, 2: array<int, string>}>
     *                                                                           Tuples of [command name, env var holding the model ID, additional args].
     */
    public static function provideInferenceCommandSummaryOutputCases(): iterable
    {
        yield ['classification', 'MINDEE_V2_SE_TESTS_CLASSIFICATION_MODEL_ID', []];
        yield ['crop', 'MINDEE_V2_SE_TESTS_CROP_MODEL_ID', []];
        yield ['extraction', 'MINDEE_V2_SE_TESTS_FINDOC_MODEL_ID', []];
        yield ['ocr', 'MINDEE_V2_SE_TESTS_OCR_MODEL_ID', []];
        yield ['split', 'MINDEE_V2_SE_TESTS_SPLIT_MODEL_ID', []];
    }

    /**
     * @dataProvider provideInferenceCommandSummaryOutputCases
     */
    public function testInferenceCommandSummaryOutput(string $command, string $modelEnv, array $extra): void
    {
        $modelId = (string) getenv($modelEnv);
        if ($modelId === '') {
            self::markTestSkipped("$modelEnv is not set; skipping $command CLI test.");
        }

        $args = array_merge(
            [$command, '-m', $modelId, '-k', $this->apiKey],
            $extra,
            [$this->filePath]
        );
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest($args);
        self::assertSame(
            0,
            $cmdOutput['code'],
            "$command summary call must succeed:\n" . implode("\n", $cmdOutput['output'])
        );
        self::assertNotEmpty($cmdOutput['output'], "$command must produce output");
    }

    public function testExtractionFullOutputWithRawText(): void
    {
        $modelId = (string) getenv('MINDEE_V2_SE_TESTS_FINDOC_MODEL_ID');
        if ($modelId === '') {
            self::markTestSkipped('MINDEE_V2_SE_TESTS_FINDOC_MODEL_ID is not set.');
        }

        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(
            [
                'extraction',
                '-m', $modelId,
                '-k', $this->apiKey,
                '-r',
                '-o', 'full',
                $this->filePath,
            ]
        );
        self::assertSame(
            0,
            $cmdOutput['code'],
            "extraction full call must succeed:\n" . implode("\n", $cmdOutput['output'])
        );
        $stdout = implode("\n", $cmdOutput['output']);
        self::assertStringContainsString('Inference', $stdout);
    }

    public function testExtractionRawJsonOutput(): void
    {
        $modelId = (string) getenv('MINDEE_V2_SE_TESTS_FINDOC_MODEL_ID');
        if ($modelId === '') {
            self::markTestSkipped('MINDEE_V2_SE_TESTS_FINDOC_MODEL_ID is not set.');
        }

        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(
            [
                'extraction',
                '-m', $modelId,
                '-k', $this->apiKey,
                '-o', 'raw',
                $this->filePath,
            ]
        );
        self::assertSame(
            0,
            $cmdOutput['code'],
            "extraction raw call must succeed:\n" . implode("\n", $cmdOutput['output'])
        );
        $stdout = implode("\n", $cmdOutput['output']);
        self::assertStringContainsString('"inference"', $stdout, 'Raw JSON output must contain "inference"');
    }

    public function testSearchModelsHumanReadableOutput(): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(
            ['search-models', '-k', $this->apiKey]
        );
        self::assertSame(
            0,
            $cmdOutput['code'],
            "search-models must succeed:\n" . implode("\n", $cmdOutput['output'])
        );
        $stdout = implode("\n", $cmdOutput['output']);
        self::assertStringContainsString('Models', $stdout);
        self::assertStringContainsString('Pagination', $stdout);
    }

    public function testSearchModelsRawJsonOutput(): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(
            ['search-models', '-k', $this->apiKey, '-r']
        );
        self::assertSame(
            0,
            $cmdOutput['code'],
            "search-models raw must succeed:\n" . implode("\n", $cmdOutput['output'])
        );
        $stdout = implode("\n", $cmdOutput['output']);
        self::assertStringContainsString('"models"', $stdout);
        self::assertStringContainsString('"pagination"', $stdout);
    }

    public function testInferenceWithInvalidApiKeyMustFail(): void
    {
        $cmdOutput = MindeeCliV2TestingUtilities::executeTest(
            [
                'extraction',
                '-m', 'fc405e37-4ba4-4d03-aeba-533a8d1f0f21',
                '-k', 'invalid-api-key',
                $this->filePath,
            ]
        );
        self::assertSame(1, $cmdOutput['code']);
        self::assertNotEmpty($cmdOutput['output']);
    }
}

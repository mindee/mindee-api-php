<?php

declare(strict_types=1);

namespace V2\ClientOptions;

use Mindee\V2\ClientOptions\BaseClassificationProductParameters;
use PHPUnit\Framework\TestCase;
use TypeError;

class BaseClassificationProductParametersTest extends TestCase
{
    public function testSerialization(): void
    {
        $params = new class ('model-id', 'alias', ['first-id', 'second-id']) extends BaseClassificationProductParameters {
            public static string $slug = 'test';
        };

        $expected = ['model_id' => 'model-id', 'alias' => 'alias', 'webhook_ids' => 'first-id,second-id'];
        self::assertNull($params->extractionTextContext);
        self::assertSame($expected, $params->getRequestParameters());

        $params->extractionTextContext = 'context';
        self::assertSame($expected + ['extraction_text_context' => 'context'], $params->getRequestParameters());
        $params->extractionTextContext = '';
        self::assertSame($expected + ['extraction_text_context' => ''], $params->getRequestParameters());
        $params->extractionTextContext = null;
        self::assertSame($expected, $params->getRequestParameters());
    }

    public function testRejectsNonStringContext(): void
    {
        $this->expectException(TypeError::class);
        new class ('model-id', null, null, 123) extends BaseClassificationProductParameters {
            public static string $slug = 'test';
        };
    }
}

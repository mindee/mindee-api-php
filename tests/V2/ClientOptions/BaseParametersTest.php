<?php

declare(strict_types=1);

namespace V2\ClientOptions;

use Mindee\V2\ClientOptions\BaseParameters;
use PHPUnit\Framework\TestCase;

class BaseParametersTest extends TestCase
{
    public function testAsHashShouldSerializeMultipleWebhookIdsAsIndexedFields(): void
    {
        $params = new class ('model-id', null, ['first-id', 'second-id']) extends BaseParameters {
            public static string $slug = 'test';
        };

        $hash = $params->asHash();

        self::assertArrayHasKey('model_id', $hash);
        self::assertArrayHasKey('webhook_ids', $hash);
        self::assertSame('model-id', $hash['model_id']);
        self::assertSame('first-id,second-id', $hash['webhook_ids']);
    }
}

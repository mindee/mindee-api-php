<?php

namespace V2\ClientOptions;

use Mindee\V2\ClientOptions\BaseParameters;
use PHPUnit\Framework\TestCase;

class BaseParametersTest extends TestCase
{
    public function testAsHashShouldSerializeMultipleWebhookIdsAsIndexedFields(): void
    {
        $params = new class ('model-id', null, ['first-id', 'second-id'], null) extends BaseParameters {
            public static string $slug = 'test';
        };

        $hash = $params->asHash();

        $this->assertArrayHasKey('model_id', $hash);
        $this->assertArrayHasKey('webhook_ids', $hash);
        $this->assertSame('model-id', $hash['model_id']);
        $this->assertSame('first-id,second-id', $hash['webhook_ids']);
    }
}

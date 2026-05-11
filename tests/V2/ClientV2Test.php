<?php

namespace V2;

use Mindee\Error\MindeeException;
use Mindee\V2\HTTP\MindeeAPIV2;
use Mindee\Input\InferenceParameters;
use Mindee\Input\LocalInputSource;
use Mindee\Input\LocalResponse;
use Mindee\Input\PathInput;
use Mindee\V2\Client;
use Mindee\V2\Parsing\InferenceResponse;
use Mindee\V2\Parsing\JobResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ClientV2Test extends TestCase
{
    private static function makeClientWithMockedApi(MindeeAPIV2 $mockedApi): Client
    {
        $client = new Client("dummy");
        $reflection = new \ReflectionClass($client);
        $property = $reflection->getProperty('mindeeApi');
        $property->setAccessible(true);
        $property->setValue($client, $mockedApi);
        return $client;
    }

    public function testEnqueuePostAsync(): void
    {
        $predictable = $this->createMock(MindeeAPIV2::class);
        $syntheticResponse = file_get_contents(\TestingUtilities::getV2DataDir() . '/job/ok_processing.json');
        $predictable->expects($this->once())
            ->method('reqPostEnqueue')
            ->with(
                $this->isInstanceOf(LocalInputSource::class),
                $this->isInstanceOf(InferenceParameters::class)
            )
            ->willReturn(new JobResponse(json_decode($syntheticResponse, true)));

        $mindeeClient = self::makeClientWithMockedApi($predictable);

        $input = new PathInput(\TestingUtilities::getFileTypesDir() . '/pdf/blank_1.pdf');
        $params = new InferenceParameters('dummy-model-id', textContext: 'dummy text context');

        $response = $mindeeClient->enqueueInference($input, $params);

        $this->assertNotNull($response, 'enqueue() must return a response');
        $this->assertInstanceOf(JobResponse::class, $response);
    }

    public function testDocumentGetJobAsync(): void
    {
        /** @var MindeeAPIV2&MockObject $predictable */
        $predictable = $this->createMock(MindeeAPIV2::class);

        $syntheticResponse = file_get_contents(\TestingUtilities::getV2DataDir() . '/job/ok_processing.json');
        $processing = new JobResponse(json_decode($syntheticResponse, true));

        $predictable->expects($this->once())
            ->method('reqGetJob')
            ->with($this->equalTo('dummy-id'))
            ->willReturn($processing);

        $mindeeClient = self::makeClientWithMockedApi($predictable);

        $response = $mindeeClient->getJob('dummy-id');

        $this->assertNotNull($response, 'must return a response');
        $this->assertNotNull($response->job, 'job must return a response');
    }

    public function testDocumentGetInferenceAsync(): void
    {
        /** @var MindeeAPIV2&MockObject $predictable */
        $predictable = $this->createMock(MindeeAPIV2::class);

        $jsonFile = \TestingUtilities::getV2DataDir() . '/products/extraction/financial_document/complete.json';
        $this->assertFileExists($jsonFile, 'Test resource file must exist');

        $json = json_decode(file_get_contents($jsonFile), true);
        $processing = new InferenceResponse($json);

        $predictable->expects($this->once())
            ->method('reqGetInference')
            ->with($this->equalTo('12345678-1234-1234-1234-123456789abc'))
            ->willReturn($processing);

        $mindeeClient = self::makeClientWithMockedApi($predictable);

        $response = $mindeeClient->getInference('12345678-1234-1234-1234-123456789abc');

        $this->assertNotNull($response, 'must have a response');
        $this->assertNotNull($response->inference, 'inference must have a response');

        $fields = $response->inference->result->fields ?? [];
        $this->assertCount(
            21,
            $fields,
            'Result must have 21 fields'
        );

        $supplierName = $fields['supplier_name']->value ?? null;
        $this->assertEquals(
            'John Smith',
            $supplierName,
            'Result "' . $supplierName . '" must deserialize fields properly.'
        );
    }

    public function testInferenceLoadsLocally(): void
    {
        $jsonFile = \TestingUtilities::getV2DataDir() . '/products/extraction/financial_document/complete.json';
        $this->assertFileExists($jsonFile, 'Test resource file must exist');

        $localResponse = new LocalResponse($jsonFile);
        $loaded = $localResponse->deserializeResponse(InferenceResponse::class);

        $this->assertNotNull($loaded, 'Loaded InferenceResponse must not be null');
        $this->assertInstanceOf(InferenceResponse::class, $loaded);

        $modelId = $loaded->inference->model->id ?? null;
        $this->assertEquals(
            '12345678-1234-1234-1234-123456789abc',
            $modelId,
            'Model Id mismatch'
        );

        $supplierName = $loaded->inference->result->fields['supplier_name']->value ?? null;
        $this->assertEquals(
            'John Smith',
            $supplierName,
            'Supplier name mismatch'
        );
    }
    public function testInvalidBaseUrlRaisesMindeeException(): void
    {
        $this->expectException(MindeeException::class);

        $original = getenv('MINDEE_V2_BASE_URL') ?: null;
        putenv('MINDEE_V2_BASE_URL=https://invalid-v2.mindee.net');

        try {
            $client = new Client('dummy-key');
            $input = new PathInput(\TestingUtilities::getFileTypesDir() . '/pdf/blank_1.pdf');
            $params = new InferenceParameters('dummy-model-id');
            $client->enqueueAndGetInference($input, $params);
        } finally {
            if ($original === null) {
                putenv('MINDEE_V2_BASE_URL');
            } else {
                putenv('MINDEE_V2_BASE_URL=' . $original);
            }
        }
    }
}

<?php

namespace V2;

use Mindee\ClientV2;
use Mindee\Http\MindeeApiV2;
use Mindee\Input\InferenceParameters;
use Mindee\Input\LocalInputSource;
use Mindee\Input\LocalResponse;
use Mindee\Input\PathInput;
use Mindee\Parsing\V2\InferenceResponse;
use Mindee\Parsing\V2\JobResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ClientV2Test extends TestCase
{
    private static function makeClientWithMockedApi(MindeeApiV2 $mockedApi): ClientV2
    {
        $client = new ClientV2("dummy");
        $reflection = new \ReflectionClass($client);
        $property = $reflection->getProperty('mindeeApi');
        $property->setAccessible(true);
        $property->setValue($client, $mockedApi);
        return $client;
    }

    public function testEnqueuePostAsync(): void
    {
        $predictable = $this->createMock(MindeeApiV2::class);
        $syntheticResponse = file_get_contents(\TestingUtilities::getV2DataDir() . '/job/ok_processing.json');
        $predictable->expects($this->once())
            ->method('reqPostInferenceEnqueue')
            ->with(
                $this->isInstanceOf(LocalInputSource::class),
                $this->isInstanceOf(InferenceParameters::class)
            )
            ->willReturn(new JobResponse(json_decode($syntheticResponse, true)));

        $mindeeClient = self::makeClientWithMockedApi($predictable);

        $input = new PathInput(\TestingUtilities::getFileTypesDir() . '/pdf/blank_1.pdf');
        $params = new InferenceParameters('dummy-model-id');

        $response = $mindeeClient->enqueueInference($input, $params);

        $this->assertNotNull($response, 'enqueue() must return a response');
        $this->assertInstanceOf(JobResponse::class, $response);
    }

    public function testDocumentGetJobAsync(): void
    {
        /** @var MindeeApiV2&MockObject $predictable */
        $predictable = $this->createMock(MindeeApiV2::class);

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
        /** @var MindeeApiV2&MockObject $predictable */
        $predictable = $this->createMock(MindeeApiV2::class);

        $jsonFile = \TestingUtilities::getV2DataDir() . '/products/financial_document/complete.json';
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
        $jsonFile = \TestingUtilities::getV2DataDir() . '/products/financial_document/complete.json';
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
}

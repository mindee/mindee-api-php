<?php

declare(strict_types=1);

namespace V2;

use Mindee\Error\MindeeException;
use Mindee\Input\LocalInputSource;
use Mindee\Input\LocalResponse;
use Mindee\Input\PathInput;
use Mindee\V2\Client;
use Mindee\V2\HTTP\MindeeAPIV2;
use Mindee\V2\Parsing\JobResponse;
use Mindee\V2\Product\Extraction\ExtractionResponse;
use Mindee\V2\Product\Extraction\Params\ExtractionParameters;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use TestingUtilities;

class ClientV2Test extends TestCase
{
    private static function makeClientWithMockedApi(MindeeAPIV2 $mockedApi): Client
    {
        $client = new Client("dummy");
        $reflection = new ReflectionClass($client);
        $property = $reflection->getProperty('mindeeApi');
        $property->setAccessible(true);
        $property->setValue($client, $mockedApi);
        return $client;
    }

    public function testEnqueuePostAsync(): void
    {
        $predictable = $this->createMock(MindeeAPIV2::class);
        $syntheticResponse = file_get_contents(TestingUtilities::getV2DataDir() . '/job/ok_processing.json');
        $predictable->expects(self::once())
            ->method('reqPostEnqueue')
            ->with(
                self::isInstanceOf(LocalInputSource::class),
                self::isInstanceOf(ExtractionParameters::class)
            )
            ->willReturn(new JobResponse(json_decode($syntheticResponse, true)));

        $mindeeClient = self::makeClientWithMockedApi($predictable);

        $input = new PathInput(TestingUtilities::getFileTypesDir() . '/pdf/blank_1.pdf');
        $params = new ExtractionParameters('dummy-model-id', textContext: 'dummy text context');

        $response = $mindeeClient->enqueueInference($input, $params);

        self::assertNotNull($response, 'enqueue() must return a response');
        self::assertInstanceOf(JobResponse::class, $response);
    }

    public function testDocumentGetJobAsync(): void
    {
        /** @var MindeeAPIV2&MockObject $predictable */
        $predictable = $this->createMock(MindeeAPIV2::class);

        $syntheticResponse = file_get_contents(TestingUtilities::getV2DataDir() . '/job/ok_processing.json');
        $processing = new JobResponse(json_decode($syntheticResponse, true));

        $predictable->expects(self::once())
            ->method('reqGetJob')
            ->with(self::equalTo('dummy-id'))
            ->willReturn($processing);

        $mindeeClient = self::makeClientWithMockedApi($predictable);

        $response = $mindeeClient->getJob('dummy-id');

        self::assertNotNull($response, 'must return a response');
        self::assertNotNull($response->job, 'job must return a response');
    }

    public function testDocumentGetInferenceAsync(): void
    {
        /** @var MindeeAPIV2&MockObject $predictable */
        $predictable = $this->createMock(MindeeAPIV2::class);

        $jsonFile = TestingUtilities::getV2DataDir() . '/products/extraction/financial_document/complete.json';
        self::assertFileExists($jsonFile, 'Test resource file must exist');

        $json = json_decode(file_get_contents($jsonFile), true);
        $processing = new ExtractionResponse($json);

        $predictable->expects(self::once())
            ->method('reqGetResult')
            ->with(
                self::equalTo(ExtractionResponse::class),
                self::equalTo('12345678-1234-1234-1234-123456789abc')
            )
            ->willReturn($processing);

        $mindeeClient = self::makeClientWithMockedApi($predictable);

        $response = $mindeeClient->getResult(ExtractionResponse::class, '12345678-1234-1234-1234-123456789abc');

        self::assertNotNull($response, 'must have a response');
        self::assertNotNull($response->inference, 'inference must have a response');

        $fields = $response->inference->result->fields ?? [];
        self::assertCount(
            21,
            $fields,
            'Result must have 21 fields'
        );

        $supplierName = $fields['supplier_name']->value ?? null;
        self::assertSame(
            'John Smith',
            $supplierName,
            'Result "' . $supplierName . '" must deserialize fields properly.'
        );
    }

    public function testInferenceLoadsLocally(): void
    {
        $jsonFile = TestingUtilities::getV2DataDir() . '/products/extraction/financial_document/complete.json';
        self::assertFileExists($jsonFile, 'Test resource file must exist');

        $localResponse = new LocalResponse($jsonFile);
        $loaded = $localResponse->deserializeResponse(ExtractionResponse::class);

        self::assertNotNull($loaded, 'Loaded ExtractionResponse must not be null');
        self::assertInstanceOf(ExtractionResponse::class, $loaded);

        $modelId = $loaded->inference->model->id ?? null;
        self::assertSame(
            '12345678-1234-1234-1234-123456789abc',
            $modelId,
            'Model Id mismatch'
        );

        $supplierName = $loaded->inference->result->fields['supplier_name']->value ?? null;
        self::assertSame(
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
            $input = new PathInput(TestingUtilities::getFileTypesDir() . '/pdf/blank_1.pdf');
            $params = new ExtractionParameters('dummy-model-id');
            $client->enqueueAndGetResult(ExtractionResponse::class, $input, $params);
        } finally {
            if ($original == null) {
                putenv('MINDEE_V2_BASE_URL');
            } else {
                putenv('MINDEE_V2_BASE_URL=' . $original);
            }
        }
    }
}

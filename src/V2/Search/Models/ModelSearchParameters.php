<?php

declare(strict_types=1);

namespace Mindee\V2\Search\Models;

use Mindee\V2\ClientOptions\BaseSearchParameters;

/**
 * Search for models within the organization linked to the API key.
 *
 * All search filters are optional.
 * If no search filters are given, all models belonging to the organization are returned.
 *
 * Results are paginated.
 *
 * @extends BaseSearchParameters<ModelSearchResponse>
 */
class ModelSearchParameters extends BaseSearchParameters
{
    /**
     * @var string Slug of the resource.
     */
    public static string $slug = "models";

    /**
     * @var class-string<ModelSearchResponse> Response class.
     */
    protected static string $responseClass = ModelSearchResponse::class;

    /**
     * @param string|null $name Case-insensitive search term for the model name.
     * @param string|null $modelType Case-insensitive search term for the model type.
     * @param integer|null $page 1-based page index.
     * @param integer|null $perPage Number of items per page.
     */
    public function __construct(
        public ?string $name = null,
        public ?string $modelType = null,
        ?int $page = null,
        ?int $perPage = null
    ) {
        parent::__construct($page, $perPage);
    }

    /**
     * @return array<string, string> Query parameters.
     */
    public function getRequestParameters(): array
    {
        $params = parent::getRequestParameters();
        if (!empty($this->name)) {
            $params['name'] = $this->name;
        }
        if (!empty($this->modelType)) {
            $params['model_type'] = $this->modelType;
        }
        return $params;
    }
}

<?php

declare(strict_types=1);

namespace Mindee\V2\Search\Models;

use Mindee\V2\ClientOptions\BaseSearchParameters;

/**
 * Search parameters for models.
 */
class ModelSearchParameters extends BaseSearchParameters
{
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
    public function getQueryParams(): array
    {
        $params = parent::getQueryParams();
        if (!empty($this->name)) {
            $params['name'] = $this->name;
        }
        if (!empty($this->modelType)) {
            $params['model_type'] = $this->modelType;
        }
        return $params;
    }
}

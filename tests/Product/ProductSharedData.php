<?php

namespace Product;

class ProductSharedData
{
    public static function getProductDataDir(): string
    {
        return (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/";
    }
}

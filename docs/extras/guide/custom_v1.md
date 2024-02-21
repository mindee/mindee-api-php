---
title: Custom API PHP
---
The PHP OCR SDK supports [custom-built APIs](https://developers.mindee.com/docs/build-your-first-document-parsing-api).
If your document isn't covered by one of Mindee's Off-the-Shelf APIs, you can create your own API using the[API Builder](https://platform.mindee.com/api-builder).

# Quick-Start

```php<?php

use Mindee\Client;
use Mindee\Product\Custom\CustomV1;
use Mindee\Input\PredictMethodOptions;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Create a custom endpoint
$customEndpoint = $mindeeClient->createEndpoint(
    "my-endpoint",
    "my-account",
//  "my-version" // Optional
);

// Add the custom endpoint to the prediction options.
$predictOptions = new PredictMethodOptions();
$predictOptions->setEndpoint($customEndpoint);

// Parse the file
$apiResponse = $mindeeClient->parse(CustomV1::class, $inputSource, $predictOptions);

echo strval($apiResponse->document);

// Iterate over all the fields in the document
foreach ($result->document->fields as field_name => field_values)
{
    echo "$field_name = $field_values";
}
```

# Custom Endpoints

You may have noticed in the previous step that in order to access a custom build, you will need to provide an account and an endpoint name at the very least.

Although it is optional, the version number should match the latest version of your build in most use-cases.
If it is not set, it will default to "1".


# Field Types

## Custom Fields

### List Field

A `ListField` is a special type of custom list that implements the following:

* **confidence** (`float`): the confidence score of the field prediction.
* **reconstructed** (`bool`): indicates whether or not an object was reconstructed (not extracted as the API gave it).
* **values** (`List[`[ListFieldValue](#list-field-value)`]`): list of value fields

Since the inner contents can vary, the value isn't accessed through a property, but rather through the following functions:
* **contentsList()** (`-> array`): returns a list of values for each element. Elements can be `float` or `string`.
* **contentsString($separator=" ")** (`-> string`): returns a list of concatenated values, with an optional **$separator** `string` between them.
* **__toString()**: returns a string representation of all values, with an empty space between each of them.


#### List Field Value

Values of `ListField`s are stored in a `ListFieldValue` structure, which is implemented as follows:
* **content** (`string`): extracted content of the prediction
* **confidence** (`float`): the confidence score of the prediction
* **bounding_box** (`BBox`): 4 relative vertices corrdinates of a rectangle containing the word in the document.
* **polygon** (`Polygon`): vertices of a polygon containing the word.
* **page** (`int`): the ID of the page, not applicable when at document-level.


### Classification Field

A `ClassificationField` is a special type of custom classification that implements the following:

* **value** (`string`): the value of the classification. Corresponds to one of the values specified during training.
* **confidence** (`float`): the confidence score of the field prediction.
* **__toString()**: returns a string representation of all values, with an empty space between each of them.

# Attributes

Custom builds always have access to at least two attributes:

## Fields

**fields** (array(`string`=> [ListField](#list-field)[])):

```php
echo strval($result->document->inference->prediction->fields["my-field"]);
```

## Classifications

**classifications** (array(`string` => [ClassificationField](#classification-field)[])): The purchase category among predefined classes.

```php
print(string(result.document.inference.prediction.classifications["my-classification"]))
```


# 🧪 Custom Line Items

> **⚠️ Warning**: Custom Line Items are an **experimental** feature, results may vary.


Though not supported directly in the API, sometimes you might need to reconstitute line items by hand.
The library provides a tool for this very purpose:

## columnsToLineItems()
The **columnsToLineItems()** function can be called from the document and page level prediction objects.

It takes the following arguments:

* **$anchorNames** (`string[]`): a list of the names of possible anchor (field) candidate for the horizontal placement a line. If all provided anchors are invalid, the `CustomLine` won't be built.
* **$fieldNames** (`string[]`): a list of fields to retrieve the values from
* **$heightTolerance** (`?float`): Optional, the height tolerance used to build the line. It helps when the height of a line can vary unexpectedly.

Example use:

```php
// document-level
$response->document->inference->prediction->columnsToLineItems(
    $anchorNames,
    $fieldNames,
    0.011 // optional, defaults to 0.01
);

// page-level
$response->document->pages[0]->prediction->columnsToLineItems(
    $anchorNames,
    $fieldNames,
    0.011 // optional, defaults to 0.01
);
```

It returns a list of [CustomLine](#CustomLine) objects.

## CustomLine

`CustomLine` represents a line as it has been read from column fields. It has the following attributes:

* **rowNumber** (`int`): Number of a given line. Starts at 1.
* **fields** (`array(string => ListFieldValue)[]`): List of the fields associated with the line, indexed by their column name.
* **bbox** (`BBox`): Simple bounding box of the current line representing the 4 minimum & maximum coordinates as `float` values.


# Questions?

[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)

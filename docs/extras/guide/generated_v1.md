---
title: Generated API PHP
---
The PHP OCR SDK supports generated APIs.
Generated APIs can theoretically support all APIs in a catch-all generic format.

# Quick-Start

```php
<?php

use Mindee\Client;
use Mindee\Product\Generated\GeneratedV1;
use Mindee\Input\PredictMethodOptions;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Create a custom endpoint
$customEndpoint = $mindeeClient->createEndpoint(
    "my-endpoint",
    "my-account",
    "my-version"
);

// Add the custom endpoint to the prediction options.
$predictOptions = new PredictMethodOptions();
$predictOptions->setEndpoint($customEndpoint);

// Parse the file
$apiResponse = $mindeeClient->enqueueAndParse(GeneratedV1::class, $inputSource, $predictOptions);

echo strval($apiResponse->document);
```

# Generated Endpoints

As shown above, you will need to provide an account and an endpoint name at the very least.

Although it is optional, the version number should match the latest version of your build in most use-cases.
If it is not set, it will default to "1".

# Field Types

## Generated Fields

### Generated List Field

A `GeneratedListField` is a special type of custom list that implements the following:

- **values** (`StringField|`[GeneratedObjectField](#Generated-object-field)): the confidence score of the field prediction.
- **pageId** (`integer`): only available for some documents ATM.

Since the inner contents can vary, the value isn't accessed through a property, but rather through the following functions:

- **contentsList()** (`-> string|float`): returns a list of values for each element.
- **contentsString($separator=" ")** (`-> string`): returns a list of concatenated values, with an optional **separator** `string` between them.
> **Note:** the `__toString()` method returns a string representation of all values of this object, with an empty space between each of them.

### Generated Object Field

Unrecognized structures and sometimes values of `ListField`s are stored in a `GeneratedObjectField` structure, which is implemented dynamically depending on the object's structure.

- **pageId** (`integer?`): the ID of the page, is `null` when at document-level.
- **rawValue** (`string?`): an optional field for when some post-processing has been done on fields (e.g. amounts). `null` in most instances.
- **confidence** (`float?`): the confidence score of the field prediction. Warning: support isn't guaranteed on all APIs.


> **Other fields**:No matter what, other fields will be stored in a dictionary-like structure with a `key: value` pair where `key` is a string and `value` is a nullable string. They can be accessed like any other regular value, but won't be suggested by your IDE.


### StringField
The text field `StringField` only has one constraint: its **value** is an `string?`.


# Attributes

Generated builds always have access to at least two attributes:

## Fields

**fields** (`array<string`=> [GeneratedListField](#generated-list-field)|[GeneratedObjectField](#generated-object-field)|(#stringfield)[StringField]`>`):

```php
echo $result->document->inference->prediction->fields["my-field"]);
```

# Questions?

[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)

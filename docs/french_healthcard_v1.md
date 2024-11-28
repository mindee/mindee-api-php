---
title: FR Health Card OCR PHP
category: 622b805aaec68102ea7fcbc2
slug: php-fr-health-card-ocr
parentDoc: 658193df8e029d002ad9c89b
---
The PHP OCR SDK supports the [Health Card API](https://platform.mindee.com/mindee/french_healthcard).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/french_healthcard/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Health Card sample](https://github.com/mindee/client-lib-test-data/blob/main/products/french_healthcard/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\Fr\HealthCard\HealthCardV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file asynchronously
$apiResponse = $mindeeClient->enqueueAndParse(HealthCardV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: 9ee2733d-933a-4dcd-a73a-a31395e3b288
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/french_healthcard v1.0
:Rotation applied: Yes

Prediction
==========
:Given Name(s): NATHALIE
:Surname: DURAND
:Social Security Number: 2 69 05 49 588 157 80
:Issuance Date: 2007-01-01
```

# Field Types
## Standard Fields
These fields are generic and used in several products.

### BaseField
Each prediction object contains a set of fields that inherit from the generic `BaseField` class.
A typical `BaseField` object will have the following attributes:

* **value** (`float|string`): corresponds to the field value. Can be `null` if no value was extracted.
* **confidence** (`float`): the confidence score of the field prediction.
* **boundingBox** (`[Point, Point, Point, Point]`): contains exactly 4 relative vertices (points) coordinates of a right rectangle containing the field in the document.
* **polygon** (`Point[]`): contains the relative vertices coordinates (`Point`) of a polygon containing the field in the image.
* **pageId** (`integer`): the ID of the page, always `null` when at document-level.
* **reconstructed** (`bool`): indicates whether an object was reconstructed (not extracted as the API gave it).

> **Note:** A `Point` simply refers to a list of two numbers (`[float, float]`).


Aside from the previous attributes, all basic fields have access to a custom `__toString` method that can be used to print their value as a string.

### DateField
Aside from the basic `BaseField` attributes, the date field `DateField` also implements the following: 

* **dateObject** (`date`): an accessible representation of the value as a php object. Can be `null`.

### StringField
The text field `StringField` implements the following:
* **value** (`string`): represents the value of the field as a string.
* **rawValue** (`string`): the value of the string as it appears on the document.

# Attributes
The following fields are extracted for Health Card V1:

## Given Name(s)
**givenNames** : The given names of the card holder.

```php
foreach ($result->document->inference->prediction->givenNames as $givenNamesElem)
{
    echo $givenNamesElem->value;
}
```

## Issuance Date
**issuanceDate** : The date when the carte vitale document was issued.

```php
echo $result->document->inference->prediction->issuanceDate->value;
```

## Social Security Number
**socialSecurity** : The social security number of the card holder.

```php
echo $result->document->inference->prediction->socialSecurity->value;
```

## Surname
**surname** : The surname of the card holder.

```php
echo $result->document->inference->prediction->surname->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)

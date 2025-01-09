---
title: Driver License OCR PHP
category: 622b805aaec68102ea7fcbc2
slug: php-driver-license-ocr
parentDoc: 658193df8e029d002ad9c89b
---
The PHP OCR SDK supports the [Driver License API](https://platform.mindee.com/mindee/driver_license).

The [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/driver_license/default_sample.jpg) can be used for testing purposes.
![Driver License sample](https://github.com/mindee/client-lib-test-data/blob/main/products/driver_license/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\DriverLicense\DriverLicenseV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file asynchronously
$apiResponse = $mindeeClient->enqueueAndParse(DriverLicenseV1::class, $inputSource);

echo $apiResponse->document;
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
The following fields are extracted for Driver License V1:

## Category
**category** : The category or class of the driver license.

```php
echo $result->document->inference->prediction->category->value;
```

## Country Code
**countryCode** : The alpha-3 ISO 3166 code of the country where the driver license was issued.

```php
echo $result->document->inference->prediction->countryCode->value;
```

## Date of Birth
**dateOfBirth** : The date of birth of the driver license holder.

```php
echo $result->document->inference->prediction->dateOfBirth->value;
```

## DD Number
**ddNumber** : The DD number of the driver license.

```php
echo $result->document->inference->prediction->ddNumber->value;
```

## Expiry Date
**expiryDate** : The expiry date of the driver license.

```php
echo $result->document->inference->prediction->expiryDate->value;
```

## First Name
**firstName** : The first name of the driver license holder.

```php
echo $result->document->inference->prediction->firstName->value;
```

## ID
**id** : The unique identifier of the driver license.

```php
echo $result->document->inference->prediction->id->value;
```

## Issued Date
**issuedDate** : The date when the driver license was issued.

```php
echo $result->document->inference->prediction->issuedDate->value;
```

## Issuing Authority
**issuingAuthority** : The authority that issued the driver license.

```php
echo $result->document->inference->prediction->issuingAuthority->value;
```

## Last Name
**lastName** : The last name of the driver license holder.

```php
echo $result->document->inference->prediction->lastName->value;
```

## MRZ
**mrz** : The Machine Readable Zone (MRZ) of the driver license.

```php
echo $result->document->inference->prediction->mrz->value;
```

## Place of Birth
**placeOfBirth** : The place of birth of the driver license holder.

```php
echo $result->document->inference->prediction->placeOfBirth->value;
```

## State
**state** : Second part of the ISO 3166-2 code, consisting of two letters indicating the US State.

```php
echo $result->document->inference->prediction->state->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)

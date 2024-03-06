---
title: Passport OCR PHP
---
The PHP OCR SDK supports the [Passport API](https://platform.mindee.com/mindee/passport).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/passport/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Passport sample](https://github.com/mindee/client-lib-test-data/blob/main/products/passport/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\Passport\PassportV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file
$apiResponse = $mindeeClient->parse(PassportV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: 18e41f6c-16cd-4f8e-8cd2-00ca02a35764
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/passport v1.0
:Rotation applied: Yes

Prediction
==========
:Country Code: GBR
:ID Number: 707797979
:Given Name(s): HENERT
:Surname: PUDARSAN
:Date of Birth: 1995-05-20
:Place of Birth: CAMTETH
:Gender: M
:Date of Issue: 2012-04-22
:Expiry Date: 2017-04-22
:MRZ Line 1: P<GBRPUDARSAN<<HENERT<<<<<<<<<<<<<<<<<<<<<<<
:MRZ Line 2: 7077979792GBR9505209M1704224<<<<<<<<<<<<<<00

Page Predictions
================

Page 0
------
:Country Code: GBR
:ID Number: 707797979
:Given Name(s): HENERT
:Surname: PUDARSAN
:Date of Birth: 1995-05-20
:Place of Birth: CAMTETH
:Gender: M
:Date of Issue: 2012-04-22
:Expiry Date: 2017-04-22
:MRZ Line 1: P<GBRPUDARSAN<<HENERT<<<<<<<<<<<<<<<<<<<<<<<
:MRZ Line 2: 7077979792GBR9505209M1704224<<<<<<<<<<<<<<00
```

# Field Types
## Standard Fields
These fields are generic and used in several products.

### BasicField
Each prediction object contains a set of fields that inherit from the generic `BaseField` class.
A typical `BaseField` object will have the following attributes:

* **value** (`float|string`): corresponds to the field value. Can be `null` if no value was extracted.
* **confidence** (`float`): the confidence score of the field prediction.
* **boundingBox** (`[Point, Point, Point, Point]`): contains exactly 4 relative vertices (points) coordinates of a right rectangle containing the field in the document.
* **polygon** (`Point[]`): contains the relative vertices coordinates (`Point`) of a polygon containing the field in the image.
* **pageId** (`integer`): the ID of the page, is `null` when at document-level.
* **reconstructed** (`bool`): indicates whether an object was reconstructed (not extracted as the API gave it).

> **Note:** A `Point` simply refers to a List of two numbers (`[float, float]`).


Aside from the previous attributes, all basic fields have access to a custom `__toString` method that can be used to print their value as a string.

### DateField
Aside from the basic `BaseField` attributes, the date field `DateField` also implements the following: 

* **dateObject** (`date`): an accessible representation of the value as a php object. Can be `null`.

### StringField
The text field `StringField` only has one constraint: its **value** is an optional `?string`.

# Attributes
The following fields are extracted for Passport V1:

## Date of Birth
**birthDate** : The date of birth of the passport holder.

```php
echo $result->document->inference->prediction->birthDate->value;
```

## Place of Birth
**birthPlace** : The place of birth of the passport holder.

```php
echo $result->document->inference->prediction->birthPlace->value;
```

## Country Code
**country** : The country's 3 letter code (ISO 3166-1 alpha-3).

```php
echo $result->document->inference->prediction->country->value;
```

## Expiry Date
**expiryDate** : The expiry date of the passport.

```php
echo $result->document->inference->prediction->expiryDate->value;
```

## Gender
**gender** : The gender of the passport holder.

```php
echo $result->document->inference->prediction->gender->value;
```

## Given Name(s)
**givenNames** : The given name(s) of the passport holder.

```php
foreach ($result->document->inference->prediction->givenNames as $givenNamesElem)
{
    echo $givenNamesElem->value;
}
```

## ID Number
**idNumber** : The passport's identification number.

```php
echo $result->document->inference->prediction->idNumber->value;
```

## Date of Issue
**issuanceDate** : The date the passport was issued.

```php
echo $result->document->inference->prediction->issuanceDate->value;
```

## MRZ Line 1
**mrz1** : Machine Readable Zone, first line

```php
echo $result->document->inference->prediction->mrz1->value;
```

## MRZ Line 2
**mrz2** : Machine Readable Zone, second line

```php
echo $result->document->inference->prediction->mrz2->value;
```

## Surname
**surname** : The surname of the passport holder.

```php
echo $result->document->inference->prediction->surname->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)

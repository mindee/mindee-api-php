---
title: International ID OCR PHP
---
The PHP OCR SDK supports the [International ID API](https://platform.mindee.com/mindee/international_id).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/international_id/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![International ID sample](https://github.com/mindee/client-lib-test-data/blob/main/products/international_id/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\InternationalId\InternationalIdV2;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file asynchronously
$apiResponse = $mindeeClient->enqueueAndParse(InternationalIdV2::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: cfa20a58-20cf-43b6-8cec-9505fa69d1c2
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/international_id v2.0
:Rotation applied: No

Prediction
==========
:Document Type: IDENTIFICATION_CARD
:Document Number: 12345678A
:Surnames: MUESTRA
           MUESTRA
:Given Names: CARMEN
:Sex: F
:Birth Date: 1980-01-01
:Birth Place: CAMPO DE CRIPTANA CIUDAD REAL ESPANA
:Nationality: ESP
:Personal Number: BAB1834284<44282767Q0
:Country of Issue: ESP
:State of Issue: MADRID
:Issue Date:
:Expiration Date: 2030-01-01
:Address: C/REAL N13, 1 DCHA COLLADO VILLALBA MADRID MADRID MADRID
:MRZ Line 1: IDESPBAB1834284<44282767Q0<<<<
:MRZ Line 2: 8001010F1301017ESP<<<<<<<<<<<3
:MRZ Line 3: MUESTRA<MUESTRA<<CARMEN<<<<<<<
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


### ClassificationField
The classification field `ClassificationField` does not implement all the basic `BaseField` attributes. It only implements **value**, **confidence** and **pageId**.

> Note: a classification field's `value is always a `string`.

### DateField
Aside from the basic `BaseField` attributes, the date field `DateField` also implements the following: 

* **dateObject** (`date`): an accessible representation of the value as a php object. Can be `null`.

### StringField
The text field `StringField` only has one constraint: its **value** is an optional `?string`.

# Attributes
The following fields are extracted for International ID V2:

## Address
**address** : The physical address of the document holder.

```php
echo $result->document->inference->prediction->address->value;
```

## Birth Date
**birthDate** : The date of birth of the document holder.

```php
echo $result->document->inference->prediction->birthDate->value;
```

## Birth Place
**birthPlace** : The place of birth of the document holder.

```php
echo $result->document->inference->prediction->birthPlace->value;
```

## Country of Issue
**countryOfIssue** : The country where the document was issued.

```php
echo $result->document->inference->prediction->countryOfIssue->value;
```

## Document Number
**documentNumber** : The unique identifier assigned to the document.

```php
echo $result->document->inference->prediction->documentNumber->value;
```

## Document Type
**documentType** : The type of personal identification document.

```php
echo $result->document->inference->prediction->documentType->value;
```

## Expiration Date
**expiryDate** : The date when the document becomes invalid.

```php
echo $result->document->inference->prediction->expiryDate->value;
```

## Given Names
**givenNames** : The list of the document holder's given names.

```php
foreach ($result->document->inference->prediction->givenNames as $givenNamesElem)
{
    echo $givenNamesElem->value;
}
```

## Issue Date
**issueDate** : The date when the document was issued.

```php
echo $result->document->inference->prediction->issueDate->value;
```

## MRZ Line 1
**mrzLine1** : The Machine Readable Zone, first line.

```php
echo $result->document->inference->prediction->mrzLine1->value;
```

## MRZ Line 2
**mrzLine2** : The Machine Readable Zone, second line.

```php
echo $result->document->inference->prediction->mrzLine2->value;
```

## MRZ Line 3
**mrzLine3** : The Machine Readable Zone, third line.

```php
echo $result->document->inference->prediction->mrzLine3->value;
```

## Nationality
**nationality** : The country of citizenship of the document holder.

```php
echo $result->document->inference->prediction->nationality->value;
```

## Personal Number
**personalNumber** : The unique identifier assigned to the document holder.

```php
echo $result->document->inference->prediction->personalNumber->value;
```

## Sex
**sex** : The biological sex of the document holder.

```php
echo $result->document->inference->prediction->sex->value;
```

## State of Issue
**stateOfIssue** : The state or territory where the document was issued.

```php
echo $result->document->inference->prediction->stateOfIssue->value;
```

## Surnames
**surnames** : The list of the document holder's family names.

```php
foreach ($result->document->inference->prediction->surnames as $surnamesElem)
{
    echo $surnamesElem->value;
}
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)

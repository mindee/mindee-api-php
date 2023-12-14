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
use Mindee\Product\InternationalId\InternationalIdV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file
$apiResponse = $mindeeClient->parse(InternationalIdV1::class, $inputSource);

echo strval($apiResponse->document);
```

**Output (RST):**
```rst

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

### StringField
The text field `StringField` only has one constraint: its **value** is an optional `?string`.

# Attributes
The following fields are extracted for International ID V1:

## Address
**address** : The physical location of the document holder's residence.

```php
echo $result->document->inference->prediction->address->value;
```

## Birth date
**birthDate** : The date of birth of the document holder.

```php
echo $result->document->inference->prediction->birthDate->value;
```

## Birth Place
**birthPlace** : The location where the document holder was born.

```php
echo $result->document->inference->prediction->birthPlace->value;
```

## Country of Issue
**countryOfIssue** : The country that issued the identification document.

```php
echo $result->document->inference->prediction->countryOfIssue->value;
```

## Document Number
**documentNumber** : The unique identifier assigned to the identification document.

```php
echo $result->document->inference->prediction->documentNumber->value;
```

## Document Type
**documentType** : The type of identification document being used.

```php
echo $result->document->inference->prediction->documentType->value;
```

## Expiry Date
**expiryDate** : The date when the document will no longer be valid for use.

```php
echo $result->document->inference->prediction->expiryDate->value;
```

## Given Names
**givenNames** : The first names or given names of the document holder.

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

## Machine Readable Zone Line 1
**mrz1** : First line of information in a standardized format for easy machine reading and processing.

```php
echo $result->document->inference->prediction->mrz1->value;
```

## Machine Readable Zone Line 2
**mrz2** : Second line of information in a standardized format for easy machine reading and processing.

```php
echo $result->document->inference->prediction->mrz2->value;
```

## Machine Readable Zone Line 3
**mrz3** : Third line of information in a standardized format for easy machine reading and processing.

```php
echo $result->document->inference->prediction->mrz3->value;
```

## Nationality
**nationality** : Indicates the country of citizenship or nationality of the document holder.

```php
echo $result->document->inference->prediction->nationality->value;
```

## Gender
**sex** : The document holder's biological sex, such as male or female.

```php
echo $result->document->inference->prediction->sex->value;
```

## Surnames
**surnames** : The surnames of the document holder.

```php
foreach ($result->document->inference->prediction->surnames as $surnamesElem)
{
    echo $surnamesElem->value;
}
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-1jv6nawjq-FDgFcF2T5CmMmRpl9LLptw)

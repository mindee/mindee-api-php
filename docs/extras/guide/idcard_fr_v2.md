---
title: FR Carte Nationale d'Identité OCR PHP
---
The PHP OCR SDK supports the [Carte Nationale d'Identité API](https://platform.mindee.com/mindee/idcard_fr).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/idcard_fr/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Carte Nationale d'Identité sample](https://github.com/mindee/client-lib-test-data/blob/main/products/idcard_fr/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\Fr\IdCard\IdCardV2;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file
$apiResponse = $mindeeClient->parse(IdCardV2::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: d33828f1-ef7e-4984-b9df-a2bfaa38a78d
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/idcard_fr v2.0
:Rotation applied: Yes

Prediction
==========
:Nationality:
:Card Access Number: 175775H55790
:Document Number:
:Given Name(s): Victor
                Marie
:Surname: DAMBARD
:Alternate Name:
:Date of Birth: 1994-04-24
:Place of Birth: LYON 4E ARRONDISSEM
:Gender: M
:Expiry Date: 2030-04-02
:Mrz Line 1: IDFRADAMBARD<<<<<<<<<<<<<<<<<<075025
:Mrz Line 2: 170775H557903VICTOR<<MARIE<9404246M5
:Mrz Line 3:
:Date of Issue: 2015-04-03
:Issuing Authority: SOUS-PREFECTURE DE BELLE (02)

Page Predictions
================

Page 0
------
:Document Type: OLD
:Document Sides: RECTO & VERSO
:Nationality:
:Card Access Number: 175775H55790
:Document Number:
:Given Name(s): Victor
                Marie
:Surname: DAMBARD
:Alternate Name:
:Date of Birth: 1994-04-24
:Place of Birth: LYON 4E ARRONDISSEM
:Gender: M
:Expiry Date: 2030-04-02
:Mrz Line 1: IDFRADAMBARD<<<<<<<<<<<<<<<<<<075025
:Mrz Line 2: 170775H557903VICTOR<<MARIE<9404246M5
:Mrz Line 3:
:Date of Issue: 2015-04-03
:Issuing Authority: SOUS-PREFECTURE DE BELLE (02)
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

## Page-Level Fields
Some fields are constrained to the page level, and so will not be retrievable to through the document.

# Attributes
The following fields are extracted for Carte Nationale d'Identité V2:

## Alternate Name
**alternateName** : The alternate name of the card holder.

```php
echo $result->document->inference->prediction->alternateName->value;
```

## Issuing Authority
**authority** : The name of the issuing authority.

```php
echo $result->document->inference->prediction->authority->value;
```

## Date of Birth
**birthDate** : The date of birth of the card holder.

```php
echo $result->document->inference->prediction->birthDate->value;
```

## Place of Birth
**birthPlace** : The place of birth of the card holder.

```php
echo $result->document->inference->prediction->birthPlace->value;
```

## Card Access Number
**cardAccessNumber** : The card access number (CAN).

```php
echo $result->document->inference->prediction->cardAccessNumber->value;
```

## Document Number
**documentNumber** : The document number.

```php
echo $result->document->inference->prediction->documentNumber->value;
```

## Document Sides
[📄](#page-level-fields "This field is only present on individual pages.")**documentSide** : The sides of the document which are visible.

```php
foreach($result->document->documentSide as $documentSideElem){
    echo $documentSideElem;
}->value;
```

## Document Type
[📄](#page-level-fields "This field is only present on individual pages.")**documentType** : The document type or format.

```php
foreach($result->document->documentType as $documentTypeElem){
    echo $documentTypeElem;
}->value;
```

## Expiry Date
**expiryDate** : The expiry date of the identification card.

```php
echo $result->document->inference->prediction->expiryDate->value;
```

## Gender
**gender** : The gender of the card holder.

```php
echo $result->document->inference->prediction->gender->value;
```

## Given Name(s)
**givenNames** : The given name(s) of the card holder.

```php
foreach ($result->document->inference->prediction->givenNames as $givenNamesElem)
{
    echo $givenNamesElem->value;
}
```

## Date of Issue
**issueDate** : The date of issue of the identification card.

```php
echo $result->document->inference->prediction->issueDate->value;
```

## Mrz Line 1
**mrz1** : The Machine Readable Zone, first line.

```php
echo $result->document->inference->prediction->mrz1->value;
```

## Mrz Line 2
**mrz2** : The Machine Readable Zone, second line.

```php
echo $result->document->inference->prediction->mrz2->value;
```

## Mrz Line 3
**mrz3** : The Machine Readable Zone, third line.

```php
echo $result->document->inference->prediction->mrz3->value;
```

## Nationality
**nationality** : The nationality of the card holder.

```php
echo $result->document->inference->prediction->nationality->value;
```

## Surname
**surname** : The surname of the card holder.

```php
echo $result->document->inference->prediction->surname->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)

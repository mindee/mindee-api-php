---
title: IND Passport - India OCR PHP
category: 622b805aaec68102ea7fcbc2
slug: php-ind-passport---india-ocr
parentDoc: 658193df8e029d002ad9c89b
---
The PHP OCR SDK supports the [Passport - India API](https://platform.mindee.com/mindee/ind_passport).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/ind_passport/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Passport - India sample](https://github.com/mindee/client-lib-test-data/blob/main/products/ind_passport/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\Ind\IndianPassport\IndianPassportV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file asynchronously
$apiResponse = $mindeeClient->enqueueAndParse(IndianPassportV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: cf88fd43-eaa1-497a-ba29-a9569a4edaa7
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/ind_passport v1.0
:Rotation applied: Yes

Prediction
==========
:Page Number: 1
:Country: IND
:ID Number: J8369854
:Given Names: JOCELYN MICHELLE
:Surname: DOE
:Birth Date: 1959-09-23
:Birth Place: GUNDUGOLANU
:Issuance Place: HYDERABAD
:Gender: F
:Issuance Date: 2011-10-11
:Expiry Date: 2021-10-10
:MRZ Line 1: P<DOE<<JOCELYNMICHELLE<<<<<<<<<<<<<<<<<<<<<
:MRZ Line 2: J8369854<4IND5909234F2110101<<<<<<<<<<<<<<<8
:Legal Guardian:
:Name of Spouse:
:Name of Mother:
:Old Passport Date of Issue:
:Old Passport Number:
:Address Line 1:
:Address Line 2:
:Address Line 3:
:Old Passport Place of Issue:
:File Number:
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


### ClassificationField
The classification field `ClassificationField` does not implement all the basic `BaseField` attributes. It only implements **value**, **confidence** and **pageId**.

> Note: a classification field's `value is always a `string`.

### DateField
Aside from the basic `BaseField` attributes, the date field `DateField` also implements the following: 

* **dateObject** (`date`): an accessible representation of the value as a php object. Can be `null`.

### StringField
The text field `StringField` implements the following:
* **value** (`string`): represents the value of the field as a string.
* **rawValue** (`string`): the value of the string as it appears on the document.

# Attributes
The following fields are extracted for Passport - India V1:

## Address Line 1
**address1** : The first line of the address of the passport holder.

```php
echo $result->document->inference->prediction->address1->value;
```

## Address Line 2
**address2** : The second line of the address of the passport holder.

```php
echo $result->document->inference->prediction->address2->value;
```

## Address Line 3
**address3** : The third line of the address of the passport holder.

```php
echo $result->document->inference->prediction->address3->value;
```

## Birth Date
**birthDate** : The birth date of the passport holder, ISO format: YYYY-MM-DD.

```php
echo $result->document->inference->prediction->birthDate->value;
```

## Birth Place
**birthPlace** : The birth place of the passport holder.

```php
echo $result->document->inference->prediction->birthPlace->value;
```

## Country
**country** : ISO 3166-1 alpha-3 country code (3 letters format).

```php
echo $result->document->inference->prediction->country->value;
```

## Expiry Date
**expiryDate** : The date when the passport will expire, ISO format: YYYY-MM-DD.

```php
echo $result->document->inference->prediction->expiryDate->value;
```

## File Number
**fileNumber** : The file number of the passport document.

```php
echo $result->document->inference->prediction->fileNumber->value;
```

## Gender
**gender** : The gender of the passport holder.

#### Possible values include:
 - 'M'
 - 'F'

```php
echo $result->document->inference->prediction->gender->value;
```

## Given Names
**givenNames** : The given names of the passport holder.

```php
echo $result->document->inference->prediction->givenNames->value;
```

## ID Number
**idNumber** : The identification number of the passport document.

```php
echo $result->document->inference->prediction->idNumber->value;
```

## Issuance Date
**issuanceDate** : The date when the passport was issued, ISO format: YYYY-MM-DD.

```php
echo $result->document->inference->prediction->issuanceDate->value;
```

## Issuance Place
**issuancePlace** : The place where the passport was issued.

```php
echo $result->document->inference->prediction->issuancePlace->value;
```

## Legal Guardian
**legalGuardian** : The name of the legal guardian of the passport holder (if applicable).

```php
echo $result->document->inference->prediction->legalGuardian->value;
```

## MRZ Line 1
**mrz1** : The first line of the machine-readable zone (MRZ) of the passport document.

```php
echo $result->document->inference->prediction->mrz1->value;
```

## MRZ Line 2
**mrz2** : The second line of the machine-readable zone (MRZ) of the passport document.

```php
echo $result->document->inference->prediction->mrz2->value;
```

## Name of Mother
**nameOfMother** : The name of the mother of the passport holder.

```php
echo $result->document->inference->prediction->nameOfMother->value;
```

## Name of Spouse
**nameOfSpouse** : The name of the spouse of the passport holder (if applicable).

```php
echo $result->document->inference->prediction->nameOfSpouse->value;
```

## Old Passport Date of Issue
**oldPassportDateOfIssue** : The date of issue of the old passport (if applicable), ISO format: YYYY-MM-DD.

```php
echo $result->document->inference->prediction->oldPassportDateOfIssue->value;
```

## Old Passport Number
**oldPassportNumber** : The number of the old passport (if applicable).

```php
echo $result->document->inference->prediction->oldPassportNumber->value;
```

## Old Passport Place of Issue
**oldPassportPlaceOfIssue** : The place of issue of the old passport (if applicable).

```php
echo $result->document->inference->prediction->oldPassportPlaceOfIssue->value;
```

## Page Number
**pageNumber** : The page number of the passport document.

#### Possible values include:
 - '1'
 - '2'

```php
echo $result->document->inference->prediction->pageNumber->value;
```

## Surname
**surname** : The surname of the passport holder.

```php
echo $result->document->inference->prediction->surname->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)

---
title: Bill of Lading OCR PHP
category: 622b805aaec68102ea7fcbc2
slug: php-bill-of-lading-ocr
parentDoc: 658193df8e029d002ad9c89b
---
The PHP OCR SDK supports the [Bill of Lading API](https://platform.mindee.com/mindee/bill_of_lading).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/bill_of_lading/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Bill of Lading sample](https://github.com/mindee/client-lib-test-data/blob/main/products/bill_of_lading/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\BillOfLading\BillOfLadingV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file asynchronously
$apiResponse = $mindeeClient->enqueueAndParse(BillOfLadingV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: 3b5250a1-b52c-4e0b-bc3e-2f0146b04e29
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/bill_of_lading v1.1
:Rotation applied: No

Prediction
==========
:Bill of Lading Number: XYZ123456
:Shipper:
  :Address: 123 OCEAN DRIVE, SHANGHAI, CHINA
  :Email:
  :Name: GLOBAL FREIGHT SOLUTIONS INC.
  :Phone: 86-21-12345678
:Consignee:
  :Address: 789 TRADE STREET, SINGAPORE 567890, SINGAPORE
  :Email:
  :Name: PACIFIC TRADING CO.
  :Phone: 65-65432100
:Notify Party:
  :Address: 789 TRADE STREET, SINGAPORE 567890, SINGAPORE
  :Email:
  :Name: PACIFIC TRADING CO.
  :Phone: 65-65432100
:Carrier:
  :Name: GLOBAL SHIPPING CO.,LTD.
  :Professional Number:
  :SCAC:
:Items:
  +--------------------------------------+--------------+-------------+------------------+----------+-------------+
  | Description                          | Gross Weight | Measurement | Measurement Unit | Quantity | Weight Unit |
  +======================================+==============+=============+==================+==========+=============+
  | ELECTRONIC COMPONENTS\nP/N: 12345... | 500.00       | 1.50        | cbm              | 1.00     | kgs         |
  +--------------------------------------+--------------+-------------+------------------+----------+-------------+
:Port of Loading: SHANGHAI, CHINA
:Port of Discharge: LOS ANGELES, USA
:Place of Delivery: LOS ANGELES, USA
:Date of issue: 2022-09-30
:Departure Date:
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

## Specific Fields
Fields which are specific to this product; they are not used in any other product.

### Carrier Field
The shipping company responsible for transporting the goods.

A `BillOfLadingV1Carrier` implements the following attributes:

* **name** (`string`): The name of the carrier.
* **professionalNumber** (`string`): The professional number of the carrier.
* **scac** (`string`): The Standard Carrier Alpha Code (SCAC) of the carrier.
Fields which are specific to this product; they are not used in any other product.

### Consignee Field
The party to whom the goods are being shipped.

A `BillOfLadingV1Consignee` implements the following attributes:

* **address** (`string`): The address of the consignee.
* **email** (`string`): The  email of the shipper.
* **name** (`string`): The name of the consignee.
* **phone** (`string`): The phone number of the consignee.
Fields which are specific to this product; they are not used in any other product.

### Items Field
The goods being shipped.

A `BillOfLadingV1CarrierItem` implements the following attributes:

* **description** (`string`): A description of the item.
* **grossWeight** (`float`): The gross weight of the item.
* **measurement** (`float`): The measurement of the item.
* **measurementUnit** (`string`): The unit of measurement for the measurement.
* **quantity** (`float`): The quantity of the item being shipped.
* **weightUnit** (`string`): The unit of measurement for weights.
Fields which are specific to this product; they are not used in any other product.

### Notify Party Field
The party to be notified of the arrival of the goods.

A `BillOfLadingV1NotifyParty` implements the following attributes:

* **address** (`string`): The address of the notify party.
* **email** (`string`): The  email of the shipper.
* **name** (`string`): The name of the notify party.
* **phone** (`string`): The phone number of the notify party.
Fields which are specific to this product; they are not used in any other product.

### Shipper Field
The party responsible for shipping the goods.

A `BillOfLadingV1Shipper` implements the following attributes:

* **address** (`string`): The address of the shipper.
* **email** (`string`): The  email of the shipper.
* **name** (`string`): The name of the shipper.
* **phone** (`string`): The phone number of the shipper.

# Attributes
The following fields are extracted for Bill of Lading V1:

## Bill of Lading Number
**billOfLadingNumber** : A unique identifier assigned to a Bill of Lading document.

```php
echo $result->document->inference->prediction->billOfLadingNumber->value;
```

## Carrier
**carrier** ([BillOfLadingV1Carrier](#carrier-field)): The shipping company responsible for transporting the goods.

```php
echo $result->document->inference->prediction->carrier->value;
```

## Items
**carrierItems** ([[BillOfLadingV1CarrierItem](#items-field)]): The goods being shipped.

```php
foreach ($result->document->inference->prediction->carrierItems as $carrierItemsElem)
{
    echo $carrierItemsElem->value;
}
```

## Consignee
**consignee** ([BillOfLadingV1Consignee](#consignee-field)): The party to whom the goods are being shipped.

```php
echo $result->document->inference->prediction->consignee->value;
```

## Date of issue
**dateOfIssue** : The date when the bill of lading is issued.

```php
echo $result->document->inference->prediction->dateOfIssue->value;
```

## Departure Date
**departureDate** : The date when the vessel departs from the port of loading.

```php
echo $result->document->inference->prediction->departureDate->value;
```

## Notify Party
**notifyParty** ([BillOfLadingV1NotifyParty](#notify-party-field)): The party to be notified of the arrival of the goods.

```php
echo $result->document->inference->prediction->notifyParty->value;
```

## Place of Delivery
**placeOfDelivery** : The place where the goods are to be delivered.

```php
echo $result->document->inference->prediction->placeOfDelivery->value;
```

## Port of Discharge
**portOfDischarge** : The port where the goods are unloaded from the vessel.

```php
echo $result->document->inference->prediction->portOfDischarge->value;
```

## Port of Loading
**portOfLoading** : The port where the goods are loaded onto the vessel.

```php
echo $result->document->inference->prediction->portOfLoading->value;
```

## Shipper
**shipper** ([BillOfLadingV1Shipper](#shipper-field)): The party responsible for shipping the goods.

```php
echo $result->document->inference->prediction->shipper->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)

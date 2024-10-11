---
title: FR Energy Bill OCR PHP
category: 622b805aaec68102ea7fcbc2
slug: php-fr-energy-bill-ocr
parentDoc: 658193df8e029d002ad9c89b
---
The PHP OCR SDK supports the [Energy Bill API](https://platform.mindee.com/mindee/energy_bill_fra).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/energy_bill_fra/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Energy Bill sample](https://github.com/mindee/client-lib-test-data/blob/main/products/energy_bill_fra/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\Fr\EnergyBill\EnergyBillV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file asynchronously
$apiResponse = $mindeeClient->enqueueAndParse(EnergyBillV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: 17f0ccef-e3fe-4a28-838d-d704489d6ce7
:Filename: default_sample.pdf

Inference
#########
:Product: mindee/energy_bill_fra v1.0
:Rotation applied: No

Prediction
==========
:Invoice Number: 10123590373
:Contract ID: 1234567890
:Delivery Point: 98765432109876
:Invoice Date: 2021-01-29
:Due Date: 2021-02-15
:Total Before Taxes: 1241.03
:Total Taxes: 238.82
:Total Amount: 1479.85
:Energy Supplier:
  :Address: TSA 12345, 12345 DEMOCITY CEDEX, 75001 PARIS
  :Name: EDF
:Energy Consumer:
  :Address: 12 AVENUE DES RÊVES, RDC A 123 COUR FAUSSE A, 75000 PARIS
  :Name: John Doe
:Subscription:
  +--------------------------------------+------------+------------+----------+-----------+------------+
  | Description                          | End Date   | Start Date | Tax Rate | Total     | Unit Price |
  +======================================+============+============+==========+===========+============+
  | Abonnement électricité               | 2021-02-28 | 2021-01-01 | 5.50     | 59.00     | 29.50      |
  +--------------------------------------+------------+------------+----------+-----------+------------+
:Energy Usage:
  +--------------------------------------+------------+------------+----------+-----------+------------+
  | Description                          | End Date   | Start Date | Tax Rate | Total     | Unit Price |
  +======================================+============+============+==========+===========+============+
  | Consommation (HT)                    | 2021-01-27 | 2020-11-28 | 20.00    | 898.43    | 10.47      |
  +--------------------------------------+------------+------------+----------+-----------+------------+
:Taxes and Contributions:
  +--------------------------------------+------------+------------+----------+-----------+------------+
  | Description                          | End Date   | Start Date | Tax Rate | Total     | Unit Price |
  +======================================+============+============+==========+===========+============+
  | Contribution au Service Public de... | 2021-01-27 | 2020-11-28 | 20.00    | 193.07    | 2.25       |
  +--------------------------------------+------------+------------+----------+-----------+------------+
  | Départementale sur la Conso Final... | 2020-12-31 | 2020-11-28 | 20.00    | 13.98     | 0.3315     |
  +--------------------------------------+------------+------------+----------+-----------+------------+
  | Communale sur la Conso Finale Ele... | 2021-01-27 | 2021-01-01 | 20.00    | 28.56     | 0.6545     |
  +--------------------------------------+------------+------------+----------+-----------+------------+
  | Contribution Tarifaire d'Achemine... | 2020-12-31 | 2020-11-28 | 20.00    | 27.96     | 0.663      |
  +--------------------------------------+------------+------------+----------+-----------+------------+
:Meter Details:
  :Meter Number: 620
  :Meter Type: electricity
  :Unit of Measure: kWh
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


### AmountField
The amount field `AmountField` only has one constraint: its **value** is an optional `?float`.

### DateField
Aside from the basic `BaseField` attributes, the date field `DateField` also implements the following: 

* **dateObject** (`date`): an accessible representation of the value as a php object. Can be `null`.

### StringField
The text field `StringField` implements the following:
* **value** (`string`): represents the value of the field as a string.
* **rawValue** (`string`): the value of the string as it appears on the document.

## Specific Fields
Fields which are specific to this product; they are not used in any other product.

### Energy Consumer Field
The entity that consumes the energy.

A `EnergyBillV1EnergyConsumer` implements the following attributes:

* **address** (`string`): The address of the energy consumer.
* **name** (`string`): The name of the energy consumer.
Fields which are specific to this product; they are not used in any other product.

### Energy Supplier Field
The company that supplies the energy.

A `EnergyBillV1EnergySupplier` implements the following attributes:

* **address** (`string`): The address of the energy supplier.
* **name** (`string`): The name of the energy supplier.
Fields which are specific to this product; they are not used in any other product.

### Energy Usage Field
Details of energy consumption.

A `EnergyBillV1EnergyUsage` implements the following attributes:

* **description** (`string`): Description or details of the energy usage.
* **endDate** (`string`): The end date of the energy usage.
* **startDate** (`string`): The start date of the energy usage.
* **taxRate** (`float`): The rate of tax applied to the total cost.
* **total** (`float`): The total cost of energy consumed.
* **unitPrice** (`float`): The price per unit of energy consumed.
Fields which are specific to this product; they are not used in any other product.

### Meter Details Field
Information about the energy meter.

A `EnergyBillV1MeterDetail` implements the following attributes:

* **meterNumber** (`string`): The unique identifier of the energy meter.
* **meterType** (`string`): The type of energy meter.

#### Possible values include:
 - electricity
 - gas
 - water
 - None

* **unit** (`string`): The unit of measurement for energy consumption, which can be kW, m³, or L.
Fields which are specific to this product; they are not used in any other product.

### Subscription Field
The subscription details fee for the energy service.

A `EnergyBillV1Subscription` implements the following attributes:

* **description** (`string`): Description or details of the subscription.
* **endDate** (`string`): The end date of the subscription.
* **startDate** (`string`): The start date of the subscription.
* **taxRate** (`float`): The rate of tax applied to the total cost.
* **total** (`float`): The total cost of subscription.
* **unitPrice** (`float`): The price per unit of subscription.
Fields which are specific to this product; they are not used in any other product.

### Taxes and Contributions Field
Details of Taxes and Contributions.

A `EnergyBillV1TaxesAndContribution` implements the following attributes:

* **description** (`string`): Description or details of the Taxes and Contributions.
* **endDate** (`string`): The end date of the Taxes and Contributions.
* **startDate** (`string`): The start date of the Taxes and Contributions.
* **taxRate** (`float`): The rate of tax applied to the total cost.
* **total** (`float`): The total cost of Taxes and Contributions.
* **unitPrice** (`float`): The price per unit of Taxes and Contributions.

# Attributes
The following fields are extracted for Energy Bill V1:

## Contract ID
**contractId** : The unique identifier associated with a specific contract.

```php
echo $result->document->inference->prediction->contractId->value;
```

## Delivery Point
**deliveryPoint** : The unique identifier assigned to each electricity or gas consumption point. It specifies the exact location where the energy is delivered.

```php
echo $result->document->inference->prediction->deliveryPoint->value;
```

## Due Date
**dueDate** : The date by which the payment for the energy invoice is due.

```php
echo $result->document->inference->prediction->dueDate->value;
```

## Energy Consumer
**energyConsumer** ([EnergyBillV1EnergyConsumer](#energy-consumer-field)): The entity that consumes the energy.

```php
echo $result->document->inference->prediction->energyConsumer->value;
```

## Energy Supplier
**energySupplier** ([EnergyBillV1EnergySupplier](#energy-supplier-field)): The company that supplies the energy.

```php
echo $result->document->inference->prediction->energySupplier->value;
```

## Energy Usage
**energyUsage** ([[EnergyBillV1EnergyUsage](#energy-usage-field)]): Details of energy consumption.

```php
foreach ($result->document->inference->prediction->energyUsage as $energyUsageElem)
{
    echo $energyUsageElem->value;
}
```

## Invoice Date
**invoiceDate** : The date when the energy invoice was issued.

```php
echo $result->document->inference->prediction->invoiceDate->value;
```

## Invoice Number
**invoiceNumber** : The unique identifier of the energy invoice.

```php
echo $result->document->inference->prediction->invoiceNumber->value;
```

## Meter Details
**meterDetails** ([EnergyBillV1MeterDetail](#meter-details-field)): Information about the energy meter.

```php
echo $result->document->inference->prediction->meterDetails->value;
```

## Subscription
**subscription** ([[EnergyBillV1Subscription](#subscription-field)]): The subscription details fee for the energy service.

```php
foreach ($result->document->inference->prediction->subscription as $subscriptionElem)
{
    echo $subscriptionElem->value;
}
```

## Taxes and Contributions
**taxesAndContributions** ([[EnergyBillV1TaxesAndContribution](#taxes-and-contributions-field)]): Details of Taxes and Contributions.

```php
foreach ($result->document->inference->prediction->taxesAndContributions as $taxesAndContributionsElem)
{
    echo $taxesAndContributionsElem->value;
}
```

## Total Amount
**totalAmount** : The total amount to be paid for the energy invoice.

```php
echo $result->document->inference->prediction->totalAmount->value;
```

## Total Before Taxes
**totalBeforeTaxes** : The total amount to be paid for the energy invoice before taxes.

```php
echo $result->document->inference->prediction->totalBeforeTaxes->value;
```

## Total Taxes
**totalTaxes** : Total of taxes applied to the invoice.

```php
echo $result->document->inference->prediction->totalTaxes->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)

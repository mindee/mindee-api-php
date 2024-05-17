---
title: Receipt OCR PHP
---
The PHP OCR SDK supports the [Receipt API](https://platform.mindee.com/mindee/expense_receipts).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/expense_receipts/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Receipt sample](https://github.com/mindee/client-lib-test-data/blob/main/products/expense_receipts/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\Receipt\ReceiptV5;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file
$apiResponse = $mindeeClient->parse(ReceiptV5::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: ce41e37a-65d8-4de1-b34b-1c92ab04b1ae
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/expense_receipts v5.0
:Rotation applied: Yes

Prediction
==========
:Expense Locale: en-GB; en; GB; GBP;
:Purchase Category: food
:Purchase Subcategory: restaurant
:Document Type: EXPENSE RECEIPT
:Purchase Date: 2016-02-26
:Purchase Time: 15:20
:Total Amount: 10.20
:Total Net: 8.50
:Total Tax: 1.70
:Tip and Gratuity:
:Taxes:
  +---------------+--------+----------+---------------+
  | Base          | Code   | Rate (%) | Amount        |
  +===============+========+==========+===============+
  | 8.50          | VAT    | 20.00    | 1.70          |
  +---------------+--------+----------+---------------+
:Supplier Name: CLACHAN
:Supplier Company Registrations: 232153895
                                 232153895
:Supplier Address: 34 kingley street w1b 5qh
:Supplier Phone Number: 02074940834
:Line Items:
  +--------------------------------------+----------+--------------+------------+
  | Description                          | Quantity | Total Amount | Unit Price |
  +======================================+==========+==============+============+
  | Meantime Pale                        | 2.00     | 10.20        |            |
  +--------------------------------------+----------+--------------+------------+

Page Predictions
================

Page 0
------
:Expense Locale: en-GB; en; GB; GBP;
:Purchase Category: food
:Purchase Subcategory: restaurant
:Document Type: EXPENSE RECEIPT
:Purchase Date: 2016-02-26
:Purchase Time: 15:20
:Total Amount: 10.20
:Total Net: 8.50
:Total Tax: 1.70
:Tip and Gratuity:
:Taxes:
  +---------------+--------+----------+---------------+
  | Base          | Code   | Rate (%) | Amount        |
  +===============+========+==========+===============+
  | 8.50          | VAT    | 20.00    | 1.70          |
  +---------------+--------+----------+---------------+
:Supplier Name: CLACHAN
:Supplier Company Registrations: 232153895
                                 232153895
:Supplier Address: 34 kingley street w1b 5qh
:Supplier Phone Number: 02074940834
:Line Items:
  +--------------------------------------+----------+--------------+------------+
  | Description                          | Quantity | Total Amount | Unit Price |
  +======================================+==========+==============+============+
  | Meantime Pale                        | 2.00     | 10.20        |            |
  +--------------------------------------+----------+--------------+------------+
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


### AmountField
The amount field `AmountField` only has one constraint: its **value** is an optional `?float`.


### ClassificationField
The classification field `ClassificationField` does not implement all the basic `BaseField` attributes. It only implements **value**, **confidence** and **pageId**.

> Note: a classification field's `value is always a `string`.


### CompanyRegistrationField
Aside from the basic `BaseField` attributes, the company registration field `CompanyRegistrationField` also implements the following:

* **type** (`string`): the type of company.

### DateField
Aside from the basic `BaseField` attributes, the date field `DateField` also implements the following: 

* **dateObject** (`date`): an accessible representation of the value as a php object. Can be `null`.

### LocaleField
The locale field `LocaleField` only implements the **value**, **confidence** and **pageId** base `BaseField` attributes, but it comes with its own:

* **language** (`string`): ISO 639-1 language code (e.g.: `en` for English). Can be `null`.
* **country** (`string`): ISO 3166-1 alpha-2 or ISO 3166-1 alpha-3 code for countries (e.g.: `GRB` or `GB` for "Great Britain"). Can be `null`.
* **currency** (`string`): ISO 4217 code for currencies (e.g.: `USD` for "US Dollars"). Can be `null`.

### StringField
The text field `StringField` only has one constraint: its **value** is an optional `?string`.

### TaxesField
#### Tax
Aside from the basic `BaseField` attributes, the tax field `TaxField` also implements the following:

* **rate** (`float`): the tax rate applied to an item expressed as a percentage. Can be `null`.
* **code** (`string`): tax code (or equivalent, depending on the origin of the document). Can be `null`.
* **base** (`float`): base amount used for the tax. Can be `null`.

> Note: currently `TaxField` is not used on its own, and is accessed through a parent `Taxes` object, a list-like structure.

#### Taxes (Array)
The `Taxes` field represents a list-like collection of `TaxField` objects. As it is the representation of several objects, it has access to a custom `__toString` method that can render a `TaxField` object as a table line.

## Specific Fields
Fields which are specific to this product; they are not used in any other product.

### Line Items Field
List of line item details.

A `ReceiptV5LineItem` implements the following attributes:

* **description** (`string`): The item description.
* **quantity** (`float`): The item quantity.
* **totalAmount** (`float`): The item total amount.
* **unitPrice** (`float`): The item unit price.

# Attributes
The following fields are extracted for Receipt V5:

## Purchase Category
**category** : The purchase category among predefined classes.

```php
echo $result->document->inference->prediction->category->value;
```

## Purchase Date
**date** : The date the purchase was made.

```php
echo $result->document->inference->prediction->date->value;
```

## Document Type
**documentType** : One of: 'CREDIT CARD RECEIPT', 'EXPENSE RECEIPT'.

```php
echo $result->document->inference->prediction->documentType->value;
```

## Line Items
**lineItems** (List[[ReceiptV5LineItem](#line-items-field)]): List of line item details.

```php
foreach ($result->document->inference->prediction->lineItems as $lineItemsElem)
{
    echo $lineItemsElem->value;
}
```

## Expense Locale
**locale** : The locale detected on the document.

```php
echo $result->document->inference->prediction->locale->value;
```

## Receipt Number
**receiptNumber** : The receipt number or identifier.

```php
echo $result->document->inference->prediction->receiptNumber->value;
```

## Purchase Subcategory
**subcategory** : The purchase subcategory among predefined classes for transport and food.

```php
echo $result->document->inference->prediction->subcategory->value;
```

## Supplier Address
**supplierAddress** : The address of the supplier or merchant.

```php
echo $result->document->inference->prediction->supplierAddress->value;
```

## Supplier Company Registrations
**supplierCompanyRegistrations** : List of company registrations associated to the supplier.

```php
foreach ($result->document->inference->prediction->supplierCompanyRegistrations as $supplierCompanyRegistrationsElem)
{
    echo $supplierCompanyRegistrationsElem->value;
}
```

## Supplier Name
**supplierName** : The name of the supplier or merchant.

```php
echo $result->document->inference->prediction->supplierName->value;
```

## Supplier Phone Number
**supplierPhoneNumber** : The phone number of the supplier or merchant.

```php
echo $result->document->inference->prediction->supplierPhoneNumber->value;
```

## Taxes
**taxes** : List of tax lines information.

```php
foreach ($result->document->inference->prediction->taxes as $taxesElem)
{
    echo $taxesElem->value;
}
```

## Purchase Time
**time** : The time the purchase was made.

```php
echo $result->document->inference->prediction->time->value;
```

## Tip and Gratuity
**tip** : The total amount of tip and gratuity.

```php
echo $result->document->inference->prediction->tip->value;
```

## Total Amount
**totalAmount** : The total amount paid: includes taxes, discounts, fees, tips, and gratuity.

```php
echo $result->document->inference->prediction->totalAmount->value;
```

## Total Net
**totalNet** : The net amount paid: does not include taxes, fees, and discounts.

```php
echo $result->document->inference->prediction->totalNet->value;
```

## Total Tax
**totalTax** : The total amount of taxes.

```php
echo $result->document->inference->prediction->totalTax->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)

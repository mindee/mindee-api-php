---
title: Nutrition Facts Label OCR PHP
category: 622b805aaec68102ea7fcbc2
slug: php-nutrition-facts-label-ocr
parentDoc: 658193df8e029d002ad9c89b
---
The PHP OCR SDK supports the [Nutrition Facts Label API](https://platform.mindee.com/mindee/nutrition_facts).

The [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/nutrition_facts/default_sample.jpg) can be used for testing purposes.
![Nutrition Facts Label sample](https://github.com/mindee/client-lib-test-data/blob/main/products/nutrition_facts/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\NutritionFactsLabel\NutritionFactsLabelV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file asynchronously
$apiResponse = $mindeeClient->enqueueAndParse(NutritionFactsLabelV1::class, $inputSource);

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


### AmountField
The amount field `AmountField` only has one constraint: its **value** is an optional `?float`.

## Specific Fields
Fields which are specific to this product; they are not used in any other product.

### Added Sugars Field
The amount of added sugars in the product.

A `NutritionFactsLabelV1AddedSugar` implements the following attributes:

* **dailyValue** (`float`): DVs are the recommended amounts of added sugars to consume or not to exceed each day.
* **per100G** (`float`): The amount of added sugars per 100g of the product.
* **perServing** (`float`): The amount of added sugars per serving of the product.
Fields which are specific to this product; they are not used in any other product.

### Calories Field
The amount of calories in the product.

A `NutritionFactsLabelV1Calorie` implements the following attributes:

* **dailyValue** (`float`): DVs are the recommended amounts of calories to consume or not to exceed each day.
* **per100G** (`float`): The amount of calories per 100g of the product.
* **perServing** (`float`): The amount of calories per serving of the product.
Fields which are specific to this product; they are not used in any other product.

### Cholesterol Field
The amount of cholesterol in the product.

A `NutritionFactsLabelV1Cholesterol` implements the following attributes:

* **dailyValue** (`float`): DVs are the recommended amounts of cholesterol to consume or not to exceed each day.
* **per100G** (`float`): The amount of cholesterol per 100g of the product.
* **perServing** (`float`): The amount of cholesterol per serving of the product.
Fields which are specific to this product; they are not used in any other product.

### Dietary Fiber Field
The amount of dietary fiber in the product.

A `NutritionFactsLabelV1DietaryFiber` implements the following attributes:

* **dailyValue** (`float`): DVs are the recommended amounts of dietary fiber to consume or not to exceed each day.
* **per100G** (`float`): The amount of dietary fiber per 100g of the product.
* **perServing** (`float`): The amount of dietary fiber per serving of the product.
Fields which are specific to this product; they are not used in any other product.

### nutrients Field
The amount of nutrients in the product.

A `NutritionFactsLabelV1Nutrient` implements the following attributes:

* **dailyValue** (`float`): DVs are the recommended amounts of nutrients to consume or not to exceed each day.
* **name** (`string`): The name of nutrients of the product.
* **per100G** (`float`): The amount of nutrients per 100g of the product.
* **perServing** (`float`): The amount of nutrients per serving of the product.
* **unit** (`string`): The unit of measurement for the amount of nutrients.
Fields which are specific to this product; they are not used in any other product.

### Protein Field
The amount of protein in the product.

A `NutritionFactsLabelV1Protein` implements the following attributes:

* **dailyValue** (`float`): DVs are the recommended amounts of protein to consume or not to exceed each day.
* **per100G** (`float`): The amount of protein per 100g of the product.
* **perServing** (`float`): The amount of protein per serving of the product.
Fields which are specific to this product; they are not used in any other product.

### Saturated Fat Field
The amount of saturated fat in the product.

A `NutritionFactsLabelV1SaturatedFat` implements the following attributes:

* **dailyValue** (`float`): DVs are the recommended amounts of saturated fat to consume or not to exceed each day.
* **per100G** (`float`): The amount of saturated fat per 100g of the product.
* **perServing** (`float`): The amount of saturated fat per serving of the product.
Fields which are specific to this product; they are not used in any other product.

### Serving Size Field
The size of a single serving of the product.

A `NutritionFactsLabelV1ServingSize` implements the following attributes:

* **amount** (`float`): The amount of a single serving.
* **unit** (`string`): The unit for the amount of a single serving.
Fields which are specific to this product; they are not used in any other product.

### sodium Field
The amount of sodium in the product.

A `NutritionFactsLabelV1Sodium` implements the following attributes:

* **dailyValue** (`float`): DVs are the recommended amounts of sodium to consume or not to exceed each day.
* **per100G** (`float`): The amount of sodium per 100g of the product.
* **perServing** (`float`): The amount of sodium per serving of the product.
* **unit** (`string`): The unit of measurement for the amount of sodium.
Fields which are specific to this product; they are not used in any other product.

### Total Carbohydrate Field
The total amount of carbohydrates in the product.

A `NutritionFactsLabelV1TotalCarbohydrate` implements the following attributes:

* **dailyValue** (`float`): DVs are the recommended amounts of total carbohydrates to consume or not to exceed each day.
* **per100G** (`float`): The amount of total carbohydrates per 100g of the product.
* **perServing** (`float`): The amount of total carbohydrates per serving of the product.
Fields which are specific to this product; they are not used in any other product.

### Total Fat Field
The total amount of fat in the product.

A `NutritionFactsLabelV1TotalFat` implements the following attributes:

* **dailyValue** (`float`): DVs are the recommended amounts of total fat to consume or not to exceed each day.
* **per100G** (`float`): The amount of total fat per 100g of the product.
* **perServing** (`float`): The amount of total fat per serving of the product.
Fields which are specific to this product; they are not used in any other product.

### Total Sugars Field
The total amount of sugars in the product.

A `NutritionFactsLabelV1TotalSugar` implements the following attributes:

* **dailyValue** (`float`): DVs are the recommended amounts of total sugars to consume or not to exceed each day.
* **per100G** (`float`): The amount of total sugars per 100g of the product.
* **perServing** (`float`): The amount of total sugars per serving of the product.
Fields which are specific to this product; they are not used in any other product.

### Trans Fat Field
The amount of trans fat in the product.

A `NutritionFactsLabelV1TransFat` implements the following attributes:

* **dailyValue** (`float`): DVs are the recommended amounts of trans fat to consume or not to exceed each day.
* **per100G** (`float`): The amount of trans fat per 100g of the product.
* **perServing** (`float`): The amount of trans fat per serving of the product.

# Attributes
The following fields are extracted for Nutrition Facts Label V1:

## Added Sugars
**addedSugars** ([NutritionFactsLabelV1AddedSugar](#added-sugars-field)): The amount of added sugars in the product.

```php
echo $result->document->inference->prediction->addedSugars->value;
```

## Calories
**calories** ([NutritionFactsLabelV1Calorie](#calories-field)): The amount of calories in the product.

```php
echo $result->document->inference->prediction->calories->value;
```

## Cholesterol
**cholesterol** ([NutritionFactsLabelV1Cholesterol](#cholesterol-field)): The amount of cholesterol in the product.

```php
echo $result->document->inference->prediction->cholesterol->value;
```

## Dietary Fiber
**dietaryFiber** ([NutritionFactsLabelV1DietaryFiber](#dietary-fiber-field)): The amount of dietary fiber in the product.

```php
echo $result->document->inference->prediction->dietaryFiber->value;
```

## nutrients
**nutrients** ([[NutritionFactsLabelV1Nutrient](#nutrients-field)]): The amount of nutrients in the product.

```php
foreach ($result->document->inference->prediction->nutrients as $nutrientsElem)
{
    echo $nutrientsElem->value;
}
```

## Protein
**protein** ([NutritionFactsLabelV1Protein](#protein-field)): The amount of protein in the product.

```php
echo $result->document->inference->prediction->protein->value;
```

## Saturated Fat
**saturatedFat** ([NutritionFactsLabelV1SaturatedFat](#saturated-fat-field)): The amount of saturated fat in the product.

```php
echo $result->document->inference->prediction->saturatedFat->value;
```

## Serving per Box
**servingPerBox** : The number of servings in each box of the product.

```php
echo $result->document->inference->prediction->servingPerBox->value;
```

## Serving Size
**servingSize** ([NutritionFactsLabelV1ServingSize](#serving-size-field)): The size of a single serving of the product.

```php
echo $result->document->inference->prediction->servingSize->value;
```

## sodium
**sodium** ([NutritionFactsLabelV1Sodium](#sodium-field)): The amount of sodium in the product.

```php
echo $result->document->inference->prediction->sodium->value;
```

## Total Carbohydrate
**totalCarbohydrate** ([NutritionFactsLabelV1TotalCarbohydrate](#total-carbohydrate-field)): The total amount of carbohydrates in the product.

```php
echo $result->document->inference->prediction->totalCarbohydrate->value;
```

## Total Fat
**totalFat** ([NutritionFactsLabelV1TotalFat](#total-fat-field)): The total amount of fat in the product.

```php
echo $result->document->inference->prediction->totalFat->value;
```

## Total Sugars
**totalSugars** ([NutritionFactsLabelV1TotalSugar](#total-sugars-field)): The total amount of sugars in the product.

```php
echo $result->document->inference->prediction->totalSugars->value;
```

## Trans Fat
**transFat** ([NutritionFactsLabelV1TransFat](#trans-fat-field)): The amount of trans fat in the product.

```php
echo $result->document->inference->prediction->transFat->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)

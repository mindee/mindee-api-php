---
title: FR Carte Grise OCR PHP
---
The PHP OCR SDK supports the [Carte Grise API](https://platform.mindee.com/mindee/carte_grise).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/carte_grise/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Carte Grise sample](https://github.com/mindee/client-lib-test-data/blob/main/products/carte_grise/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\Fr\CarteGrise\CarteGriseV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file
$apiResponse = $mindeeClient->parse(CarteGriseV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: 4443182b-57c1-4426-a288-01b94f226e84
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/carte_grise v1.1
:Rotation applied: Yes

Prediction
==========
:a: AB-123-CD
:b: 1998-01-05
:c1: DUPONT YVES
:c3: 27 RUE DES ROITELETS 59169 FERIN LES BAINS FRANCE
:c41: 2 DELAROCHE
:c4a: EST LE PROPRIETAIRE DU VEHICULE
:d1:
:d3: MODELE
:e: VFS1V2009AS1V2009
:f1: 1915
:f2: 1915
:f3: 1915
:g: 3030
:g1: 1307
:i: 2009-12-04
:j: N1
:j1: VP
:j2: AA
:j3: CI
:p1: 1900
:p2: 90
:p3: GO
:p6: 6
:q: 006
:s1: 5
:s2:
:u1: 77
:u2: 3000
:v7: 155
:x1: 2011-07-06
:y1: 17835
:y2:
:y3: 0
:y4: 4
:y5: 2.5
:y6: 178.35
:Formula Number: 2009AS05284
:Owner's First Name: YVES
:Owner's Surname: DUPONT
:MRZ Line 1:
:MRZ Line 2: CI<<MARQUES<<<<<<<MODELE<<<<<<<2009AS0528402

Page Predictions
================

Page 0
------
:a: AB-123-CD
:b: 1998-01-05
:c1: DUPONT YVES
:c3: 27 RUE DES ROITELETS 59169 FERIN LES BAINS FRANCE
:c41: 2 DELAROCHE
:c4a: EST LE PROPRIETAIRE DU VEHICULE
:d1:
:d3: MODELE
:e: VFS1V2009AS1V2009
:f1: 1915
:f2: 1915
:f3: 1915
:g: 3030
:g1: 1307
:i: 2009-12-04
:j: N1
:j1: VP
:j2: AA
:j3: CI
:p1: 1900
:p2: 90
:p3: GO
:p6: 6
:q: 006
:s1: 5
:s2:
:u1: 77
:u2: 3000
:v7: 155
:x1: 2011-07-06
:y1: 17835
:y2:
:y3: 0
:y4: 4
:y5: 2.5
:y6: 178.35
:Formula Number: 2009AS05284
:Owner's First Name: YVES
:Owner's Surname: DUPONT
:MRZ Line 1:
:MRZ Line 2: CI<<MARQUES<<<<<<<MODELE<<<<<<<2009AS0528402
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
The following fields are extracted for Carte Grise V1:

## a
**a** : The vehicle's license plate number.

```php
echo $result->document->inference->prediction->a->value;
```

## b
**b** : The vehicle's first release date.

```php
echo $result->document->inference->prediction->b->value;
```

## c1
**c1** : The vehicle owner's full name including maiden name.

```php
echo $result->document->inference->prediction->c1->value;
```

## c3
**c3** : The vehicle owner's address.

```php
echo $result->document->inference->prediction->c3->value;
```

## c41
**c41** : Number of owners of the license certificate.

```php
echo $result->document->inference->prediction->c41->value;
```

## c4a
**c4A** : Mentions about the ownership of the vehicle.

```php
echo $result->document->inference->prediction->c4A->value;
```

## d1
**d1** : The vehicle's brand.

```php
echo $result->document->inference->prediction->d1->value;
```

## d3
**d3** : The vehicle's commercial name.

```php
echo $result->document->inference->prediction->d3->value;
```

## e
**e** : The Vehicle Identification Number (VIN).

```php
echo $result->document->inference->prediction->e->value;
```

## f1
**f1** : The vehicle's maximum admissible weight.

```php
echo $result->document->inference->prediction->f1->value;
```

## f2
**f2** : The vehicle's maximum admissible weight within the license's state.

```php
echo $result->document->inference->prediction->f2->value;
```

## f3
**f3** : The vehicle's maximum authorized weight with coupling.

```php
echo $result->document->inference->prediction->f3->value;
```

## Formula Number
**formulaNumber** : The document's formula number.

```php
echo $result->document->inference->prediction->formulaNumber->value;
```

## g
**g** : The vehicle's weight with coupling if tractor different than category M1.

```php
echo $result->document->inference->prediction->g->value;
```

## g1
**g1** : The vehicle's national empty weight.

```php
echo $result->document->inference->prediction->g1->value;
```

## i
**i** : The car registration date of the given certificate.

```php
echo $result->document->inference->prediction->i->value;
```

## j
**j** : The vehicle's category.

```php
echo $result->document->inference->prediction->j->value;
```

## j1
**j1** : The vehicle's national type.

```php
echo $result->document->inference->prediction->j1->value;
```

## j2
**j2** : The vehicle's body type (CE).

```php
echo $result->document->inference->prediction->j2->value;
```

## j3
**j3** : The vehicle's body type (National designation).

```php
echo $result->document->inference->prediction->j3->value;
```

## MRZ Line 1
**mrz1** : Machine Readable Zone, first line.

```php
echo $result->document->inference->prediction->mrz1->value;
```

## MRZ Line 2
**mrz2** : Machine Readable Zone, second line.

```php
echo $result->document->inference->prediction->mrz2->value;
```

## Owner's First Name
**ownerFirstName** : The vehicle's owner first name.

```php
echo $result->document->inference->prediction->ownerFirstName->value;
```

## Owner's Surname
**ownerSurname** : The vehicle's owner surname.

```php
echo $result->document->inference->prediction->ownerSurname->value;
```

## p1
**p1** : The vehicle engine's displacement (cm3).

```php
echo $result->document->inference->prediction->p1->value;
```

## p2
**p2** : The vehicle's maximum net power (kW).

```php
echo $result->document->inference->prediction->p2->value;
```

## p3
**p3** : The vehicle's fuel type or energy source.

```php
echo $result->document->inference->prediction->p3->value;
```

## p6
**p6** : The vehicle's administrative power (fiscal horsepower).

```php
echo $result->document->inference->prediction->p6->value;
```

## q
**q** : The vehicle's power to weight ratio.

```php
echo $result->document->inference->prediction->q->value;
```

## s1
**s1** : The vehicle's number of seats.

```php
echo $result->document->inference->prediction->s1->value;
```

## s2
**s2** : The vehicle's number of standing rooms (person).

```php
echo $result->document->inference->prediction->s2->value;
```

## u1
**u1** : The vehicle's sound level (dB).

```php
echo $result->document->inference->prediction->u1->value;
```

## u2
**u2** : The vehicle engine's rotation speed (RPM).

```php
echo $result->document->inference->prediction->u2->value;
```

## v7
**v7** : The vehicle's CO2 emission (g/km).

```php
echo $result->document->inference->prediction->v7->value;
```

## x1
**x1** : Next technical control date.

```php
echo $result->document->inference->prediction->x1->value;
```

## y1
**y1** : Amount of the regional proportional tax of the registration (in euros).

```php
echo $result->document->inference->prediction->y1->value;
```

## y2
**y2** : Amount of the additional parafiscal tax of the registration (in euros).

```php
echo $result->document->inference->prediction->y2->value;
```

## y3
**y3** : Amount of the additional CO2 tax of the registration (in euros).

```php
echo $result->document->inference->prediction->y3->value;
```

## y4
**y4** : Amount of the fee for managing the registration (in euros).

```php
echo $result->document->inference->prediction->y4->value;
```

## y5
**y5** : Amount of the fee for delivery of the registration certificate in euros.

```php
echo $result->document->inference->prediction->y5->value;
```

## y6
**y6** : Total amount of registration fee to be paid in euros.

```php
echo $result->document->inference->prediction->y6->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)

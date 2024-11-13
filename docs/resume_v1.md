---
title: Resume OCR PHP
category: 622b805aaec68102ea7fcbc2
slug: php-resume-ocr
parentDoc: 658193df8e029d002ad9c89b
---
The PHP OCR SDK supports the [Resume API](https://platform.mindee.com/mindee/resume).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/resume/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Resume sample](https://github.com/mindee/client-lib-test-data/blob/main/products/resume/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\Resume\ResumeV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file asynchronously
$apiResponse = $mindeeClient->enqueueAndParse(ResumeV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: 9daa3085-152c-454e-9245-636f13fc9dc3
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/resume v1.1
:Rotation applied: Yes

Prediction
==========
:Document Language: ENG
:Document Type: RESUME
:Given Names: Christopher
:Surnames: Morgan
:Nationality:
:Email Address: christoper.m@gmail.com
:Phone Number: +44 (0)20 7666 8555
:Address: 177 Great Portland Street, London, W5W 6PQ
:Social Networks:
  +----------------------+----------------------------------------------------+
  | Name                 | URL                                                |
  +======================+====================================================+
  | LinkedIn             | linkedin.com/christopher.morgan                    |
  +----------------------+----------------------------------------------------+
:Profession: Senior Web Developer
:Job Applied:
:Languages:
  +----------+----------------------+
  | Language | Level                |
  +==========+======================+
  | SPA      | Fluent               |
  +----------+----------------------+
  | ZHO      | Beginner             |
  +----------+----------------------+
  | DEU      | Beginner             |
  +----------+----------------------+
:Hard Skills: HTML5
              PHP OOP
              JavaScript
              CSS
              MySQL
              SQL
:Soft Skills: Project management
              Creative design
              Strong decision maker
              Innovative
              Complex problem solver
              Service-focused
:Education:
  +-----------------+---------------------------+-----------+----------+---------------------------+-------------+------------+
  | Domain          | Degree                    | End Month | End Year | School                    | Start Month | Start Year |
  +=================+===========================+===========+==========+===========================+=============+============+
  | Computer Inf... | Bachelor                  |           | 2014     | Columbia University, NY   |             |            |
  +-----------------+---------------------------+-----------+----------+---------------------------+-------------+------------+
:Professional Experiences:
  +-----------------+------------+--------------------------------------+---------------------------+-----------+----------+----------------------+-------------+------------+
  | Contract Type   | Department | Description                          | Employer                  | End Month | End Year | Role                 | Start Month | Start Year |
  +=================+============+======================================+===========================+===========+==========+======================+=============+============+
  |                 |            | Cooperate with designers to creat... | Luna Web Design, New York | 05        | 2019     | Web Developer        | 09          | 2015       |
  +-----------------+------------+--------------------------------------+---------------------------+-----------+----------+----------------------+-------------+------------+
:Certificates:
  +------------+--------------------------------+---------------------------+------+
  | Grade      | Name                           | Provider                  | Year |
  +============+================================+===========================+======+
  |            | PHP Framework (certificate)... |                           |      |
  +------------+--------------------------------+---------------------------+------+
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

### StringField
The text field `StringField` implements the following:
* **value** (`string`): represents the value of the field as a string.
* **rawValue** (`string`): the value of the string as it appears on the document.

## Specific Fields
Fields which are specific to this product; they are not used in any other product.

### Certificates Field
The list of certificates obtained by the candidate.

A `ResumeV1Certificate` implements the following attributes:

* **grade** (`string`): The grade obtained for the certificate.
* **name** (`string`): The name of certification.
* **provider** (`string`): The organization or institution that issued the certificate.
* **year** (`string`): The year when a certificate was issued or received.
Fields which are specific to this product; they are not used in any other product.

### Education Field
The list of the candidate's educational background.

A `ResumeV1Education` implements the following attributes:

* **degreeDomain** (`string`): The area of study or specialization.
* **degreeType** (`string`): The type of degree obtained, such as Bachelor's, Master's, or Doctorate.
* **endMonth** (`string`): The month when the education program or course was completed.
* **endYear** (`string`): The year when the education program or course was completed.
* **school** (`string`): The name of the school.
* **startMonth** (`string`): The month when the education program or course began.
* **startYear** (`string`): The year when the education program or course began.
Fields which are specific to this product; they are not used in any other product.

### Languages Field
The list of languages that the candidate is proficient in.

A `ResumeV1Language` implements the following attributes:

* **language** (`string`): The language's ISO 639 code.
* **level** (`string`): The candidate's level for the language.

#### Possible values include:
 - Native
 - Fluent
 - Proficient
 - Intermediate
 - Beginner

Fields which are specific to this product; they are not used in any other product.

### Professional Experiences Field
The list of the candidate's professional experiences.

A `ResumeV1ProfessionalExperience` implements the following attributes:

* **contractType** (`string`): The type of contract for the professional experience.

#### Possible values include:
 - Full-Time
 - Part-Time
 - Internship
 - Freelance

* **department** (`string`): The specific department or division within the company.
* **description** (`string`): The description of the professional experience as written in the document.
* **employer** (`string`): The name of the company or organization.
* **endMonth** (`string`): The month when the professional experience ended.
* **endYear** (`string`): The year when the professional experience ended.
* **role** (`string`): The position or job title held by the candidate.
* **startMonth** (`string`): The month when the professional experience began.
* **startYear** (`string`): The year when the professional experience began.
Fields which are specific to this product; they are not used in any other product.

### Social Networks Field
The list of social network profiles of the candidate.

A `ResumeV1SocialNetworksUrl` implements the following attributes:

* **name** (`string`): The name of the social network.
* **url** (`string`): The URL of the social network.

# Attributes
The following fields are extracted for Resume V1:

## Address
**address** : The location information of the candidate, including city, state, and country.

```php
echo $result->document->inference->prediction->address->value;
```

## Certificates
**certificates** ([[ResumeV1Certificate](#certificates-field)]): The list of certificates obtained by the candidate.

```php
foreach ($result->document->inference->prediction->certificates as $certificatesElem)
{
    echo $certificatesElem->value;
}
```

## Document Language
**documentLanguage** : The ISO 639 code of the language in which the document is written.

```php
echo $result->document->inference->prediction->documentLanguage->value;
```

## Document Type
**documentType** : The type of the document sent.

#### Possible values include:
 - RESUME
 - MOTIVATION_LETTER
 - RECOMMENDATION_LETTER

```php
echo $result->document->inference->prediction->documentType->value;
```

## Education
**education** ([[ResumeV1Education](#education-field)]): The list of the candidate's educational background.

```php
foreach ($result->document->inference->prediction->education as $educationElem)
{
    echo $educationElem->value;
}
```

## Email Address
**emailAddress** : The email address of the candidate.

```php
echo $result->document->inference->prediction->emailAddress->value;
```

## Given Names
**givenNames** : The candidate's first or given names.

```php
foreach ($result->document->inference->prediction->givenNames as $givenNamesElem)
{
    echo $givenNamesElem->value;
}
```

## Hard Skills
**hardSkills** : The list of the candidate's technical abilities and knowledge.

```php
foreach ($result->document->inference->prediction->hardSkills as $hardSkillsElem)
{
    echo $hardSkillsElem->value;
}
```

## Job Applied
**jobApplied** : The position that the candidate is applying for.

```php
echo $result->document->inference->prediction->jobApplied->value;
```

## Languages
**languages** ([[ResumeV1Language](#languages-field)]): The list of languages that the candidate is proficient in.

```php
foreach ($result->document->inference->prediction->languages as $languagesElem)
{
    echo $languagesElem->value;
}
```

## Nationality
**nationality** : The ISO 3166 code for the country of citizenship of the candidate.

```php
echo $result->document->inference->prediction->nationality->value;
```

## Phone Number
**phoneNumber** : The phone number of the candidate.

```php
echo $result->document->inference->prediction->phoneNumber->value;
```

## Profession
**profession** : The candidate's current profession.

```php
echo $result->document->inference->prediction->profession->value;
```

## Professional Experiences
**professionalExperiences** ([[ResumeV1ProfessionalExperience](#professional-experiences-field)]): The list of the candidate's professional experiences.

```php
foreach ($result->document->inference->prediction->professionalExperiences as $professionalExperiencesElem)
{
    echo $professionalExperiencesElem->value;
}
```

## Social Networks
**socialNetworksUrls** ([[ResumeV1SocialNetworksUrl](#social-networks-field)]): The list of social network profiles of the candidate.

```php
foreach ($result->document->inference->prediction->socialNetworksUrls as $socialNetworksUrlsElem)
{
    echo $socialNetworksUrlsElem->value;
}
```

## Soft Skills
**softSkills** : The list of the candidate's interpersonal and communication abilities.

```php
foreach ($result->document->inference->prediction->softSkills as $softSkillsElem)
{
    echo $softSkillsElem->value;
}
```

## Surnames
**surnames** : The candidate's last names.

```php
foreach ($result->document->inference->prediction->surnames as $surnamesElem)
{
    echo $surnamesElem->value;
}
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)

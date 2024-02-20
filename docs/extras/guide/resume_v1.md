---
title: Resume OCR PHP
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

echo strval($apiResponse->document);
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: bc80bae0-af75-4464-95a9-2419403c75bf
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/resume v1.0
:Rotation applied: No

Prediction
==========
:Document Language: ENG
:Document Type: RESUME
:Given Names: Christopher
:Surnames: Morgan
:Nationality:
:Email Address: christoper.m@gmail.com
:Phone Number: +44 (0) 20 7666 8555
:Address: 177 Great Portland Street, London W5W 6PQ
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
  | DEU      | Intermediate         |
  +----------+----------------------+
:Hard Skills: HTML5
              PHP OOP
              JavaScript
              CSS
              MySQL
:Soft Skills: Project management
              Strong decision maker
              Innovative
              Complex problem solver
              Creative design
              Service-focused
:Education:
  +-----------------+---------------------------+-----------+----------+---------------------------+-------------+------------+
  | Domain          | Degree                    | End Month | End Year | School                    | Start Month | Start Year |
  +=================+===========================+===========+==========+===========================+=============+============+
  | Computer Inf... | Bachelor                  |           |          | Columbia University, NY   |             | 2014       |
  +-----------------+---------------------------+-----------+----------+---------------------------+-------------+------------+
:Professional Experiences:
  +-----------------+------------+---------------------------+-----------+----------+----------------------+-------------+------------+
  | Contract Type   | Department | Employer                  | End Month | End Year | Role                 | Start Month | Start Year |
  +=================+============+===========================+===========+==========+======================+=============+============+
  | Full-Time       |            | Luna Web Design, New Y... | 05        | 2019     | Web Developer        | 09          | 2015       |
  +-----------------+------------+---------------------------+-----------+----------+----------------------+-------------+------------+
:Certificates:
  +------------+--------------------------------+---------------------------+------+
  | Grade      | Name                           | Provider                  | Year |
  +============+================================+===========================+======+
  |            | PHP Framework (certificate)... |                           | 2014 |
  +------------+--------------------------------+---------------------------+------+
  |            | Programming Languages: Java... |                           |      |
  +------------+--------------------------------+---------------------------+------+

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

### StringField
The text field `StringField` only has one constraint: its **value** is an optional `?string`.

## Specific Fields
Fields which are specific to this product; they are not used in any other product.

### Certificates Field
The list of certificates obtained by the candidate.

A `ResumeV1Certificate` implements the following attributes:

* **grade** (`string`): The grade obtained for the certificate.
* **name** (`string`): The name of certifications obtained by the individual.
* **provider** (`string`): The organization or institution that issued the certificates listed in the document.
* **year** (`string`): The year when a certificate was issued or received.
Fields which are specific to this product; they are not used in any other product.

### Education Field
The list of values that represent the educational background of an individual.

A `ResumeV1Education` implements the following attributes:

* **degreeDomain** (`string`): The area of study or specialization pursued by an individual in their educational background.
* **degreeType** (`string`): The type of degree obtained by the individual, such as Bachelor's, Master's, or Doctorate.
* **endMonth** (`string`): The month when the education program or course was completed or is expected to be completed.
* **endYear** (`string`): The year when the education program or course was completed or is expected to be completed.
* **school** (`string`): The name of the school the individual went to.
* **startMonth** (`string`): The month when the education program or course began.
* **startYear** (`string`): The year when the education program or course began.
Fields which are specific to this product; they are not used in any other product.

### Languages Field
The list of languages that a person is proficient in, as stated in their resume.

A `ResumeV1Language` implements the following attributes:

* **language** (`string`): The language ISO 639 code.
* **level** (`string`): The level for the language. Possible values: 'Fluent', 'Proficient', 'Intermediate' and 'Beginner'.
Fields which are specific to this product; they are not used in any other product.

### Professional Experiences Field
The list of values that represent the professional experiences of an individual in their global resume.

A `ResumeV1ProfessionalExperience` implements the following attributes:

* **contractType** (`string`): The type of contract for a professional experience. Possible values: 'Full-Time', 'Part-Time', 'Internship' and 'Freelance'.
* **department** (`string`): The specific department or division within a company where the professional experience was gained.
* **employer** (`string`): The name of the company or organization where the candidate has worked.
* **endMonth** (`string`): The month when a professional experience ended.
* **endYear** (`string`): The year when a professional experience ended.
* **role** (`string`): The position or job title held by the individual in their previous work experience.
* **startMonth** (`string`): The month when a professional experience began.
* **startYear** (`string`): The year when a professional experience began.
Fields which are specific to this product; they are not used in any other product.

### Social Networks Field
The list of URLs for social network profiles of the person.

A `ResumeV1SocialNetworksUrl` implements the following attributes:

* **name** (`string`): The name of of the social media concerned.
* **url** (`string`): The URL of the profile for this particular social network.

# Attributes
The following fields are extracted for Resume V1:

## Address
**address** : The location information of the person, including city, state, and country.

```php
echo $result->document->inference->prediction->address->value;
```

## Certificates
**certificates** (List[[ResumeV1Certificate](#certificates-field)]): The list of certificates obtained by the candidate.

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
**documentType** : The type of the document sent, possible values being RESUME, MOTIVATION_LETTER and RECOMMENDATION_LETTER.

```php
echo $result->document->inference->prediction->documentType->value;
```

## Education
**education** (List[[ResumeV1Education](#education-field)]): The list of values that represent the educational background of an individual.

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
**givenNames** : The list of names that represent a person's first or given names.

```php
foreach ($result->document->inference->prediction->givenNames as $givenNamesElem)
{
    echo $givenNamesElem->value;
}
```

## Hard Skills
**hardSkills** : The list of specific technical abilities and knowledge mentioned in a resume.

```php
foreach ($result->document->inference->prediction->hardSkills as $hardSkillsElem)
{
    echo $hardSkillsElem->value;
}
```

## Job Applied
**jobApplied** : The specific industry or job role that the applicant is applying for.

```php
echo $result->document->inference->prediction->jobApplied->value;
```

## Languages
**languages** (List[[ResumeV1Language](#languages-field)]): The list of languages that a person is proficient in, as stated in their resume.

```php
foreach ($result->document->inference->prediction->languages as $languagesElem)
{
    echo $languagesElem->value;
}
```

## Nationality
**nationality** : The ISO 3166 code for the country of citizenship or origin of the person.

```php
echo $result->document->inference->prediction->nationality->value;
```

## Phone Number
**phoneNumber** : The phone number of the candidate.

```php
echo $result->document->inference->prediction->phoneNumber->value;
```

## Profession
**profession** : The area of expertise or specialization in which the individual has professional experience and qualifications.

```php
echo $result->document->inference->prediction->profession->value;
```

## Professional Experiences
**professionalExperiences** (List[[ResumeV1ProfessionalExperience](#professional-experiences-field)]): The list of values that represent the professional experiences of an individual in their global resume.

```php
foreach ($result->document->inference->prediction->professionalExperiences as $professionalExperiencesElem)
{
    echo $professionalExperiencesElem->value;
}
```

## Social Networks
**socialNetworksUrls** (List[[ResumeV1SocialNetworksUrl](#social-networks-field)]): The list of URLs for social network profiles of the person.

```php
foreach ($result->document->inference->prediction->socialNetworksUrls as $socialNetworksUrlsElem)
{
    echo $socialNetworksUrlsElem->value;
}
```

## Soft Skills
**softSkills** : The list of values that represent a person's interpersonal and communication abilities in a global resume.

```php
foreach ($result->document->inference->prediction->softSkills as $softSkillsElem)
{
    echo $softSkillsElem->value;
}
```

## Surnames
**surnames** : The list of last names provided in a resume document.

```php
foreach ($result->document->inference->prediction->surnames as $surnamesElem)
{
    echo $surnamesElem->value;
}
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-1jv6nawjq-FDgFcF2T5CmMmRpl9LLptw)

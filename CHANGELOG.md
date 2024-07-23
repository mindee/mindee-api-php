# Mindee PHP API Library Changelog

# v1.9.0 - 2024-07-23
### Changes
* :sparkles: add support for US healthcare card V1
* :sparkles: add support for Invoice V4.7
* :sparkles: add support for FinancialDocument V1.9
* :sparkles: add PDF fixing utility

### Fixes
* :recycle: rigidify api key check


# v1.8.1 - 2024-06-24
### Fixes
* :bug: fix improper script targeting causing warning in composer install (#70)


# v1.8.0 - 2024-05-31
### Changes
* :sparkles: add support for webhooks & HMAC validation (#64)


# v1.7.0 - 2024-05-28
### Changes
* :sparkles: add support for boolean fields
* :sparkles: add support for US Mail V2
* :sparkles: add support for local responses & webhooks
* :sparkles: add support for HMAC validation

### Fixes
* :bug: fix base64 encoded files not being sent properly
* :recycle: loosen symfony/console version to prevent incompatibilities (Co-authored-by: @psihius)


# v1.6.0 - 2024-05-22
### Changes
* :sparkles: add Command Line Utility
* :recycle: tweak tests
* :memo: add CLI documentation


# v1.5.0 - 2024-05-21
### Changes
* :sparkles: update financial document to v1.7
* :sparkles: update receipt to v5.2


# v1.4.3 - 2024-05-04
### Fixes
* :bug: fix urls improperly being handled (#52)


# v1.4.2 - 2024-05-04
### Fixes
* :bug: fix improper handling of url sources in client calls (#50)
* :recycle: add more relevant tests


## v1.4.1 - 2024-05-03
### Fixes
* :bug: replace instance of invalid syntax for older php versions (#47)


## v1.4.0 - 2024-05-02
### Changes
* :sparkles: update invoice to 4.6 and financial document to 1.6 (#44)


## v1.3.4 - 2024-03-20
### Fixes
* :bug: fix improper handling of number fields on Generated APIs (see #41)


## v1.3.3 - 2024-03-15
### Fixes
* :bug: not sending the custom endpoint to parseQueued method (#39) (Co-authored-by: andreifiroiu)


## v1.3.2 - 2024-03-12
### Fixes
* :bug: fix improper handling of base64 files
* :bug: fix improper handling of bytes files
* :bug: fix error catching for CURL calls


## v1.3.1 - 2024-03-07
### Changes
* :recycle: update error handling to account for future evolutions
* :memo: update miscellaneous product documentations

### Fixes
* :bug: fix file close not working properly


## v1.3.0 - 2024-02-23
### Changes
* :sparkles: add support for GeneratedV1
* :recycle: update internals to support changes


## v1.2.0 - 2024-02-21
### Changes
* :sparkles: add support for International ID V2
* :sparkles: add support for Resume V1
* :sparkles: add support for EU Driver License

### Fixes
* :bug: fix display issue for rst columns that were exactly the length of a cell
* :recycle: properly parse bytes, base64 & file inputs (see #23 )
* :recycle: removed base64 coercion on most input types
* :bug: fix mimetype being improperly assigned for most input types
* :bug: fix an issue that prevented some errors from being displayed properly
* :memo: update documentation


## v1.1.0 - 2024-01-30
### Changes
* :arrow_up: update invoices to v4.4
* :sparkles: add support for `raw_value` in string fields


## v1.0.1 - 2024-01-23
### Fixes
* :bug: fix Fatal error including API version string in a namespaced project (Co-authored-by: superwave1999)


## v1.0.0 - 2024-01-08
* :tada: First official release!


## v1.0.0-RC2 - 2023-12-19
### Changes
* :memo: fix typos in README.md documentation
* :memo: fix typos in getting_started.md documentation
* :wrench: add code samples directly in documentation
* :recycle: uniformize name for default sample


## v1.0.0-RC1 - 2023-12-19
* :tada: First release!

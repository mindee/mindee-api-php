# Mindee PHP API Library Changelog

## v1.18.0 - 2025-04-08
### Changes
* :sparkles: add support for Financial Document V1.12
* :sparkles: add support for Invoices V4.10
* :sparkles: add support for US Healthcare Cards V1.2


## v1.17.1 - 2025-03-27
### Fixes
* :bug: fix null objects being returned in extras


## v1.17.0 - 2025-03-27
### Changes
* :sparkles: update structure for InvoiceSplitterV1
* :sparkles: update FR EnegryBillV1 to V1.2
* :sparkles: update US HealthcareCardV1 to V1.1
* :coffin: remove support for EU Driver License
* :coffin: remove support for License Plates
* :coffin: remove support for ReceiptV4
* :coffin: remove support for Proof of Address
* :coffin: remove support for US Driver License
* :coffin: remove support for US W9V1


## v1.16.1 - 2025-02-17
### Fixes
* :bug: harmonize OS names across client libraries


## v1.16.0 - 2025-01-14
### Changes
* :sparkles: add support for US Mail V3
* :recycle: increase async retry timers


## v1.15.0 - 2024-12-13
### Changes
* :sparkles: allow local downloading of remote sources
* :coffin: remove support for (FR) Carte Vitale V1 in favor of French Health Card V1


## v1.14.0 - 2024-11-28
### Changes
* :sparkles: add support for workflows
* :sparkles: add support for French Health Card V1
* :sparkles: add support for Driver License V1
* :sparkles: add support for Payslip FR V3
* :coffin: remove support for international ID V1


## v1.13.0 - 2024-11-14
### Changes
* :sparkles: add support for business cards V1
* :sparkles: add support for delivery note V1.1
* :sparkles: add support for indian passport V1
* :sparkles: add support for resume V1.1
* :sparkles: add support for image compression
* :sparkles: add support for PDF compression
### Fixes
* :recycle: adjust default values for async delays


## v1.12.0 - 2024-10-11
### Changes
* :sparkles: add support for Financial Document v1.10
* :sparkles: add support for Invoice v4.8


## v1.11.1 - 2024-09-19
### Fixes
* :recycle: removed unused dependency
* :recycle: fix tests


## v1.11.0 - 2024-09-18
### Changes
* :sparkles: add support for BillOfLadingV1
* :sparkles: add support for (US) UsMailV2
* :sparkles: add support for (FR) EnergyBillV1
* :sparkles: add support for (FR) PayslipV1
* :sparkles: add support for NutritionFactsLabelV1
* :sparkles: add support for full text OCR extra

### Fixes
* :bug: fixed a bug that prevented longer decimals from appearing in the string representation of some objects
* :bug: fixed a bug that caused non-table elements to unexpectedly appear truncated when printed to the console
* :memo: fix a few documentation errors & typos
* :wrench: updated CI dependencies


## v1.10.0 - 2024-09-04
### Changes
* :sparkles: add support for pdf operations
* :sparkles: add support for multi-receipts auto-extraction
* :sparkles: add support for invoice-splitter auto-extraction
*
### Fixes
* :coffin: remove regression testing
* :memo: update documentation
* :wrench: add a few integration tests


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

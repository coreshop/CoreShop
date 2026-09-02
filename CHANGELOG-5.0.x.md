## 5.0.2
* All changes merged from 4.1.12 and 4.1.13
* [PHPStan] fix listing return types for Pimcore 12.3.9 by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3092
* [Security] fix pull_request_target workflow injection (pwn request) by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3115
* [Rule] Fix nested conditions/actions lost with JSON configuration column by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3128
* [Deps] Cap doctrine/orm < 3.6.8 until doctrine/dbal ^4.5 is released by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3129
* [Core] Migrate legacy PHP-serialized json columns to JSON (CoreShop 4 -> 5) by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3131
* [IndexBundle] support Elasticsearch alongside OpenSearch by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3156
* [CoreBundle] migrate the doctrine json columns from LONGTEXT to the platform json type by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3168
* [Composer] sync root replace with the actual split packages by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3176
* Messenger: fix empty "Failed At" column by emitting the key the UI reads by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3179

## 5.0.1
* All changes merged from 4.1.*
* Dependency Updates in FrontendBundle Design v2
 
## 5.0.0

> CoreShop is now Licensed under CCL only! If you update to Version 5 make sure to read
> the [License](https://github.com/coreshop/CoreShop/blob/5.0/LICENSE.md) carefully and get in touch with us!

### Frontend Bundle

A new Frontend has been introduced. This could break existing Frontend implementations. Please make sure to copy the
old, not copied yet, Frontend Files to your implementation.

### Index Bundle

The IndexBundle Extensions (`IndexColumnsExtensionInterface`, `IndexRelationalColumnsExtensionInterface`) get*Columns
Methods for a MySQL Worker need to return a array of `Doctrine\DBAL\Schema\Column` now

## What's Changed
* [FrontendBundle] Design v2 and Pimcore 12 compatibility by @codingioanniskrikos in https://github.com/coreshop/CoreShop/pull/2744
* [IndexBundle] Optimize 404 Exception Handling on Index Item Deletion by @aarongerig in https://github.com/coreshop/CoreShop/pull/2894
* [IndexBundle] Ignore missing 404 error when deleting non-existent document by @aarongerig in https://github.com/coreshop/CoreShop/pull/2896
* [CoreBundle] fix store values version preview with null values by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2912
* [GraphQL] enable all translations for graphql by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2914
* [Pimcore] downgrade doctrine/dbal by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2922
*  [CoreBundle] fix deprecation and issue in ObjectUserProvider and use stable Pimcore 12 in https://github.com/coreshop/CoreShop/pull/2926
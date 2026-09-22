## 5.1.3

### CoreShop field types in the Studio class definition editor

All CoreShop DataObject field types (`coreShopStore`, `coreShopMoney`, `coreShopProductUnitDefinitions`, ...)
can now be added and configured in the Studio class editor. Previously the editor showed "Type not supported"
and the types were missing from the "add field" dropdown, because CoreShop only registered them in the object
editor registry (#3260).

- Added `DynamicTypeFieldDefinitionCoreShop*` classes for all 31 field types, grouped under a new "CoreShop"
  entry in the "add field" dropdown.
- Added the base classes `DynamicTypeFieldDefinitionCoreShopAbstract` (PimcoreBundle),
  `DynamicTypeFieldDefinitionCoreShopSelect`, `...Multiselect`, `...Relation`, `...Relations` (ResourceBundle)
  and `registerCoreShopFieldDefinitionTypes()` for third-party bundles that ship their own CoreShop-style
  field types. See `docs/03_Development/14_Studio/02_Base_Infrastructure/06_Dynamic_Types.md`.

## 5.1.2

- All changes merged from 5.0.3.
- Fixed the Classic Admin "About CoreShop" menu entry: it is wired to the classic resource dispatcher again, and the
  Studio-only menu entries are hidden in the Classic Admin (#3236).
- Fixed the category and search pages failing on a zero or negative `page` / `perPage` query parameter (#3247).
- Docs: the demo links point at the CoreShop 5 demo (#3232), and the Elasticsearch client configuration key is
  corrected with links to the client bundles (#3239).

## 5.1.1

### Telemetry ping and license check

The new `coreshop/telemetry-bundle` (`CoreShopTelemetryBundle`, registered by `CoreShopCoreBundle` and usable
without it on installations that only run single CoreShop bundles) sends an anonymous ping to the CoreShop license
portal once every 24 hours through the Pimcore maintenance task `coreshop_telemetry`. It transmits a hashed instance identifier (plus Pimcore's instance identifier in clear text, so installations can
be matched with Pimcore's product registration), the configured domains, CoreShop,
Pimcore and PHP versions and the list of installed CoreShop packages and Pimcore bundles. No customer or content data
is sent. The subscription token contributed by `coreshop/enterprise-subscription-bundle` is transmitted as a SHA-256
hash only.

- Added `CoreShop\Bundle\TelemetryBundle\Contract\TelemetryDataProviderInterface` (tag `coreshop.telemetry.provider`),
  `TelemetryPingerInterface`, `TelemetryResultStorageInterface` and `InstanceIdentifierProviderInterface`.
- Added the console command `coreshop:telemetry:ping [--dump]`.
- Added the configuration node `core_shop_telemetry` (`enabled`, `endpoint`, `timeout`). Opt out with
  `CORESHOP_TELEMETRY=false`. The previously unused `send_usage_log` option is superseded by `telemetry.enabled`.
- See [Telemetry and License Check](docs/01_Getting_Started/05_Telemetry.md).

## 5.1.0

### Pimcore Studio v2

CoreShop 5.1 introduces support for Pimcore Studio. Both Studio and the ExtJS-based Classic Admin 
are now supported as **optional, independent installations** — install either, 
or both side-by-side. Existing Classic-Admin-only installations continue to work without
changes.

### Classic-Admin internals moved to `AdminClass/` subnamespace

Internal helper classes that exist solely to integrate with the ExtJS Classic Admin
(grid column config operators, admin-JS injection listeners, admin-grid filter listeners, etc.)
have been moved into a dedicated `AdminClass/` subnamespace within their bundles.

This is a preparatory step: it isolates Classic-Admin-specific code so it can be cleanly removed
in a future major version once Studio v2 covers all functionality. **The classes still exist and
Classic Admin continues to work without changes** — only the fully-qualified namespace changed.

If you imported any of these classes directly via `use` statements or referenced them by FQCN as
service IDs in your own configuration, see [UPGRADE-5.1.md](UPGRADE-5.1.md) for the rename mapping.

### What's Changed

* All changes merged from 5.0.*
* [PimcoreBundle] fix studio form integration and split studio form bundle by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3072
* [Security] apply npm security updates to 5.1 by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3074
* [PHPStan] fix listing return types for Pimcore 12.3.9 by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3078
* [CI] trigger monorepo split on 5.1 branch by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3079
* [Messenger] fix Studio widget not rendering in navigation by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3102
* [CI] Rebuild Studio frontend bundles by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3103
* [Studio] Render CoreShop document editables in the Studio document editor by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3113
* [Security] fix pull_request_target workflow injection (pwn request) by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3116
* [CI] Rebuild Studio frontend bundles by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3114
* [CI] derive Studio build ids from sources, build PRs without committing by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3119
* [Docs] fix three broken documentation links by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3122
* [CI] fail the Studio build when the asset commit cannot be pushed by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3121
* [CI] push the Studio asset commit with a GitHub App token by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3123
* [Studio] Render CoreShop trees with Pimcore's TreeElement by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3125
* Studio: render a no-configuration note for form-less rule conditions and actions by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3138
* 5.1: Make TestBundle pages compatible with page-object-extension ^0.3 and ^0.4 by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3154
* Studio: make plugin registry lookups independent of plugin init order by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3139
* Fix typo in coreshop.security.frontend_regex breaking the pimcore-studio exclusion by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3164
* [5.1][RuleBundle] Add missing coreshop_settings key to Studio translation catalogue by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3163
* Make Studio shipment/invoice creation modals fully schema-driven by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3170
* [Composer] constrain 5.1 split packages to their 5.1 siblings by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3173
* Make RuleFormSchemaCollector injection optional (split-package installs) by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3171
* Messenger: drop the redundant failedAt field from MessengerFailedMessage by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3180
* [5.1] Treat PHP deprecations the way Pimcore does and drop the react/promise conflict by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/3198

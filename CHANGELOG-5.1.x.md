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

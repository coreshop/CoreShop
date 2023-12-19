# 4.0.0

* add missing subscribed services by @hethehe in https://github.com/coreshop/CoreShop/pull/2439
* [Docs] update docs by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2455
* fix workflow.registry service argument by @hethehe in https://github.com/coreshop/CoreShop/pull/2457
* [StoreBundle] fix StoreCollector for backend by @codingioanniskrikos in https://github.com/coreshop/CoreShop/pull/2466
* [Core] fix o_id usages by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2473

# 4.0.0-beta.4

* [Pimcore11] remove o_ column usages by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2413
* [Pimcore11] fix return type for getChildCategories by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2414
* can not save store shop settings by @sevarozh in https://github.com/coreshop/CoreShop/pull/2415
* [Pimcore] require Pimcore 11.1 as minimum by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2423
* Fix error in the filter functionality for multiselects by @hethehe in https://github.com/coreshop/CoreShop/pull/2426

# 4.0.0-beta.3
- CoreShop 4.0.0 is the same as 3.2.0 will be, it contains all bug-fixes and feature from 3.1 and 3.2

## Bugs
 - [ResourceBundle] fix CoreShopRelation and CoreShopRelations dynamic classes setter by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2396

## Features
### From 3.2
- [Order] Backend Order Editing by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2397, https://github.com/coreshop/CoreShop/pull/2382

# 4.0.0-beta.2
 - CoreShop 4.0.0 is the same as 3.2.0 will be, it contains all bug-fixes and feature from 3.1 and 3.2

# 4.0.0-beta.1

> CoreShop 4.0.0 is the same as 3.1.0, but with Pimcore 11 compatibility. Updating CoreShop therefore is quite easy. Since Symfony now doesn't have a full container anymore, we use Service Containers now for our Controllers. So your overwritten Controllers probably need changes.

 - Pimcore 11 Compatibility (https://github.com/coreshop/CoreShop/pull/2252, https://github.com/coreshop/CoreShop/pull/2340, https://github.com/coreshop/CoreShop/pull/2345, https://github.com/coreshop/CoreShop/pull/2352, https://github.com/coreshop/CoreShop/pull/2321, https://github.com/coreshop/CoreShop/pull/2347)
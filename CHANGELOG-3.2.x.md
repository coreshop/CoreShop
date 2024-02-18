# 3.2.5
* [Migration] [Migration] fix Staticroute Migration for Pimcore 10 by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2550

# 3.2.4
* [CoreBundle] fix priority of coreshop_payment_token route by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2542
* [Frontend] create order-token if not yet exists by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2543
* [ProductBundle] default return empty array instead of null in preGetData by @breakone in https://github.com/coreshop/CoreShop/pull/2544

# 3.2.3
* [ProductBundle] fix ClearCachedPriceRulesListener - remove service definition

# 3.2.2 
* [CartManager] create unique key for cart items by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2510
* [Core] Use order tokens in payment capture by @yariksheptykin in https://github.com/coreshop/CoreShop/pull/2515
* [Core] add caching for recursive variant checking by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2520
* use null coalescing operator against maxUsagePerUser by @sovlerat in https://github.com/coreshop/CoreShop/pull/2524

# 3.2.1
* [ClassDefinitionPatch] allow update of field-definitions instead of replace by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2494
* [Product] assert range sort for qpr by @solverat in https://github.com/coreshop/CoreShop/pull/2498
* [OrderEdit] allow 0 quantity by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2499
* [ClassDefinitionPatch] Class patches array by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2496

# 3.2.0

* [Order] introduce feature to allow editing confirmed orders in backend by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2382
* [Order] Backend Order Editing by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2397
* [StorageListBundle] make restore cart after checkout configurable by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2428
* [OrderEdit] don't allow cancelled orders to be editable by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2431
* [Voucher] restrict voucher usage per customer by @Philip-Neusta in https://github.com/coreshop/CoreShop/pull/2451
* [CoreBundle] introduce Product Price Rule that is not combinable with Cart Price Voucher Rule by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2458
* [CoreBundle] fix migration to add immutable field to order by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2465

# 3.2.0-beta.1

* [StorageListBundle] make restore cart after checkout configurable by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2428
* [OrderEdit] don't allow cancelled orders to be editable by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2431

# 3.2.0-beta.1

## Features

- [Order] Backend Order Editing by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2397, https://github.com/coreshop/CoreShop/pull/2382


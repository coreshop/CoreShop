# 3.1.4

* cast trackingCode as string by @solverat in https://github.com/coreshop/CoreShop/pull/2412
* fix comment delete request by @solverat in https://github.com/coreshop/CoreShop/pull/2407
* [Core] add unit to GiftProductAction by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2422
* [Cart] fix wrong decoration of cart-context by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2432

# 3.1.3

* [Cart] fix using right context for performance increase by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2387
* [Core] Optimize Category recursive and Product Variants Condition Checker by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2388
* [ResourceBundle] fix cache marshalling issues with CoreShop Doctrine … by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2389
* [Cache] cache improvements - decorate Pimcore CoreCacheHandler by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2398
* [Messenger] fix menu in standalone mode by @jdreesen in https://github.com/coreshop/CoreShop/pull/2393

# 3.1.2

## Bugs
 - [Cache] "apply CacheResourceMarshaller to doctrine_dbal adapter, add cache marshaller to MoneyCurrency (https://github.com/coreshop/CoreShop/pull/2373)
 - [Core] re-add mainObjectId and objectId (https://github.com/coreshop/CoreShop/pull/2380)
 - [Reports] fix reports to only select actual orders (https://github.com/coreshop/CoreShop/pull/2381)

# 3.1.1

## Bugs
 - [PaymentRules] fix decoration of PaymentProviderResolver (https://github.com/coreshop/CoreShop/pull/2317)
 - [Notes] fix note title translation (https://github.com/coreshop/CoreShop/pull/2335)
 - [Menu] making it easer to have more independent bundles with the same main-menu (https://github.com/coreshop/CoreShop/pull/2344)
 - [FrontendBundle] fix: don't require permissions to render partials (https://github.com/coreshop/CoreShop/pull/2329)
 - [VariantBundle] fix null values in ValidAttributesValidator (https://github.com/coreshop/CoreShop/pull/2348)
 - [OrderBundle] don't modify CartItem twice (https://github.com/coreshop/CoreShop/pull/2355)

# 3.1.0

## BC Breaks:
- core_shop_order.expiration
  ```
  core_shop_order:
   expiration:
       cart:
           days: 20
           anonymous: true
           customer: true
       order:
          days: 20

  ```
  is now configured like:
  ```
    core_shop_storage_list:
        list:
           order:
               expiration:
                   params:
                       cart:
                           days: 0
                           params:
                               anonymous: true
                               customer: false
                       order:
                           days: 20
  ```

## Bugs
 - [Performance] optimization (https://github.com/coreshop/CoreShop/pull/2265)

## Features
 - [WishlistBundle] new feature: wishlist cleaner (https://github.com/coreshop/CoreShop/pull/2234, https://github.com/coreshop/CoreShop/pull/2267)
 - [OrderBundle] introduce not-combinable price rule condition (https://github.com/coreshop/CoreShop/pull/2253)
 - [ClassDefinitionPatchBundle] Introduce new Bundle to allow patching Pimcore Class Definitions (https://github.com/coreshop/CoreShop/pull/2279)
 - [Tracking] add Google Analytics 4 Tracking (https://github.com/coreshop/CoreShop/pull/2303)
 - [Payment] Introduce Payment Provider Rules for conditionally selecting Payment Providers or adding Payment Fees (https://github.com/coreshop/CoreShop/pull/2301)
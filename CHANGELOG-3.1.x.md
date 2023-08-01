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
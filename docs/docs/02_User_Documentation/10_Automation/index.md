# Automation

CoreShop implements several automated mechanisms to streamline operations and maintain order within the system. These
automations are executed daily via maintenance jobs.

## Order Cancellation Automation

**Execution Time**: Once per day.

### Default Behavior

CoreShop automatically cancels orders that are older than 20 days.

### Customizing Order Expiration

To change the expiration period for orders, modify the configuration as follows:

```yml
core_shop_storage_list:
    list:
        order:
            expiration:
                params:
                    order:
                        days: 30
```

## Expired Carts Cleanup

**Execution Time**: Once per day.

> **Note**: By default, cart cleanup is disabled (set to 0 days).

Expired carts are not removed unless you enable and configure this feature. Set a specific timeframe after which
inactive carts should be cleaned up to manage cart data effectively.

#### Enabling Cart Cleanup

To activate and set a specific time frame for cart cleanup:

```yml
core_shop_storage_list:
    list:
        order:
            expiration:
                params:
                    cart:
                        days: 20
                        params:
                            anonymous: true
                            customer: false
```

## Expired Wishlists

**Execution Time**: Once per day.
> **Note**: Wishlist expiration is disabled by default.

Similar to carts, wishlists also do not expire by default. You can enable this feature and set an expiration period for
wishlists, helping to maintain a clean and current wishlist database.

#### Setting Wishlist Expiration

To enable and configure the expiration for wishlists:

```yml
core_shop_storage_list:
    list:
        wishlist:
            expiration:
                service: ~ # use default service
                enabled: true
                days: 14
                params:
                    anonymous: true
                    customer: false
```

## Expired Rules Management

**Execution Time**: Once per day.

CoreShop automatically disables rules that are beyond their valid time-span. This feature helps in keeping your rule set
relevant and up-to-date. For more complex rule availability logic, CoreShop provides an
event (`coreshop.rule.availability_check`) that allows for custom implementation of rule availability criteria.
# CoreShop Automation
In CoreShop there are several automation mechanism implemented.

## Order Cancellation
> Execution Time: Once per day via maintenance job

CoreShop will automatically cancel orders older than `20` days.

#### Change Orders Expiration Date
```yml
core_shop_order:
    expiration:
        order:
            days: 30
```

## Expired Carts
> Execution Time: Once per day via maintenance job

> **Note**: By default, this feature is disabled.

By default, this feature is disabled (`days = 0`) so no carts will be removed by default.
If you want to remove older carts, just enable it via configuration:

#### Change Cart Expiration Date
```yml
core_shop_order:
    expiration:
        cart:
            days: 20
            anonymous: true
            customer: true
```

## Expired Rules
> Execution Time: Once per day via maintenance job

If you're having a lot of active rules in your system, you may want to disable them via automation.
CoreShop already comes with a time-span check, which means all rules with time-span elements will be disabled if they're outdated.
If you want do implement some further availability logic, you could use the `coreshop.rule.availability_check` Event to define
the availability of the rule. Just use the `setAvailability()` method to override the system availability suggestion.
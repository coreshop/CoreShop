# CoreShop Cart Commands

## Expire Abandoned Carts

```bash
# Delete only anonymous carts
$ bin/console coreshop:cart:expire --anonymous

# Delete only user carts
$ bin/console coreshop:cart:expire --user

# Delete carts older than 20 days
$ bin/console coreshop:cart:expire --days=20
```

## Expire Abandoned Carts via Maintenance Mode
By default, this feature is disabled.
If you want to swipe abandoned carts by default you need to define a expiration date:

```yml
core_shop_order:
    expiration:
        cart:
            days: 20
            anonymous: true
            customer: true
```

Read more about automation [here](../../02_User_Documentation/10_Automation/README.md#expired-carts).
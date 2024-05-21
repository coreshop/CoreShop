# Cart Commands

CoreShop provides command line utilities to manage and expire abandoned carts.

## Expire Abandoned Carts

To manage cart expiration via the command line, use the following commands:

???bash

# Delete only anonymous carts

$ bin/console coreshop:cart:expire --anonymous

# Delete only user carts

$ bin/console coreshop:cart:expire --user

# Delete carts older than 20 days

$ bin/console coreshop:cart:expire --days=20
???

## Expire Abandoned Carts via Maintenance Job

By default, the automatic expiration of abandoned carts is disabled. To enable this feature, set an expiration date in
the configuration:

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

For more information on automation, refer to
the [Automation Documentation](../../02_User_Documentation/10_Automation/index.md).
# CoreShop State Machine - Important Things to Know

## Canceling Orders
Within the CoreShop workflow architecture a lot of orders may stay forever in the `new` or `confirmed` state.
CoreShop will automatically cancel orders older than 5 days under following conditions:

- Order creationDate >= 5 days ago
- Order State is `initialized` or `new` or `confirmed`
- Order Payment State is not `paid`

#### Expire Orders via Backend
There is also a `cancel` button in the order detail section.

#### Expire Orders via Command
There is also a command for that:

```bash
$ coreshop:order:expire
```

#### Change Orders Expiration Date

```yml
core_shop_order:
    expiration:
        order:
            days: 20
```
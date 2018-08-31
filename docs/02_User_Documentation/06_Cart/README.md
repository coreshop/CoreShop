# CoreShop Cart

## Inventory Change
If a customer adds a item to the cart CoreShop will check if product stock is sufficient. If not, a form error will show up.

## Disable / Delete Products
If a product gets disabled or deleted in backend CoreShop will automatically remove this product from all active carts.
A form error will notify every customer in frontend if the cart has been modified.

## Abandoned Carts
No cart gets deleted by default. This allows you to:
- check abandoned cart statistics
- build your own business logic in case you want to inform customers within a follow-up email for example

There is a expire cart command to remove abandoned carts, please checkout the [development section](../../03_Development/04_Cart/05_Commands.md).
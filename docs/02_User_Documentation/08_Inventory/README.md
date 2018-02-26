# CoreShop Inventory
The Inventory is a complex topic since there is a lot of business logic you need to know about.

## Product Inventory
Every Product object comes with a "Stock" Tab. Let's have a look about the configuration:

| Name | Description |
|:-----|:------------|
| Is Tracked | Define if a product should get tracked |
| On Hand | Amount of available products. |
| On Hold | Defines how many elements are currently locked. Do not change that value unless you know what you're doing. |

### Is Tracked
If you want to enable the inventory feature for a product, you need to check this setting.
After that this product is not orderable in frontend if stock is insufficient.

> **Note**: Only if you enable "Is Tracked" the inventory stock is active!
> Otherwise the product is always available regardless of it's stock amount.

### On Hand
Define a available amount for each product.
With every successfully order, an arbitrary amount will be subtracted.

### On Hold
This one needs some further explanation:
After the checkout is complete, all ordered items will be removed from "On Hand" and get moved to "On Hold" until the payment is complete:
- If the unpaid order gets cancelled, the reserved "On Hold" amount gets back to "On Hand".
- If the order payment status switches to `paid`, the reserved "On Hold" amount gets subtracted.

## Cart / Checkout
If a product stock gets insufficient during a customers checkout, the product gets removed from customers cart following by a form error.
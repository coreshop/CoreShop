# Cart

## Inventory Check on Cart Addition

When a customer adds an item to their cart, CoreShop verifies product stock availability. In cases where stock is
insufficient, the customer is informed through a form error.

## Auto-Removal of Disabled or Deleted Products

Should a product be disabled or deleted from the backend, CoreShop will automatically remove it from all active carts.
Customers with modified carts will receive a form error notification in the frontend.

## Handling Abandoned Carts

By default, carts are not deleted in CoreShop. This approach allows for:

- Analysis of abandoned cart statistics.
- Development of customized follow-up strategies, such as email notifications to customers.

For managing abandoned carts, including their removal, refer to
the [expire cart command](../../03_Development/06_Order/20_Commands.md) in the development section.

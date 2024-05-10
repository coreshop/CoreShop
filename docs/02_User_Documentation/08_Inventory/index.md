# Inventory

Managing inventory in CoreShop is a critical aspect that involves understanding several key business logics to ensure
accurate stock tracking and order fulfillment.

## Product Inventory Configuration

Each product in CoreShop includes a "Stock" tab for inventory management. Here's a breakdown of the available
configurations:

### Configuration Options

| Name       | Description                                                                          |
|:-----------|:-------------------------------------------------------------------------------------|
| Is Tracked | Indicates whether the product's stock is tracked.                                    |
| On Hand    | The current available quantity of the product.                                       |
| On Hold    | The quantity currently reserved (locked) for orders. Do not modify unless necessary. |

#### Is Tracked

To activate inventory tracking for a product, enable the "Is Tracked" setting. With this enabled, the product becomes
unorderable in the frontend if the stock is insufficient.

> **Note**: Inventory tracking is active only when "Is Tracked" is enabled. Without it, the product is considered always
> available, regardless of stock levels.

#### On Hand

This setting determines the available stock for each product. The stock decreases automatically with each successful
order.

#### On Hold

This setting requires further explanation:

- After checkout completion, ordered items shift from "On Hand" to "On Hold" until payment is complete.
- If an unpaid order is cancelled, the "On Hold" quantity returns to "On Hand".
- If the order payment status changes to `paid`, the "On Hold" quantity is permanently deducted.

## Cart and Checkout Process

During the checkout process, if a product becomes out of stock, it will be removed from the customer's cart, and a form
error will be displayed. This ensures that customers are only able to purchase items that are available in stock.

# Cart Price Rules and Vouchers

In CoreShop, Cart Price Rules provide a mechanism to apply discounts to a shopping cart. This is achieved by creating
Cart Price Rules and then adding Cart Price Rule Vouchers to the cart. For a detailed understanding of Cart Price Rules,
refer to [Cart Price Rules](./../../02_User_Documentation/03_Price_Rules/01_Cart_Price_Rules.md).

## Discount and Surcharge Application

Discounts or surcharges from Cart Price Rules are applied as [Adjustments](./05_Adjustment.md) to the order. To ensure
correct tax calculations, these discounts or surcharges are applied proportionally to the items in the cart.

### Example Scenario

Consider a cart with the following items:

| Product   | Quantity | Price   |
|-----------|----------|---------|
| Product A | 1        | €50.00  |
| Product B | 1        | €200.00 |

Product A accounts for 20% of the total price, while Product B accounts for 80%.

If a discount of €50 is applied, it will be distributed as follows:

| Product   | Quantity | Price   | Discount | Discounted Price |
|-----------|----------|---------|----------|------------------|
| Product A | 1        | €50.00  | €10.00   | €40.00           |
| Product B | 1        | €200.00 | €40.00   | €160.00          |

This example demonstrates how the discount is proportionally divided among the products based on their contribution to
the total cart value. This proportional allocation ensures that the discount is fairly distributed and that tax
calculations remain accurate.

# Price Rules

CoreShop Price Rules are powerful tools for price calculation. This section provides a detailed overview of how price
calculation works in CoreShop.

## Types of Price Rules

There are three types of Price Rules in CoreShop:

#### 1. Cart Price Rules

Apply a Price Rule to the customer's cart.

#### 2. Product Price Rules

Apply a Price Rule globally to products.

#### 3. Specific Product Prices

Apply Price Rules to specific products.

## Global Priority

The order of priority for price rules is as follows:

1. **Product Price Rules** are applied first.
2. **Specific Product Prices** are applied second.

### Example 1:

- Given: Product A with a price of 100,-
- Product Price Rule for Product A: New Price 50,-
- Specific Price Rule for Product A: New Price 20,-
- Resulting Price: 20,-

### Example 2:

- Given: Product A with a price of 100,-
- Product Price Rule for Product A: Discount Percentage 50%
- Specific Price Rule for Product A: New Price 50,-
- Resulting Price: 25,-

## Specific Price Priority

It's possible to add multiple Specific Price Rules per Product. The priority can be adjusted using the priority number
field.

## Automatic Rule Availability Checker

Rules with time-span elements included on the root level will be disabled automatically if they're outdated. Read more
about automation [here](../10_Automation/index.md).

## More Information

- [Cart Price Rules](./01_Cart_Price_Rules.md)
- [Product Price Rules](./02_Product_Price_Rules.md)
- [Specific Product Prices](./03_Specific_Price_Rules.md)
- [Vouchers](./05_Vouchers.md)
- [Available Actions](./06_Actions.md)
- [Available Conditions](./07_Conditions.md)

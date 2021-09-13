# CoreShop Price Rules
CoreShop Price Rules are very powerful tools for price calculation.
This section will help you to get a detailed overview about how price calculation works in CoreShop:

## Types of Price Rules
There are three Price Rule Types.

#### 1. Cart Price Rules
Apply Price Rule to the customers cart.

#### 2. Product Price Rules
Apply Price Rule globally.

#### 3. Specific Product Prices
Apply Price Rules to specific products.

## Global Priority
- Product Price Rules first
- Specific Price Rules second

#### Example 1:
- Given: Product A with price 100,-
- Product Price Rule for Product A: New Price 50,-
- Specific Price Rule for Product A: New Price: 20,-
- Given Price: 20,-

#### Example 2:
- Given: Product A with price 100,-
- Product Price Rule for Product A: Discount Percentage 50%
- Specific Price Rule for Product A: New Price: 50,-
- Given Price: 25,-

## Specific Price Priority
Since it's possible to add multiple Specific Price Rules per Product you can adjust the
priority via the priority number field.

## Automatic Rule Availability Checker
Rules with time-span elements included on root level will be disabled automatically if they're outdated.
Read more about automation [here](../10_Automation/README.md#expired-rules).

## More Information

- [Cart Price Rules](./01_Cart_Price_Rules.md)
- [Product Price Rules](./02_Product_Price_Rules.md)
- [Specific Product Prices](./03_Specific_Price_Rules.md)
- [Vouchers](./05_Vouchers.md)
- [Available Actions](./06_Actions.md)
- [Available Conditions](./07_Conditions.md)

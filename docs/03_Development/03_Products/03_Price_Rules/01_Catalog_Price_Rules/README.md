# CoreShop Catalog Price Rules

## Default Conditions and Actions

CoreShop comes with a set of default action and condition implementations:

### Default Conditions

 - Categories
 - Products
 - Quantity
 - Timespan
 - Countries
 - Currencies
 - Customer Groups
 - Customers
 - Stores
 - Zones
 - Nested

### Default Actions

Actions are divided into 3 groups:
 - [Discount Action](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Product/Rule/Action/ProductDiscountActionProcessorInterface.php)
 - [Discount Price Action](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Product/Rule/Action/ProductDiscountPriceActionProcessorInterface.php)
 - [Price Action](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Product/Rule/Action/ProductPriceActionProcessorInterface.php)

#### Discount Action
Used to calculate product discounts. Following actions are available:

 - Discount Amount
 - Discount Percent

#### Discount Price Action
Used to calculate a discounted product price. Following actions are available:

 - Discount Price

#### Price Action
Used to calculate a new retail price. Following actions are available:

 - Price

## Extending Conditions and Actions

 - [Click here to see how you can add custom Actions](../../../01_Extending_Guide/04_Extending_Rule_Actions.md)
 - [Click here to see how you can add custom Conditions](../../../01_Extending_Guide/05_Extending_Rule_Conditions.md)
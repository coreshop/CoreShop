# CoreShop Shipping Rules

![Shipping Rules](img/shipping-rules.png)

CoreShop uses "Shipping Rules" for Carriage Price Calculation. Therefore, one Shipping Rule it exists of:

 - **Settings**: Basic Settings for a Shipping Rule
 - **Conditions**: Conditions are validated on Carriage Price determination. If all conditions are true, it will continue with actions
 - **Actions**: Actions define the Carriage Price

## Conditions

CoreShop currently supports these Condition Types:

 - **Nested Conditions**: You can nest different kind of conditions to create fuller, richer conditions
 - **Countries**: Which Countries are valid
 - **Amount**: Min Cart Value
 - **Weight**: Max Cart Weight
 - **Dimensions**: Max Dimensions of the Products in Cart
 - **Zones**: Which Zones are valid
 - **Postcodes**: Which Post Codes are valid
 - **Products**: Which Products are valid
 - **Categories**: Which Categories are valid
 - **Customer Groups**: Which Customer Groups are valid
 - **Currencies**: Which Currencies are valid
 - **Shipping Rule**: Validates another Shipping Rule

It is possible to extend this list with [your own Shipping Rule Condition](./03_Create_Shipping_Rule_Condition.md)

## Action

CoreShop currently supports these Action Types:

 - **Fixed Price**: Price for carriage
 - **Addition Amount**: Adds an specific addition value
 - **Addition Percent**: Adds an specific addition in percent
 - **Discount Amount**: Adds an specific discount value
 - **Discount Percent**: Adds an specific discount in percent
 - **Shipping Rule**: Calculates the Price from another Shipping Rule

It is possible to extend this list with [your own Shipping Rule Action](./04_Create_Shipping_Rule_Action.md)

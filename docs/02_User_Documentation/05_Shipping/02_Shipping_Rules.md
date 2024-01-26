# Shipping Rules

CoreShop Shipping Rules are a key feature within the CoreShop e-commerce framework on the Pimcore platform, enabling you
to define shipping costs through customizable rules. These rules can be based on various conditions such as weight,
price, dimensions, and shipping destination.

## Overview

Shipping rules in CoreShop are composed of conditions and actions:

- **Conditions**: Determine the applicability of a rule.
- **Actions**: Define the adjustments to shipping costs when a rule is applied.

You can create a variety of rules, each tailored to specific shipping scenarios.

## Creating Shipping Rules

1. **Access Shipping Rules**: Navigate to the "Shipping" tab and select "Shipping Rules."
2. **Add New Rule**: Click "Add new" to start creating a rule.
3. **Configure Rule**: Name the rule and adjust settings according to your requirements.

## Configuring Conditions

Conditions are criteria that must be met for a shipping rule to apply. CoreShop supports multiple conditions per rule,
including:

- **Weight**: Based on the total weight of the order.
- **Price**: Based on the total price of the order.
- **Dimension**: Based on order dimensions (length, width, height).
- **Quantity**: Based on the total quantity of items.
- **Countries**: Targeting specific shipping destinations.
- **Zones**: Focusing on particular geographic zones.

### Setting Up a Condition

- Click "Add Condition" in the rule's "Conditions" section.
- Select a condition type and configure its parameters.

Example: For orders over 10kg, add a weight condition with a minimum weight of 10kg.

## Configuring Actions

Actions determine how shipping costs are modified when a rule is triggered. CoreShop allows for multiple actions per
rule, such as:

- **Discount Amount**: A fixed discount on shipping costs.
- **Discount Percentage**: A percentage discount on shipping costs.
- **Add Amount**: An additional fixed amount to the shipping costs.
- **Add Percentage**: An extra percentage to the shipping costs.

### Setting Up an Action

- Click "Add Action" in the rule's "Actions" section.
- Choose an action type and set the necessary parameters.

Example: For a 20% shipping discount, add a discount percentage action and set it to 20%.

## Summary

CoreShop Shipping Rules provide a versatile and robust mechanism to manage shipping costs in your e-commerce store. By
leveraging various conditions and actions, you can tailor shipping costs to meet diverse requirements and offer precise
shipping estimates to your customers.

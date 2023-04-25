# CoreShop Shipping Rules Documentation

CoreShop Shipping Rules is a powerful feature within the CoreShop e-commerce framework built on the Pimcore platform. It allows you to create flexible and customizable shipping rules that define shipping costs based on various conditions such as weight, price, dimensions, and shipping destination.

## Overview

Shipping rules in CoreShop can be composed of conditions and actions. Conditions determine whether a rule should be applied, while actions define the changes to the shipping costs.

CoreShop provides a user-friendly interface for creating and managing shipping rules. You can create multiple rules, each with different conditions and actions, to cover various scenarios.

## Creating Shipping Rules

 - Navigate to the "Shipping" tab and click on "Shipping Rules."
 - Click the "Add new" button to create a new shipping rule.
 - Enter a name for the rule and configure the other available options as needed.

## Configuring Conditions

Conditions determine whether the shipping rule should be applied to an order. You can create multiple conditions for a single rule, and all conditions must be met for the rule to be applied. CoreShop offers several types of conditions, including:

 - Weight: Based on the total weight of the order.
 - Price: Based on the total price of the order.
 - Dimension: Based on the dimensions (length, width, and height) of the order.
 - Quantity: Based on the total quantity of items in the order.
 - Countries: Based on the shipping destination country.
 - Zones: Based on the shipping destination zone (a group of countries). 

To add a condition to your shipping rule:

 - Click the "Add Condition" button in the "Conditions" section of the shipping rule.
 - Choose the desired condition type from the dropdown menu.
 - Configure the condition parameters as needed.
 
For example, if you want to apply a shipping rule only to orders with a total weight of over 10kg, you would add a weight condition and set the minimum weight to 10kg.

## Configuring Actions
Actions define the changes to the shipping costs when a rule is applied. You can create multiple actions for a single rule. CoreShop offers several types of actions, including:

 - Discount Amount: Apply a fixed discount amount to the shipping cost.
 - Discount Percentage: Apply a percentage discount to the shipping cost.
 - Add Amount: Add a fixed amount to the shipping cost.
 - Add Percentage: Add a percentage amount to the shipping cost.

To add an action to your shipping rule:

 - Click the "Add Action" button in the "Actions" section of the shipping rule.
 - Choose the desired action type from the dropdown menu.
 - Configure the action parameters as needed.

For example, if you want to apply a 20% discount to the shipping cost when the rule is applied, you would add a discount percentage action and set the percentage value to 20.

# Summary
CoreShop Shipping Rules offer a powerful and flexible way to define and manage shipping costs for your e-commerce store. By creating various conditions and actions, you can customize shipping costs based on factors such as weight, price, dimensions, and destination. This allows you to cater to different customer needs and provide more accurate shipping estimates.
# Shipping Rules

CoreShop provides a robust set of default conditions and actions for shipping rules, enabling you to tailor the shipping
process according to various criteria and actions. Additionally, CoreShop allows for the extension of these rules
through custom actions and conditions.

## Default Conditions and Actions

### Default Conditions

CoreShop includes a variety of predefined conditions for shipping rules:

- **Currencies**: Rules based on currency type.
- **Customer Groups**: Rules targeting specific customer groups.
- **Customers**: Rules applicable to individual customers.
- **Stores**: Rules for specific store locations.
- **Zones**: Rules pertaining to geographical zones.
- **Amount**: Rules based on the amount in the cart.
- **Categories**: Rules for specific product categories.
- **Countries**: Rules for shipments to specific countries.
- **Dimension**: Rules based on the dimensions of the products.
- **Post Codes**: Rules for specific postal code areas.
- **Products**: Rules applicable to individual products.
- **Shipping Rule**: Rules that trigger based on other shipping rules.
- **Weight**: Rules based on the weight of the cart items.
- **Nested**: Complex rules combining multiple conditions.

### Default Actions

The default actions included in CoreShop are:

- **Addition Amount**: Add a fixed amount to the shipping cost.
- **Addition Percent**: Add a percentage to the shipping cost.
- **Discount Amount**: Apply a discount amount to the shipping cost.
- **Discount Percent**: Apply a discount percentage to the shipping cost.
- **Price**: Set a fixed price for shipping.
- **Shipping Rule**: Trigger other shipping rules.

## Reusing Shipping Rules

The **Shipping Rule** condition and action let a rule reuse another Shipping Rule: the condition is valid if the
referenced rule is valid, the action adds the price (and price modifications) of the referenced rule.

A rule must never end up referencing itself, neither directly (A uses A) nor through other rules (A uses B, B uses A),
also not from inside a **Nested** condition. CoreShop protects against that on two levels:

- **When saving:** a Shipping Rule that would close such a circle is rejected with a validation error that shows the
  path, e.g. `Express → Heavy Goods → Express`. A rule that only uses rules which reference each other (but not the rule
  being saved) can still be saved.
- **While calculating:** rules created before this check (or written to the database directly) do not crash the
  checkout. As soon as a rule is reached a second time during the same evaluation, it is treated as *not valid*
  (conditions) or as adding *nothing* (actions: `0`), and a warning with the path is written to the log.

See [Rules referencing other Rules](../../01_Extending_Guide/05_Extending_Rule_Conditions.md#rules-referencing-other-rules)
for how to make custom conditions and actions that reference rules part of this check.

## Extending Conditions and Actions

CoreShop also provides options to extend these default rules with custom conditions and actions:

- **Custom Actions
  **: [Learn how to add custom actions to your shipping rules](../../01_Extending_Guide/04_Extending_Rule_Actions.md).
- **Custom Conditions
  **: [Discover how to create custom conditions for more tailored shipping rules](../../01_Extending_Guide/05_Extending_Rule_Conditions.md).

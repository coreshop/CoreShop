# Taxation

CoreShop's flexible taxation system enables you to configure tax rules based on countries, states, zip codes, and tax
categories. This system is designed to handle complex tax scenarios across different regions and products.

## Setting Up Taxation

To configure taxation effectively in CoreShop, follow these essential steps:

1. **Create a Tax Rate**: Begin by setting up a tax rate. This rate represents the percentage of tax applied to a
   product or service.
2. **Create a Tax Rule**: Tax rules define the conditions under which a particular tax rate is applied. These rules can
   be based on various factors such as country, state, or zip code.
3. **Assign the Tax Rule to a Tax Rate**: Link your tax rule to the appropriate tax rate. This step ensures that the
   correct rate is applied based on the defined conditions in the tax rule.
4. **Assign the Tax Rule to a Product**: Finally, assign the tax rule to the products it applies to. This step
   integrates your tax configuration with your product catalog.

## Understanding Gross/Net Product Prices

The concept of gross and net prices in CoreShop is pivotal and depends on your store's configuration:

- **Gross Product Price**: If the store is configured for gross pricing, the product prices entered are treated as
  inclusive of tax. This means that the listed price includes the tax amount.
- **Net Product Price**: Conversely, if the store is set up for net pricing, the product prices are exclusive of tax. In
  this case, the tax is calculated and added to the net price at the point of sale.

CoreShop automatically adjusts the final price, adding or subtracting the tax amount based on whether the product price
is configured as gross or net. This feature ensures accurate pricing and tax calculation across different store
configurations.


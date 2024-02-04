# Carrier Price Calculation

In CoreShop, the calculation of shipping prices for various carriers involves multiple methods. These methods are
crucial for determining the appropriate shipping cost for a carrier based on the specifics of a given cart.

## Calculator Implementation

To calculate shipping prices, a Calculator must implement the
interface `CoreShop\Component\Shipping\Calculator\CarrierPriceCalculatorInterface`. This interface defines the necessary
functionality for calculating shipping costs.

Calculators should be registered in the container with the tag `coreshop.shipping.price_calculator`, and should include
a `type` attribute and a `priority`. This registration process ensures that CoreShop recognizes and utilizes the
calculator correctly during the shipping price calculation process.

## Default Implementation

The default implementation for carrier price calculation in CoreShop is based
on [Shipping Rules](../02_Shipping_Rules/index.md). This method takes into account various rules defined for shipping,
which can include factors like the weight of the cart, the destination, and other relevant criteria. By using these
rules, CoreShop can accurately calculate the shipping price for different carriers, ensuring a fair and consistent
approach to shipping costs.

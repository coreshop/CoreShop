# CoreShop Carrier Price Calculation

CoreShop Shipping/Carrier Calculation uses multiple Calculation methods to calculate the price for a given Carrier on a given
Cart. A Calculator needs to implement the Interface ```CoreShop\Component\Shipping\Calculator\CarrierPriceCalculatorInterface```
and registered to the container using the tag ```coreshop.shipping.price_calculator```, a ```type``` attribute and a ```priority```

## Default Implementation

The Default Implementation calculates the Price based on [Shipping Rules](../02_Shipping_Rules).
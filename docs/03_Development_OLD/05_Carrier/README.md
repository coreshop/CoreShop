# CoreShop Carrier

CoreShop already has a feature for Carriers. But if you need some different shipping cost calculation, you can implement your own Carrier Plugin. 

To implement a custom Carrier, you need to:

1. Implement the class CoreShop\Bundle\LegacyBundle\Model\Carrier.
2. Create a Carrier in CoreShop and fill the "class" column in the database table "coreshop_carriers".

You can find a example implementation [here](https://github.com/coreshop/coreshop-carrier-custom)

## Shipping Rules
CoreShop uses "Shipping Rules" for Carriage Price Calculation. [Read more about Shipping Rules](./02_Shipping_Rules.md)

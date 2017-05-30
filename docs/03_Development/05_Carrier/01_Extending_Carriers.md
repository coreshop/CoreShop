# Extending CoreShop Carriers

CoreShop implements Carrier Price Calculation using Shipping Rules Conditions/Actions.

But if you need some different kind of shipping cost calculation, you can implement your own Carrier Price Calculator.

Your Carrier Price Calculator needs to implement ```CoreShop\Bundle\ShippingBundle\Calculator\CarrierPriceCalculatorInterface```

## Create a Custom Carrier

1. Add a new Price Calculator Class

```php
namespace Acme\Carrier;

class AcmeCarrierPriceCalculation extends \CoreShop\Bundle\ShippingBundle\Calculator\CarrierPriceCalculatorInterface
{

    public function getPrice(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, $withTax = true)
    {
        return $withTax ? 12 : 10;
    }
}

```

2. Register your AcmeCarrierPriceCalculation with tag coreshop.shipping.price_calculator and a priority. The
   Priority determines which calculator should be processed first. First to return a float wins!

```
acme.coreshop.carrier.price_calculator:
    class: Acme\Carrier\AcmeCarrierPriceCalculation
    tags:
      - { name: coreshop.shipping.price_calculator, type: acme, priority: 1 }
```
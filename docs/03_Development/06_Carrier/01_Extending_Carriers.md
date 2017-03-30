# Extending CoreShop Carriers

CoreShop implements Carrier Price Calculation using Conditions/Actions.

But if you need some different kind of shipping cost calculation, you can implement your own Carrier Plugin.

## Create a Custom Carrier

1. Add a new Carrier File wherevere you want, for example:

```php
namespace CustomCarrierDemo;

use CoreShop\Bundle\LegacyBundle\Model\Carrier as CoreShopCarrier;
use CoreShop\Bundle\LegacyBundle\Model\Cart;
use CoreShop\Bundle\LegacyBundle\Model\User\Address;
use CoreShop\Bundle\LegacyBundle\Model\Zone;

class Carrier extends CoreShopCarrier
{

    public function getMaxDeliveryPrice(Address $address = null)
    {
        return 1000;
    }

    public function getDeliveryPrice(Cart $cart, Address $address = null)
    {
        return 100;
    }

    public function checkCarrierForCart(Cart $cart = null, Address $address = null)
    {
        return true;
    }
}

```

2. Create a new carrier in CoreShop
3. Open PhpMyAdmin (or Adminer) and open the table "coreshop_carriers"
4. Find your newly created carrier in the table
4. Edit the "class" column and fill in your Class Name "\CustomCarrierDemo\Carrier"
5. CoreShop will no instanciate your class for this Carrier
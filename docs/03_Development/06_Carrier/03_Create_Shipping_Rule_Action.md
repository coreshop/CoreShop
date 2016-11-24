
#### CoreShop Custom Shipping Rule Action

Create a file in your Plugin (or Website, I recommend creating a Plugin):

```
YourPlugin/lib/CoreShop/Model/Carrier/ShippingRule/Action/YourAction.php
```

```php
namespace CoreShop\Model\Carrier\ShippingRule\Action;

use CoreShop\Model;
use CoreShop\Model\Carrier\ShippingRule;
use CoreShop\Tool;

class YourAction extends AbstractAction
{
    public $type = 'yourAction';

    public function getPriceModification(Model\Carrier $carrier, Cart $cart, Model\User\Address $address, $price)
    {
        //You can return some kind of modification here
        return 0;
    }

    public function getPrice(Model\Carrier $carrier, Cart $cart, Model\User\Address $address)
    {
        //You can return a fixed price here
        return false;
    }
}
```

You also need to create a js file for the ShippingRule UI.

```
YourPlugin/static/js/coreshop/carrier/shippingRule/action/yourAction.js
```

```js
pimcore.registerNS('pimcore.plugin.coreshop.carrier.shippingrules.actions.yourAction');

pimcore.plugin.coreshop.carrier.shippingrules.actions.yourAction = Class.create(pimcore.plugin.coreshop.rules.actions.abstract, {
    type : 'yourAction',

    getForm : function () {
        this.form = Ext.create('Ext.form.FieldSet', {
            items : [
                //add some extjs fields here
            ]
        });

        return this.form;
    }
});

```

You also need to register your new Action to CoreShop:

```php
\CoreShop\Model\Carrier\ShippingRule::addAction("YourAction");
```

You can find more examples in the CoreShopExamples Project [https://github.com/coreshop/CoreShopExamples](https://github.com/coreshop/CoreShopExamples)
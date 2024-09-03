# Custom Price-Rule/Shipping-Rule/Notification-Rule Actions

Adding Price-, Shipping- or Notification-Rule Actions is the same for all of these types. Their only difference is the
tag you use and Interface you need to implement for them.

| Action Type            | Tag                                         | Interface                                                                                                                                                                                                                         |
|------------------------|---------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Cart Price Rule        | coreshop.cart_price_rule.action             | [```CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Cart/Rule/Action/CartPriceRuleActionProcessorInterface.php) |
| Product Price Rule     | coreshop.product_price_rule.action          | [```CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Product/Rule/Action/ProductPriceActionProcessorInterface.php)         |
| Product Specific Price | coreshop.product_specific_price_rule.action | [```CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Product/Rule/Action/ProductPriceActionProcessorInterface.php)         |
| Shipping Rule          | coreshop.shipping_rule.action               | [```CoreShop\Component\Shipping\Rule\Action\CarrierPriceActionProcessorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Shipping/Rule/Action/CarrierPriceActionProcessorInterface.php)       |
| Notification Rule      | coreshop.notification_rule.action           | [```CoreShop\Component\Notification\Rule\Action\NotificationRuleProcessorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Notification/Rule/Action/NotificationRuleProcessorInterface.php)   |

## Example Adding a new Action

Now, let's add a new Action for Product Price Rules.

To do so, we first need to create a new class and implement the interface listed in the table above. For Product Price
Rules, we need to use
[```CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Product/Rule/Action/ProductPriceActionProcessorInterface.php)

```php
namespace App\CoreShop;

final class CustomAction implements \CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface
{
    public function getPrice($subject, array $context, array $configuration): int 
    {
        //If your action gives the product a new Price, put your calculation here

        return $configuration['some_value'];
    }
}
```

We also need a FormType for the actions configurations:

```php
<?php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class CustomActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('some_value', TextType::class)
        ;
    }
}
```

With configuration, comes a Javascript file as well:

```javascript
// public/coreshop/js/custom_action.js

pimcore.registerNS('coreshop.product.pricerule.actions.custom');
coreshop.product.pricerule.actions.custom = Class.create(coreshop.rules.actions.abstract, {

    type: 'custom',

    getForm: function () {
        var some_value = 0;
        var me = this;

        if (this.data) {
            some_value = this.data.some_value / 100;
        }

        var some_valueField = new Ext.form.NumberField({
            fieldLabel: t('custom'),
            name: 'some_value',
            value: some_value,
            decimalPrecision: 2
        });

        this.form = new Ext.form.Panel({
            items: [
                some_valueField
            ]
        });

        return this.form;
    }
});
```

Don't forget to run the following command afterwards to deploy it if needed. If you're using the latest symfony
structure, omit the web.

```
bin/console assets:install web
```

## Registering the Custom Action to the Container and load the Javascript File

We now need to create our Service Definition for our Custom Action:

```yaml
App\CoreShop\CustomAction:
  tags:
    - { name: coreshop.product_price_rule.action, type: custom, form-type: App\CoreShop\Form\Type\CustomActionType }
```

and add this to your config.yml:

```yaml
core_shop_product:
  pimcore_admin:
    js:
      custom_action: '/coreshop/js/custom_action.js'
```

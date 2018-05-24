# Custom Price-Rule/Shipping-Rule/Notification-Rule Actions

Adding Price-, Shipping- or Notification-Rule Actions is the same for all of these types. They're only difference is the
tag you use and Interface you need to implement for them.


| Action Type               | Tag                                           | Interface |
| ------------------------- | --------------------------------------------- | --------- |
| Cart Price Rule           | coreshop.cart_price_rule.action               | [```CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Cart/Rule/Action/CartPriceRuleActionProcessorInterface.php)   |
| Product Price Rule        | coreshop.product_price_rule.action            | [```CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Product/Rule/Action/ProductPriceActionProcessorInterface.php)       |
| Product Specific Price    | coreshop.product_specific_price_rule.action   | [```CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Product/Rule/Action/ProductPriceActionProcessorInterface.php)       |
| Shipping Rule             | coreshop.shipping_rule.action                 | [```CoreShop\Component\Shipping\Rule\Action\CarrierPriceActionProcessorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Shipping/Rule/Action/CarrierPriceActionProcessorInterface.php)      |
| Notification Rule         | coreshop.notification_rule.action             | [```CoreShop\Component\Notification\Rule\Action\NotificationRuleProcessorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Notification/Rule/Action/NotificationRuleProcessorInterface.php)    |

## Example Adding a new Action
Now, lets add a new Action for Product Price Rules.

To do so, we first need to create a new class and implement the interface listed in the table above. For Product Price Rules, we need to use
[```CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Product/Rule/Action/ProductPriceActionProcessorInterface.php)

```php
//AppBundle/CoreShop/CustomAction.php
namespace AppBundle\CoreShop;

final class CustomAction implements \CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface
{
    public function getDiscount($subject, $price, array $configuration) {
        //If your action calculates a discount, put your calculation here
    }

    public function getPrice($subject, array $configuration) {
        //If your action gives the product a new Price, put your calculation here

        return $configuration['some_value'];
    }
}
```

We also need a FormType for the actions configurations:

```php
//AppBundle/Form/Type/CustomActionType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class CustomActionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('some_value', TextType::class)
        ;
    }
}
```

With configuration, comes a Javascript file as well:

```javascript
//AppBundle/Resources/public/pimcore/js/custom_action.js

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
Don't forget to run the following command afterwards to deploy it if needed:
```
bin/console assets:install
``` 



## Registering the Custom Action to the Container and load the Javascript File
We now need to create our Service Definition for our Custom Action:

```yaml
app.product_price_rule.custom:
    class: AppBundle\CoreShop\CustomAction
    tags:
      - { name: coreshop.product_price_rule.action, type: custom, form-type: AppBundle\Form\Type\CustomActionType }
```

and add this to your config.yml:

```yaml
core_shop_product:
    pimcore_admin:
        js:
            custom_action: '/bundles/app/pimcore/js/custom_action.js'
```

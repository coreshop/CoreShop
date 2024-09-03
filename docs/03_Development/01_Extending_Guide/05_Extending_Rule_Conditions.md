# Custom Price-Rule/Shipping-Rule/Notification-Rule Conditions

Adding Price-, Shipping- or Notification-Rule Conditions is the same for all of these types. They're only difference is
the
tag you use and Interface you need to implement for them.

| Action Type            | Tag                                            | Interface/AbstractClass                                                                                                                                                                                           |
|------------------------|------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Cart Price Rule        | coreshop.cart_price_rule.condition             | [```CoreShop\Component\Order\Cart\Rule\Condition\AbstractConditionChecker```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Cart/Rule/Condition/AbstractConditionChecker.php)     |
| Product Price Rule     | coreshop.product_price_rule.condition          | [```CoreShop\Component\Rule\Condition\ConditionCheckerInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Rule/Condition/ConditionCheckerInterface.php)                         |
| Product Specific Price | coreshop.product_specific_price_rule.condition | [```CoreShop\Component\Rule\Condition\ConditionCheckerInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Rule/Condition/ConditionCheckerInterface.php)                         |
| Shipping Rule          | coreshop.shipping_rule.condition               | [```CoreShop\Component\Shipping\Rule\Condition\CategoriesConditionChecker```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Shipping/Rule/Condition/AbstractConditionChecker.php)       |
| Notification Rule      | coreshop.notification_rule.condition           | [```CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Notification/Rule/Condition/AbstractConditionChecker.php) |

## Example Adding a new Condition

Now, lets add a new Condition for Product Price Rules.

To do so, we first need to create a new class and implement the interface listed in the table above. For Product Price
Rules, we need to use
[```CoreShop\Component\Rule\Condition\ConditionCheckerInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Rule/Condition/ConditionCheckerInterface.php)

```php
//src/App/CoreShop/CustomCondition.php
namespace App\CoreShop;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

final class CustomCondition implements \CoreShop\Component\Rule\Condition\ConditionCheckerInterface
{
public function isValid(ResourceInterface $subject, RuleInterface $rule, array $configuration, array $params = []): bool
{
//return true if valid, false if not
return true;
}
```

We also need a FormType for the conditions configurations:

```php
//src/App/CoreShop/Form/Type/CustomConditionType.php
namespace App\CoreShop\Form\Type;

final class CustomConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       $builder->add('some_value', TextType::class);
    }
}
```

With configuration, comes a Javascript file as well:

```javascript
//public/coreshop/js/custom_condition.js

pimcore.registerNS('coreshop.product.pricerule.conditions.custom');
coreshop.product.pricerule.conditions.custom = Class.create(coreshop.rules.conditions.abstract, {

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

## Registering the Custom Condition to the Container and load the Javascript File

We now need to create our Service Definition for our Custom Condition:

```yaml
App\CoreShop\CustomCondition:
  tags:
    - { name: coreshop.product_price_rule.condition, type: custom, form-type: App\CoreShop\Form\Type\CustomConditionType }
```

and add this to your config.yml:

```yaml
core_shop_product:
    pimcore_admin:
        js:
            custom_condition: '/coreshop/js/custom_condition.js'

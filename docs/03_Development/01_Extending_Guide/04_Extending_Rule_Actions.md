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

## Pimcore Studio (React)

### Schema-Driven (Recommended — No Custom JS Needed)

**The service registration above is all you need for Pimcore Studio.** The `form-type` attribute in the service tag automatically makes the configuration form available in Studio. No React/TypeScript code is required.

The StudioFormBundle renders the PHP FormType (`CustomActionType`) as a React form at runtime. The `get-config` endpoint returns a `actionSchemaByType` mapping, and `registerSchemaComponentsFromConfig()` auto-generates the React component from the schema.

For details, see [StudioFormBundle — Rule Engine Integration](../14_Studio/02_Base_Infrastructure/05_StudioFormBundle_Examples.md#example-13--rule-conditionaction-as-schema-form).

### Hand-Written React Component (Only for Special UIs)

If your action needs custom interactive behavior that cannot be expressed as a Symfony FormType (e.g., complex multi-step wizards, drag-and-drop), you can still create a hand-written React component:

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/product-price-rules/actions/CustomAction.tsx

import React from 'react'
import { Form, InputNumber } from 'antd'
import type { ActionComponentProps } from '@coreshop/rule/src/rules'

interface CustomActionData {
  some_value?: number
}

export const CustomAction: React.FC<ActionComponentProps> = ({
  data,
  onChange
}) => {
  const actionData = data as CustomActionData
  const someValue = actionData.some_value || 0

  const handleChange = (value: number | null) => {
    onChange({
      ...actionData,
      some_value: value || 0
    })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Custom Value">
        <InputNumber
          value={someValue}
          onChange={handleChange}
          precision={2}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}
```

Register the hand-written action in your bundle's main plugin file. Hand-written components take priority over schema-generated ones:

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/main.ts

import { IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import type { ActionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopProductServiceIds } from '@coreshop/product/src/modules/product-price-rules/service-ids'
import { CustomAction } from './modules/product-price-rules/actions/CustomAction'

const plugin: IAbstractPlugin = {
    name: 'your-bundle',

    onInit() {
        // Get the ProductPriceRule action registry from the container
        const actionRegistry = container.get<ActionRegistry>(
            coreshopProductServiceIds.productPriceRuleActionRegistry
        )

        // Register the custom action
        actionRegistry.register('custom', CustomAction)
    }
}

export default plugin
```

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
```

## Pimcore Studio (React)

For the new Pimcore Studio UI, you need to create a React component instead of ExtJS:

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/product-price-rules/conditions/CustomCondition.tsx

import React from 'react'
import { Form, InputNumber } from 'antd'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'

interface CustomConditionData {
  some_value?: number
}

export const CustomCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const conditionData = data as CustomConditionData
  const someValue = conditionData.some_value || 0

  const handleChange = (value: number | null) => {
    onChange({
      ...conditionData,
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

### Registering the React Condition

Register the condition in your bundle's main plugin file:

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/main.ts

import { IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import type { ConditionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopProductServiceIds } from '@coreshop/product/src/modules/product-price-rules/service-ids'
import { CustomCondition } from './modules/product-price-rules/conditions/CustomCondition'

const plugin: IAbstractPlugin = {
    name: 'your-bundle',

    onInit() {
        // Get the ProductPriceRule condition registry from the container
        const conditionRegistry = container.get<ConditionRegistry>(
            coreshopProductServiceIds.productPriceRuleConditionRegistry
        )

        // Register the custom condition
        conditionRegistry.register('custom', CustomCondition)
    }
}

export default plugin
```

### Condition Without Configuration

If your condition doesn't need any configuration UI, you can use the built-in `EmptyCondition`:

```typescript
import { EmptyCondition } from '@coreshop/rule/src/rules'

conditionRegistry.register('customConditionWithoutConfig', EmptyCondition)
```

### Using Entity Selects in Conditions

For selecting entities (e.g., countries, customers), use the `useEntitySelect` hook:

```typescript
import { useEntitySelect } from '@coreshop/resource'
import { countryApi } from '@coreshop/address/src/modules/countries/api'

export const CustomCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => {
  const countryIds = data.countries || []
  const [options, value, handleSelectChange, loading] = useEntitySelect(countryApi, countryIds)

  const handleChange = (selectedIds: number[]) => {
    handleSelectChange(selectedIds)
    onChange({ ...data, countries: selectedIds })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Countries">
        <Select
          mode="multiple"
          value={value}
          onChange={handleChange}
          options={options}
          loading={loading}
          showSearch
          optionFilterProp="label"
        />
      </Form.Item>
    </Form>
  )
}
```

The `useEntitySelect` hook automatically:
- Loads all available entities from the API
- Loads missing entities (for saved rules)
- Prevents duplicate selections
- Provides loading state
- Returns properly formatted options for the Select component

### Nested Conditions

For conditions that contain sub-conditions (like "AND"/"OR" logic), use the `NestedCondition`:

```typescript
import { NestedCondition } from '@coreshop/core/src/modules/shared/rules/conditions'

conditionRegistry.register('nested', NestedCondition)
```

### Available Form Components

The Studio UI uses Ant Design components. Commonly used form components:

- `InputNumber` - Number input with precision
- `Input` - Text input
- `Select` - Dropdown selection (with `mode="multiple"` for multi-select)
- `Checkbox` - Boolean values
- `DatePicker` - Date selection
- `Switch` - Toggle switch
- `TimePicker` - Time selection

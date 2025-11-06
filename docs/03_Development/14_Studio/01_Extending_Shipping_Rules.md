# Extending Shipping Rules in Pimcore Studio v2

This guide explains how to extend Shipping Rules with custom Actions and Conditions in the new Pimcore Studio (React/TypeScript).

## Overview

Shipping Rules in CoreShop allow you to define conditions under which shipping costs are calculated. The system consists of:

- **Conditions**: Define when a shipping rule applies (e.g., weight, amount, postcodes)
- **Actions**: Define how the shipping cost is calculated (e.g., fixed price, percentage discount)

## Architecture

### Separate Registries

Each rule engine (Cart Price Rules, Product Price Rules, Shipping Rules) has **separate ConditionRegistry and ActionRegistry instances**. This is a fundamental design principle:

```typescript
// ShippingBundle creates its own registries
container.bind(coreshopShippingServiceIds.shippingRuleConditionRegistry)
    .to(ConditionRegistry).inSingletonScope()
container.bind(coreshopShippingServiceIds.shippingRuleActionRegistry)
    .to(ActionRegistry).inSingletonScope()
```

### Bundle Responsibilities

- **ShippingBundle**: Registers shipping-specific conditions/actions (weight, dimension, price actions)
- **CoreBundle**: Registers shared conditions/actions across all rule types (categories, products, customers, etc.)

## Adding a Custom Shipping Rule Action

### Step 1: Create the React Component

Create a new TypeScript file for your action component:

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/shipping-rules/actions/CustomShippingAction.tsx

import React from 'react'
import { Form, InputNumber, Select } from 'antd'
import type { ActionComponentProps } from '@coreshop/rule/src/rules/types'
import { currencyApi } from '@coreshop/currency/src/modules/currencies/api'

export const CustomShippingAction: React.FC<ActionComponentProps> = ({ data, onChange }) => {
  const [form] = Form.useForm()
  const [currencies, setCurrencies] = React.useState<Array<{ value: number, label: string }>>([])

  React.useEffect(() => {
    form.setFieldsValue(data ?? {})
  }, [data])

  React.useEffect(() => {
    void loadCurrencies()
  }, [])

  const loadCurrencies = async () => {
    try {
      const list = await currencyApi.list()
      setCurrencies(list.map(c => ({
        value: c.id!,
        label: c.name ?? `#${c.id}`
      })))
    } catch (err) {
      console.error('Failed to load currencies:', err)
    }
  }

  return (
    <Form
      form={form}
      layout="vertical"
      onValuesChange={(_, allValues) => {
        onChange(allValues)
      }}
    >
      <Form.Item
        label="Surcharge"
        name="surcharge"
        help="Additional shipping cost"
        rules={[{ required: true, message: 'Surcharge is required' }]}
      >
        <InputNumber
          min={0}
          step={1}
          precision={0}
          style={{ width: '100%' }}
          placeholder="Surcharge"
        />
      </Form.Item>

      <Form.Item
        label="Currency"
        name="currency"
        rules={[{ required: true, message: 'Currency is required' }]}
      >
        <Select
          placeholder="Select currency"
          options={currencies}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}
```

### Step 2: Export the Action

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/shipping-rules/actions/index.ts

export * from './CustomShippingAction'
```

### Step 3: Register the Action

Register the action in your bundle's main plugin file:

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/main.ts

import { IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import type { ActionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopShippingServiceIds } from '@coreshop/shipping/src/modules/shipping-rules/service-ids'
import { CustomShippingAction } from './modules/shipping-rules/actions'

const plugin: IAbstractPlugin = {
    name: 'your-bundle',

    onInit() {
        // Get the ShippingRule action registry from the container
        const actionRegistry = container.get<ActionRegistry>(
            coreshopShippingServiceIds.shippingRuleActionRegistry
        )

        // Register the custom action
        actionRegistry.register('customSurcharge', CustomShippingAction)
    }
}

export default plugin
```

### Important: Data Handling

**CRITICAL**: Actions receive `data` as the configuration object directly, NOT `data.configuration`:

```typescript
// ❌ WRONG
React.useEffect(() => {
    form.setFieldsValue(data.configuration ?? {})
}, [data])

const handleChange = () => {
    onChange({ configuration: allValues })
}

// ✅ CORRECT
React.useEffect(() => {
    form.setFieldsValue(data ?? {})
}, [data])

const handleChange = () => {
    onChange(allValues)
}
```

## Adding a Custom Shipping Rule Condition

### Step 1: Create the React Component

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/shipping-rules/conditions/VolumeCondition.tsx

import React from 'react'
import { Form, InputNumber } from 'antd'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'

export const VolumeCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => {
  const [form] = Form.useForm()

  React.useEffect(() => {
    form.setFieldsValue(data ?? {})
  }, [data])

  return (
    <Form
      form={form}
      layout="vertical"
      onValuesChange={(_, allValues) => {
        onChange(allValues)
      }}
    >
      <Form.Item
        label="Min Volume"
        name="minVolume"
        help="Minimum volume in cubic cm"
      >
        <InputNumber
          min={0}
          step={100}
          precision={0}
          style={{ width: '100%' }}
          placeholder="Min volume"
          addonAfter="cm³"
        />
      </Form.Item>

      <Form.Item
        label="Max Volume"
        name="maxVolume"
        help="Maximum volume in cubic cm"
      >
        <InputNumber
          min={0}
          step={100}
          precision={0}
          style={{ width: '100%' }}
          placeholder="Max volume"
          addonAfter="cm³"
        />
      </Form.Item>
    </Form>
  )
}
```

### Step 2: Export and Register

```typescript
// Export
export * from './VolumeCondition'

// Register in main.ts
const conditionRegistry = container.get<ConditionRegistry>(
    coreshopShippingServiceIds.shippingRuleConditionRegistry
)
conditionRegistry.register('volume', VolumeCondition)
```

## Built-in Shipping Rule Components

### Conditions (ShippingBundle)
- `weight` - Weight-based condition
- `amount` - Cart value condition
- `postcodes` - Postcode validation
- `dimension` - Package dimensions (width, height, depth)
- `shippingRule` - Reference other shipping rules
- `nested` - Nested conditions (AND/OR logic)
- `timespan` - Time-based conditions

### Conditions (Shared from CoreBundle)
- `categories` - Product categories
- `products` - Specific products
- `customers` - Specific customers
- `customerGroups` - Customer groups
- `guest` - Guest checkout
- `countries` - Destination countries
- `zones` - Shipping zones
- `stores` - Store selection
- `currencies` - Currency selection

### Actions (ShippingBundle)
- `price` - Fixed price
- `additionPercent` - Percentage surcharge
- `additionAmount` - Fixed amount surcharge
- `discountPercent` - Percentage discount
- `discountAmount` - Fixed amount discount
- `shippingRule` - Trigger another shipping rule

## Using Select Components

### Simple Select with API

For selecting entities, use the pattern with module-level caching:

```typescript
// Module-level cache
let cachedOptions: Array<{ value: number, label: string }> | null = null
let loadPromise: Promise<Array<{ value: number, label: string }>> | null = null

const loadEntities = async (): Promise<Array<{ value: number, label: string }>> => {
  if (cachedOptions) return cachedOptions
  if (loadPromise) return loadPromise

  loadPromise = (async () => {
    try {
      const entities = await entityApi.list()
      cachedOptions = entities.map(e => ({
        value: e.id!,
        label: e.name ?? `#${e.id}`
      }))
      return cachedOptions
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

export const clearCache = () => {
  cachedOptions = null
  loadPromise = null
}

// Use in component
const [options, setOptions] = React.useState<Array<{ value: number, label: string }>>([])

React.useEffect(() => {
  void loadEntities().then(setOptions)
}, [])
```

### Using Relation Components

For complex entity selection with drag-and-drop:

```typescript
import { useRelationIds } from '@coreshop/resource/src/entities/hooks/useRelationIds'

export const ProductsCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => {
  const [productIds, setProductIds] = useRelationIds(data.products ?? [])

  const handleChange = (newIds: number[]) => {
    setProductIds(newIds)
    onChange({ ...data, products: newIds })
  }

  return (
    <ProductRelationField
      value={productIds}
      onChange={handleChange}
    />
  )
}
```

## Testing Your Extension

1. **Build the bundle**: `npm run dev:single YourBundle`
2. **Clear browser cache**: Hard refresh (Ctrl+Shift+R / Cmd+Shift+R)
3. **Test the action/condition**:
   - Navigate to CoreShop → Shipping → Shipping Rules
   - Create a new rule
   - Add your custom action/condition
   - Verify the form displays correctly
   - Save and verify the data persists

## Common Patterns

### Currency Selection

```typescript
import { currencyApi } from '@coreshop/currency/src/modules/currencies/api'

const [currencies, setCurrencies] = React.useState<Array<{ value: number, label: string }>>([])

React.useEffect(() => {
  void loadCurrencies()
}, [])

const loadCurrencies = async () => {
  const list = await currencyApi.list()
  setCurrencies(list.map(c => ({
    value: c.id!,
    label: c.name ?? `#${c.id}`
  })))
}
```

### Multiple Form Fields

```typescript
<Form.Item label="Min Value" name="minValue">
  <InputNumber style={{ width: '100%' }} />
</Form.Item>

<Form.Item label="Max Value" name="maxValue">
  <InputNumber style={{ width: '100%' }} />
</Form.Item>

<Form.Item label="Unit" name="unit">
  <Select
    options={[
      { value: 'kg', label: 'Kilograms' },
      { value: 'g', label: 'Grams' }
    ]}
  />
</Form.Item>
```

### Validation Rules

```typescript
<Form.Item
  label="Required Field"
  name="requiredValue"
  rules={[
    { required: true, message: 'This field is required' },
    { type: 'number', min: 0, message: 'Must be positive' }
  ]}
>
  <InputNumber style={{ width: '100%' }} />
</Form.Item>
```

## Backend Implementation

Don't forget to implement the backend logic:

### PHP Action Processor

```php
<?php

namespace App\CoreShop\Action;

use CoreShop\Component\Shipping\Rule\Action\CarrierPriceActionProcessorInterface;

class CustomSurchargeActionProcessor implements CarrierPriceActionProcessorInterface
{
    public function getPrice(
        $carrier,
        $cartItem,
        array $configuration,
        array $context
    ): int {
        return $configuration['surcharge'] ?? 0;
    }
}
```

### Service Registration

```yaml
# config/services.yaml
App\CoreShop\Action\CustomSurchargeActionProcessor:
  tags:
    - { name: coreshop.shipping_rule.action, type: customSurcharge, form-type: App\CoreShop\Form\Type\CustomSurchargeType }
```

## Troubleshooting

### Action/Condition Not Showing
- Verify the bundle is loaded: Check browser console for errors
- Check registry registration: Ensure `container.get()` uses correct service ID
- Verify action type: The registered key must match the backend type

### Data Not Saving
- Check `buildSavePayload`: Ensure conditions/actions are included
- Verify backend processor: Check PHP service is registered
- Check browser network tab: Inspect API request payload

### Form Not Displaying
- Check data handling: Use `data` directly, not `data.configuration`
- Verify imports: Ensure all Ant Design components are imported
- Check console for React errors

## Best Practices

1. **Always use TypeScript**: Type-safe components prevent runtime errors
2. **Implement module-level caching**: Prevents duplicate API calls
3. **Use Ant Design components**: Consistent UI across CoreShop
4. **Add help text**: Guide users with `help` prop on Form.Item
5. **Validate inputs**: Use Form.Item `rules` for validation
6. **Handle loading states**: Show loading indicators for async operations
7. **Test thoroughly**: Test with different data combinations

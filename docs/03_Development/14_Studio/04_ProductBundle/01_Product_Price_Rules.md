# Extending Product Price Rules in Pimcore Studio v2

This guide explains how to extend Product Price Rules with custom Actions and Conditions in Pimcore Studio (React/TypeScript).

## Overview

Product Price Rules in CoreShop allow you to define dynamic pricing based on various conditions. The system consists of:

- **Conditions**: Define when a price rule applies (e.g., customer group, time period, product category)
- **Actions**: Define how the price is calculated (e.g., fixed price, percentage discount, amount discount)

### Product Price Rules vs Product Specific Price Rules

CoreShop has two types of product-related price rules:

| Type | Scope | Use Case |
|------|-------|----------|
| **Product Price Rules** | Global rules applied to all matching products | "10% off all electronics for VIP customers" |
| **Product Specific Price Rules** | Rules attached to individual products via Pimcore Data Object | "This specific laptop is 15% off until December" |

Both rule types share the same conditions and actions, but differ in how they're managed.

## Architecture

### Separate Registries

Each rule engine has **separate ConditionRegistry and ActionRegistry instances**:

```typescript
// ProductBundle creates its own registries
container.bind(coreshopProductServiceIds.productPriceRuleConditionRegistry)
    .to(ConditionRegistry).inSingletonScope()
container.bind(coreshopProductServiceIds.productPriceRuleActionRegistry)
    .to(ActionRegistry).inSingletonScope()

// Separate registries for Product Specific Price Rules
container.bind(coreshopProductServiceIds.productSpecificPriceRuleConditionRegistry)
    .to(ConditionRegistry).inSingletonScope()
container.bind(coreshopProductServiceIds.productSpecificPriceRuleActionRegistry)
    .to(ActionRegistry).inSingletonScope()
```

### Bundle Responsibilities

- **ProductBundle**: Registers product-specific conditions/actions (weight, nested, timespan, price actions)
- **CoreBundle**: Registers shared conditions/actions (categories, customers, countries, currencies, etc.)

### Service IDs

```typescript
// src/CoreShop/Bundle/ProductBundle/.../service-ids.ts
export const coreshopProductServiceIds = {
  productPriceRuleConditionRegistry: Symbol.for('coreshop.product.product_price_rule.condition_registry'),
  productPriceRuleActionRegistry: Symbol.for('coreshop.product.product_price_rule.action_registry'),
  productSpecificPriceRuleConditionRegistry: Symbol.for('coreshop.product.product_specific_price_rule.condition_registry'),
  productSpecificPriceRuleActionRegistry: Symbol.for('coreshop.product.product_specific_price_rule.action_registry')
}
```

## Adding a Custom Product Price Rule Action

### Step 1: Create the React Component

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/product-price-rules/actions/CustomDiscountAction.tsx

import React, { useState, useEffect } from 'react'
import { Form, InputNumber, Select } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ActionComponentProps } from '@coreshop/rule/src/rules'
import { currencyApi } from '@coreshop/currency/src/modules/currencies/api'

export const CustomDiscountAction: React.FC<ActionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const discount = data.discount || 0
  const currency = data.currency || null
  const [currencies, setCurrencies] = useState<Array<{ id: number, name: string }>>([])

  useEffect(() => {
    currencyApi.list()
      .then((response) => {
        setCurrencies(Array.isArray(response) ? response : [])
      })
      .catch(() => {
        setCurrencies([])
      })
  }, [])

  const handleDiscountChange = (value: number | null) => {
    onChange({ ...data, discount: value || 0 })
  }

  const handleCurrencyChange = (value: number) => {
    onChange({ ...data, currency: value })
  }

  return (
    <Form layout="vertical">
      <Form.Item label={t('coreshop_action_discount', { defaultValue: 'Discount' })}>
        <InputNumber
          value={discount}
          onChange={handleDiscountChange}
          min={0}
          precision={2}
          style={{ width: '100%' }}
        />
      </Form.Item>
      <Form.Item label={t('coreshop_currency', { defaultValue: 'Currency' })}>
        <Select
          value={currency}
          onChange={handleCurrencyChange}
          options={currencies.map(c => ({ label: c.name, value: c.id }))}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}
```

### Step 2: Export the Action

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/product-price-rules/actions/index.ts

export * from './CustomDiscountAction'
```

### Step 3: Register the Action

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/main.ts

import { IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import type { ActionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopProductServiceIds } from '@coreshop/product/src/modules/product-price-rules/service-ids'
import { CustomDiscountAction } from './modules/product-price-rules/actions'

const plugin: IAbstractPlugin = {
    name: 'your-bundle',

    onInit() {
        // Get the ProductPriceRule action registry
        const actionRegistry = container.get<ActionRegistry>(
            coreshopProductServiceIds.productPriceRuleActionRegistry
        )

        // Register the custom action
        actionRegistry.register('customDiscount', CustomDiscountAction)

        // Optionally register for Product Specific Price Rules too
        const specificActionRegistry = container.get<ActionRegistry>(
            coreshopProductServiceIds.productSpecificPriceRuleActionRegistry
        )
        specificActionRegistry.register('customDiscount', CustomDiscountAction)
    }
}

export default plugin
```

### Important: Data Handling

**CRITICAL**: Actions receive `data` as the configuration object directly, NOT `data.configuration`:

```typescript
// WRONG
React.useEffect(() => {
    form.setFieldsValue(data.configuration ?? {})
}, [data])

// CORRECT
React.useEffect(() => {
    form.setFieldsValue(data ?? {})
}, [data])
```

## Adding a Custom Product Price Rule Condition

### Step 1: Create the React Component

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/product-price-rules/conditions/MinOrderQuantityCondition.tsx

import React from 'react'
import { Form, InputNumber } from 'antd'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'

export const MinOrderQuantityCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const minQuantity = data.minQuantity || 1
  const maxQuantity = data.maxQuantity || 0

  const handleMinChange = (value: number | null) => {
    onChange({ ...data, minQuantity: value || 1 })
  }

  const handleMaxChange = (value: number | null) => {
    onChange({ ...data, maxQuantity: value || 0 })
  }

  return (
    <Form layout="vertical">
      <Form.Item
        label="Minimum Quantity"
        help="Minimum quantity required for this rule to apply"
      >
        <InputNumber
          value={minQuantity}
          onChange={handleMinChange}
          min={1}
          precision={0}
          style={{ width: '100%' }}
        />
      </Form.Item>
      <Form.Item
        label="Maximum Quantity"
        help="Maximum quantity (0 = no limit)"
      >
        <InputNumber
          value={maxQuantity}
          onChange={handleMaxChange}
          min={0}
          precision={0}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}
```

### Step 2: Register the Condition

```typescript
// In your main.ts
const conditionRegistry = container.get<ConditionRegistry>(
    coreshopProductServiceIds.productPriceRuleConditionRegistry
)
conditionRegistry.register('minOrderQuantity', MinOrderQuantityCondition)
```

## Built-in Product Price Rule Components

### Conditions (ProductBundle)

| Key | Component | Description |
|-----|-----------|-------------|
| `weight` | WeightCondition | Product weight range (min/max) |
| `nested` | NestedCondition | Combine conditions with AND/OR logic |
| `timespan` | TimespanCondition | Date/time range |

### Conditions (Shared from CoreBundle)

| Key | Component | Description |
|-----|-----------|-------------|
| `categories` | CategoriesCondition | Product categories |
| `products` | ProductsCondition | Specific products |
| `customers` | CustomersCondition | Specific customers |
| `customerGroups` | CustomerGroupsCondition | Customer groups |
| `guest` | GuestCondition | Guest checkout |
| `countries` | CountriesCondition | Customer countries |
| `zones` | ZonesCondition | Geographic zones |
| `stores` | StoresCondition | Store selection |
| `currencies` | CurrenciesCondition | Currency selection |
| `quantity` | QuantityCondition | Cart quantity range |
| `not_combinable_with_cart_price_voucher_rule` | NotCombinableCondition | Voucher exclusion |

### Actions (ProductBundle)

| Key | Component | Description |
|-----|-----------|-------------|
| `discountAmount` | DiscountAmountAction | Fixed amount discount |
| `discountPercent` | DiscountPercentAction | Percentage discount |
| `price` | PriceAction | Fixed price override |
| `discountPrice` | DiscountPriceAction | Discounted fixed price |
| `notDiscountableCustomAttributes` | EmptyAction | Marks products as non-discountable |

### Actions (Shared from CoreBundle)

CoreBundle also registers these shared actions:

| Key | Component | Description |
|-----|-----------|-------------|
| `discountAmount` | DiscountAmountAction | Fixed amount discount (with currency) |
| `discountPercent` | DiscountPercentAction | Percentage discount |
| `price` | PriceAction | Fixed price override |

## Using Select Components with Caching

For entity selection, use module-level caching to prevent duplicate API calls:

```typescript
// Module-level cache
let cachedCurrencies: Array<{ value: number, label: string }> | null = null
let loadPromise: Promise<Array<{ value: number, label: string }>> | null = null

const loadCurrencies = async (): Promise<Array<{ value: number, label: string }>> => {
  if (cachedCurrencies) return cachedCurrencies
  if (loadPromise) return loadPromise

  loadPromise = (async () => {
    try {
      const currencies = await currencyApi.list()
      cachedCurrencies = currencies.map(c => ({
        value: c.id!,
        label: c.name ?? `#${c.id}`
      }))
      return cachedCurrencies
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

export const clearCurrencyCache = () => {
  cachedCurrencies = null
  loadPromise = null
}
```

## Using Relation Components

For product/category selection with drag-and-drop:

```typescript
import { ProductMultiSelectField } from '@coreshop/product/src/components'
import { CategoryMultiSelectField } from '@coreshop/product/src/components'

export const ProductsCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => {
  return (
    <Form layout="vertical">
      <Form.Item label="Products">
        <ProductMultiSelectField
          value={data.products ?? []}
          onChange={(products) => onChange({ ...data, products })}
        />
      </Form.Item>
    </Form>
  )
}
```

## File Structure

```
ProductBundle/Resources/assets/pimcore-studio/src/
├── main.ts                              # Plugin entry, registry setup
├── modules/
│   ├── product-price-rules/
│   │   ├── service-ids.ts               # Registry service IDs
│   │   ├── types.ts                     # TypeScript types
│   │   ├── api.ts                       # API client
│   │   ├── ProductPriceRuleManager.tsx  # Main manager widget
│   │   ├── ProductPriceRuleFormBuilder.ts
│   │   ├── form-builder-module.ts
│   │   ├── components/
│   │   │   └── SettingsForm.tsx
│   │   ├── conditions/
│   │   │   ├── index.ts
│   │   │   └── WeightCondition.tsx
│   │   └── actions/
│   │       ├── index.ts
│   │       ├── DiscountAmountAction.tsx
│   │       ├── DiscountPercentAction.tsx
│   │       ├── DiscountPriceAction.tsx
│   │       └── PriceAction.tsx
│   └── product-specific-price-rules/
│       ├── types.ts
│       ├── ProductSpecificPriceRuleFormBuilder.ts
│       ├── form-builder-module.ts
│       ├── components/
│       │   ├── SettingsForm.tsx
│       │   └── ProductSpecificPriceRulesPanel.tsx
│       └── index.ts
├── components/
│   ├── CategoryMultiSelect.tsx
│   ├── CategoryMultiSelectField.tsx
│   ├── ProductMultiSelect.tsx
│   ├── ProductMultiSelectField.tsx
│   └── ProductUnitSelect.tsx
└── dynamic-types/
    ├── index.ts
    ├── DynamicTypeObjectDataCoreShopProductUnit.tsx
    ├── DynamicTypeObjectDataCoreShopProductUnitDefinition.tsx
    ├── DynamicTypeObjectDataCoreShopProductUnitDefinitions.tsx
    └── DynamicTypeObjectDataCoreShopProductSpecificPriceRules.tsx
```

## Backend Implementation

Don't forget to implement the backend logic:

### PHP Action Processor

```php
<?php

namespace App\CoreShop\Action;

use CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface;
use CoreShop\Component\Core\Model\ProductInterface;

class CustomDiscountActionProcessor implements ProductPriceActionProcessorInterface
{
    public function getPrice(
        ProductInterface $product,
        array $context,
        array $configuration
    ): int {
        $originalPrice = $product->getStorePrice($context['store']);
        $discount = $configuration['discount'] ?? 0;

        return max(0, $originalPrice - $discount);
    }
}
```

### Service Registration

```yaml
# config/services.yaml
App\CoreShop\Action\CustomDiscountActionProcessor:
  tags:
    - { name: coreshop.product_price_rule.action, type: customDiscount, form-type: App\Form\Type\CustomDiscountType }
```

## Testing Your Extension

1. **Build the bundle**: `npm run dev:single YourBundle`
2. **Clear browser cache**: Hard refresh (Ctrl+Shift+R / Cmd+Shift+R)
3. **Test the action/condition**:
   - Navigate to CoreShop -> Product -> Product Price Rules
   - Create a new rule
   - Add your custom action/condition
   - Verify the form displays correctly
   - Save and verify the data persists

## Troubleshooting

### Action/Condition Not Showing

- Verify the bundle is loaded: Check browser console for errors
- Check registry registration: Ensure `container.get()` uses correct service ID
- Verify action type: The registered key must match the backend type

### Data Not Saving

- Check `onChange` handler: Ensure it's called with the full data object
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
7. **Test with both rule types**: Ensure your extension works for both Product Price Rules and Product Specific Price Rules

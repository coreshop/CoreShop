# Extending Quantity Price Rules in Pimcore Studio v2

This guide explains how to extend Quantity Price Rules with custom Conditions in Pimcore Studio (React/TypeScript).

## Overview

Quantity Price Rules in CoreShop allow you to define tiered/volume-based pricing for products. Unlike other rule types, Quantity Price Rules use **ranges** instead of actions to define pricing.

The system consists of:

- **Conditions**: Define when a quantity price rule applies (e.g., customer group, time period)
- **Ranges**: Define price tiers based on quantity (e.g., "10+ units = 10% discount")

### Key Differences from Other Rule Types

| Feature | Product/Cart Price Rules | Quantity Price Rules |
|---------|-------------------------|---------------------|
| Actions | Yes (discount, price, etc.) | No - uses Ranges instead |
| Ranges | No | Yes (quantity tiers) |
| Scope | Global or per-product | Per-product (Data Object field) |
| UI | Separate manager | Inline in Data Object |

## Architecture

### Condition Registry Only

Quantity Price Rules only have a **ConditionRegistry** (no ActionRegistry):

```typescript
// ProductQuantityPriceRulesBundle creates only condition registry
container.bind(coreshopQuantityPriceRulesServiceIds.conditionRegistry)
    .to(ConditionRegistry)
    .inSingletonScope()
```

### Bundle Responsibilities

- **ProductQuantityPriceRulesBundle**: Creates the condition registry and UI components
- **CoreBundle**: Registers shared conditions (categories, customers, countries, etc.)

### Service IDs

```typescript
// src/CoreShop/Bundle/ProductQuantityPriceRulesBundle/.../service-ids.ts
export const coreshopQuantityPriceRulesServiceIds = {
  conditionRegistry: Symbol.for('coreshop.quantity_price_rules.condition_registry')
}
```

## Data Structures

### QuantityPriceRule

```typescript
interface QuantityPriceRule {
  id?: number | null
  name: string
  calculationBehaviour: CalculationBehaviour
  priority: number
  active: boolean
  conditions: RuleCondition[]
  ranges: QuantityRange[]
}
```

### QuantityRange

```typescript
interface QuantityRange {
  id?: number | null
  rangeStartingFrom: number           // Quantity threshold
  pricingBehaviour: PricingBehaviour  // How price is calculated
  unitDefinition?: number | null      // Product unit (if applicable)
  amount?: number                     // Fixed amount (for amount-based behaviours)
  percentage?: number                 // Percentage (for percent-based behaviours)
  pseudoPrice?: number                // Strike-through price
  currency?: number | null            // Currency for amount
  highlighted?: boolean               // Mark as "best value"
}
```

### Pricing Behaviours

```typescript
type PricingBehaviour =
  | 'fixed'              // Fixed price
  | 'percentage_decrease' // Reduce by percentage
  | 'percentage_increase' // Increase by percentage
  | 'amount_decrease'     // Reduce by fixed amount
  | 'amount_increase'     // Increase by fixed amount
```

### Calculation Behaviours

```typescript
type CalculationBehaviour =
  | 'volume'        // Volume-based pricing
  | 'by_quantity'   // By quantity
  | 'by_percentage' // By percentage
  | 'by_price'      // By price
```

## Adding a Custom Condition

### Step 1: Create the React Component

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/quantity-price-rules/conditions/MinCartValueCondition.tsx

import React from 'react'
import { Form, InputNumber, Select } from 'antd'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'
import { currencyApi } from '@coreshop/currency/src/modules/currencies/api'

export const MinCartValueCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const minValue = data.minValue || 0
  const currency = data.currency || null
  const [currencies, setCurrencies] = React.useState<Array<{ id: number, name: string }>>([])

  React.useEffect(() => {
    currencyApi.list()
      .then((response) => {
        setCurrencies(Array.isArray(response) ? response : [])
      })
      .catch(() => setCurrencies([]))
  }, [])

  return (
    <Form layout="vertical">
      <Form.Item label="Minimum Cart Value">
        <InputNumber
          value={minValue}
          onChange={(value) => onChange({ ...data, minValue: value || 0 })}
          min={0}
          precision={2}
          style={{ width: '100%' }}
        />
      </Form.Item>
      <Form.Item label="Currency">
        <Select
          value={currency}
          onChange={(value) => onChange({ ...data, currency: value })}
          options={currencies.map(c => ({ label: c.name, value: c.id }))}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}
```

### Step 2: Register the Condition

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/main.ts

import { IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import type { ConditionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopQuantityPriceRulesServiceIds } from '@coreshop/productquantitypricerules/src/modules/quantity-price-rules/service-ids'
import { MinCartValueCondition } from './modules/quantity-price-rules/conditions'

const plugin: IAbstractPlugin = {
    name: 'your-bundle',

    onInit() {
        // Get the Quantity Price Rule condition registry
        const conditionRegistry = container.get<ConditionRegistry>(
            coreshopQuantityPriceRulesServiceIds.conditionRegistry
        )

        // Register the custom condition
        conditionRegistry.register('minCartValue', MinCartValueCondition)
    }
}

export default plugin
```

## Built-in Conditions

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
| `nested` | NestedCondition | Combine conditions with AND/OR |
| `timespan` | TimespanCondition | Date/time range |

## UI Components

### QuantityPriceRulePanel

Main panel for editing a single quantity price rule with three tabs:

1. **Settings**: Name, calculation behaviour, priority, active status
2. **Conditions**: Rule conditions using shared ConditionsPanel
3. **Ranges**: Quantity-based price tiers

### RangesPanel

Editable grid for defining quantity ranges:

- **Starting From**: Minimum quantity for this tier
- **Calculation Behaviour**: How price is modified
- **Unit**: Product unit (if product has unit definitions)
- **Highlight**: Mark as "best value" tier
- **Amount**: Fixed amount (for amount-based behaviours)
- **Currency**: Currency for amount
- **Percentage**: Percentage value (for percent-based behaviours)
- **Pseudo Price**: Strike-through/original price display

Features:
- Add/remove ranges
- Reorder ranges (move up/down)
- Copy/paste ranges between rules
- Conditional field editing based on pricing behaviour

## File Structure

```
ProductQuantityPriceRulesBundle/Resources/assets/pimcore-studio/src/
├── main.ts                           # Plugin entry, registry setup
├── modules/
│   ├── icon-library/
│   │   └── index.ts
│   └── quantity-price-rules/
│       ├── service-ids.ts            # Registry service ID
│       ├── types.ts                  # TypeScript types
│       ├── index.ts                  # Module exports
│       └── components/
│           ├── index.ts
│           ├── QuantityPriceRulePanel.tsx      # Single rule editor
│           ├── ProductQuantityPriceRulesPanel.tsx  # Multi-rule manager
│           └── RangesPanel.tsx                 # Ranges grid editor
└── dynamic-types/
    ├── index.ts
    └── DynamicTypeObjectDataCoreShopProductQuantityPriceRules.tsx
```

## Dynamic Type Integration

Quantity Price Rules are attached to products via a Pimcore Data Object field:

```typescript
// DynamicTypeObjectDataCoreShopProductQuantityPriceRules.tsx
export class DynamicTypeObjectDataCoreShopProductQuantityPriceRules
  extends DynamicTypeObjectDataAbstract {
  readonly id = 'coreShopProductQuantityPriceRules'
  // Renders ProductQuantityPriceRulesPanel in Data Object editor
}
```

## Backend Implementation

### PHP Condition Checker

```php
<?php

namespace App\CoreShop\Condition;

use CoreShop\Component\ProductQuantityPriceRules\Rule\Condition\QuantityRangeConditionCheckerInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;

class MinCartValueConditionChecker implements QuantityRangeConditionCheckerInterface
{
    public function isValid(
        ProductQuantityPriceRuleInterface $rule,
        array $context,
        array $configuration
    ): bool {
        $cartValue = $context['cart']?->getTotal() ?? 0;
        $minValue = $configuration['minValue'] ?? 0;
        $currency = $configuration['currency'] ?? null;

        // Check if cart value meets minimum
        return $cartValue >= $minValue;
    }
}
```

### Service Registration

```yaml
# config/services.yaml
App\CoreShop\Condition\MinCartValueConditionChecker:
  tags:
    - { name: coreshop.product_quantity_price_rules.condition, type: minCartValue, form-type: App\Form\Type\MinCartValueConditionType }
```

## How Ranges Work

### Pricing Behaviour Logic

```typescript
// Amount-based behaviours need: amount, currency, pseudoPrice
const AMOUNT_BASED_BEHAVIOURS = ['fixed', 'amount_decrease', 'amount_increase']

// Percent-based behaviours need: percentage only
const PERCENT_BASED_BEHAVIOURS = ['percentage_decrease', 'percentage_increase']
```

When changing pricing behaviour:
- Switching to percent-based: Resets amount, pseudoPrice, currency to 0/null
- Switching to amount-based: Resets percentage to 0

### Example Range Configuration

| Quantity | Behaviour | Amount | Percentage | Result |
|----------|-----------|--------|------------|--------|
| 1+ | fixed | 100 | - | Fixed price: $100 |
| 10+ | percentage_decrease | - | 10% | 10% off base price |
| 50+ | percentage_decrease | - | 20% | 20% off base price |
| 100+ | amount_decrease | 50 | - | $50 off base price |

## Testing Your Extension

1. **Build the bundle**: `npm run dev:single YourBundle`
2. **Clear browser cache**: Hard refresh (Ctrl+Shift+R)
3. **Test the condition**:
   - Open a Product Data Object
   - Find the Quantity Price Rules field
   - Create a new rule
   - Add your custom condition
   - Verify the form displays correctly
   - Save the Data Object and verify persistence

## Best Practices

1. **Conditions Only**: Remember Quantity Price Rules don't have actions - pricing is defined via ranges
2. **Unit Awareness**: If products have unit definitions, consider them in your conditions
3. **Currency Handling**: For amount-based conditions, always include currency selection
4. **Range Validation**: Ensure ranges don't overlap in unexpected ways
5. **Performance**: Cache API calls for entity selects (currencies, stores, etc.)

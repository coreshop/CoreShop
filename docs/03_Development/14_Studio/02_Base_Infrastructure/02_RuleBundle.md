# RuleBundle

The RuleBundle provides the infrastructure for all rule-based features in CoreShop (Cart Price Rules, Product Price Rules, Shipping Rules, etc.). It includes the RuleApi, Registry system, and reusable UI components.

## RuleApi Base Class

The `RuleApi` class extends `EntityApi` and adds rule-specific functionality.

### Basic Usage

```typescript
import { RuleApi } from '@coreshop/rule/src/rules'
import type { CartPriceRule } from './types'

export class CartPriceRuleApi extends RuleApi<CartPriceRule> {}

export const cartPriceRuleApi = new CartPriceRuleApi({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/cart_price_rules'
})
```

### Additional Methods

In addition to all EntityApi methods (list, get, add, save, delete), RuleApi provides:

#### `getConfig()` - Get Rule Configuration

Fetches available conditions and actions for the rule type:

```typescript
const config = await cartPriceRuleApi.getConfig()
// Returns: { conditions: string[], actions: string[] }
```

**Response Format:**
```typescript
interface RuleConfig {
  conditions: string[]  // Available condition types
  actions: string[]     // Available action types
}
```

**Example:**
```typescript
const config = await cartPriceRuleApi.getConfig()
console.log(config.conditions) // ['countries', 'products', 'customers', ...]
console.log(config.actions)    // ['discount', 'freeShipping', ...]
```

## Registry Pattern

The Registry Pattern allows bundles to register their rule conditions and actions dynamically.

### ConditionRegistry

```typescript
import { ConditionRegistry } from '@coreshop/rule/src/rules/registry'
import { container } from '@pimcore/studio-ui-bundle'

// Bind registry as singleton
container.bind(serviceIds.conditionRegistry)
  .to(ConditionRegistry)
  .inSingletonScope()

// Get registry
const conditionRegistry = container.get<ConditionRegistry>(
  serviceIds.conditionRegistry
)

// Register a condition
conditionRegistry.register('countries', CountriesCondition)

// Get a condition component
const Component = conditionRegistry.get('countries')

// Check if condition exists
if (conditionRegistry.has('countries')) {
  // ...
}
```

### ActionRegistry

```typescript
import { ActionRegistry } from '@coreshop/rule/src/rules/registry'

// Bind registry as singleton
container.bind(serviceIds.actionRegistry)
  .to(ActionRegistry)
  .inSingletonScope()

// Get registry
const actionRegistry = container.get<ActionRegistry>(
  serviceIds.actionRegistry
)

// Register an action
actionRegistry.register('discount', DiscountAction)
```

### Multiple Registries

**Important:** Each rule type has its own registries!

```typescript
// Product Price Rules
const productConditionRegistry = container.get<ConditionRegistry>(
  coreshopProductServiceIds.productPriceRuleConditionRegistry
)
const productActionRegistry = container.get<ActionRegistry>(
  coreshopProductServiceIds.productPriceRuleActionRegistry
)

// Cart Price Rules
const cartConditionRegistry = container.get<ConditionRegistry>(
  coreshopOrderServiceIds.cartPriceRuleConditionRegistry
)
const cartActionRegistry = container.get<ActionRegistry>(
  coreshopOrderServiceIds.cartPriceRuleActionRegistry
)
```

## Registration in Bundles

### ProductBundle Example

```typescript
// src/CoreShop/Bundle/ProductBundle/Resources/assets/pimcore-studio/src/main.ts

import { ConditionRegistry, ActionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopProductServiceIds } from './modules/product-price-rules/service-ids'
import { WeightCondition } from './modules/product-price-rules/conditions'
import { DiscountAction } from './modules/product-price-rules/actions'

const plugin: IAbstractPlugin = {
    name: 'coreshop-product',

    onInit() {
        // Bind registries
        container.bind(coreshopProductServiceIds.productPriceRuleConditionRegistry)
          .to(ConditionRegistry)
          .inSingletonScope()

        container.bind(coreshopProductServiceIds.productPriceRuleActionRegistry)
          .to(ActionRegistry)
          .inSingletonScope()

        // Get registries
        const conditionRegistry = container.get<ConditionRegistry>(
          coreshopProductServiceIds.productPriceRuleConditionRegistry
        )
        const actionRegistry = container.get<ActionRegistry>(
          coreshopProductServiceIds.productPriceRuleActionRegistry
        )

        // Register components
        conditionRegistry.register('weight', WeightCondition)
        actionRegistry.register('discount', DiscountAction)
    }
}
```

### CoreBundle as Glue Layer

CoreBundle registers shared components into multiple registries:

```typescript
// src/CoreShop/Bundle/CoreBundle/Resources/assets/pimcore-studio/src/main.ts

const plugin: IAbstractPlugin = {
    onInit() {
        // Get ProductPriceRule registries
        const productConditionRegistry = container.get<ConditionRegistry>(
          coreshopProductServiceIds.productPriceRuleConditionRegistry
        )

        // Get CartPriceRule registries
        const cartConditionRegistry = container.get<ConditionRegistry>(
          coreshopOrderServiceIds.cartPriceRuleConditionRegistry
        )

        // Register shared conditions in BOTH registries
        productConditionRegistry.register('countries', CountriesCondition)
        cartConditionRegistry.register('countries', CountriesCondition)
    }
}
```

## Rule Components

### RuleManager

Main component for managing rules (list + detail view):

```typescript
import { RuleManager } from '@coreshop/rule/src/rules/components'

export const CartPriceRuleManager = () => (
  <RuleManager
    api={cartPriceRuleApi}
    conditionRegistryId={serviceIds.cartPriceRuleConditionRegistry}
    actionRegistryId={serviceIds.cartPriceRuleActionRegistry}
    settingsComponent={<SettingsForm />}
  />
)
```

### RuleForm

Form component with conditions and actions panels:

```typescript
import { RuleForm } from '@coreshop/rule/src/rules/components'

<RuleForm
  rule={data}
  config={config}
  conditionRegistryId={serviceIds.conditionRegistry}
  actionRegistryId={serviceIds.actionRegistry}
  settingsComponent={<SettingsForm />}
  onChange={setData}
/>
```

### ConditionsPanel

Panel for managing rule conditions:

```typescript
import { ConditionsPanel } from '@coreshop/rule/src/rules/components'

<ConditionsPanel
  conditions={rule.conditions}
  onChange={handleConditionsChange}
  registryId={serviceIds.conditionRegistry}
/>
```

### ActionsPanel

Panel for managing rule actions:

```typescript
import { ActionsPanel } from '@coreshop/rule/src/rules/components'

<ActionsPanel
  actions={rule.actions}
  onChange={handleActionsChange}
  registryId={serviceIds.actionRegistry}
/>
```

## EmptyAction / EmptyCondition

For rules without configuration UI:

```typescript
import { EmptyAction, EmptyCondition } from '@coreshop/rule/src/rules'

// Register action without config
actionRegistry.register('notDiscountableCustomAttributes', EmptyAction)

// Register condition without config
conditionRegistry.register('alwaysTrue', EmptyCondition)
```

These components display an info message instead of a form.

## Component Props

### ActionComponentProps

```typescript
interface ActionComponentProps {
  data: any                      // Action configuration data
  onChange: (data: any) => void  // Update handler
}
```

### ConditionComponentProps

```typescript
interface ConditionComponentProps {
  data: any                      // Condition configuration data
  onChange: (data: any) => void  // Update handler
}
```

## Complete Example

### 1. Service IDs

```typescript
// service-ids.ts
export const coreshopProductServiceIds = {
  productPriceRuleConditionRegistry: Symbol.for('ProductPriceRuleConditionRegistry'),
  productPriceRuleActionRegistry: Symbol.for('ProductPriceRuleActionRegistry')
}
```

### 2. Create Action Component

```typescript
// DiscountAction.tsx
import React from 'react'
import { Form, InputNumber } from 'antd'
import type { ActionComponentProps } from '@coreshop/rule/src/rules'

export const DiscountAction: React.FC<ActionComponentProps> = ({ data, onChange }) => {
  return (
    <Form layout="vertical">
      <Form.Item label="Discount Amount">
        <InputNumber
          value={data.amount || 0}
          onChange={(value) => onChange({ ...data, amount: value })}
        />
      </Form.Item>
    </Form>
  )
}
```

### 3. Register in Plugin

```typescript
// main.ts
import { ActionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopProductServiceIds } from './service-ids'
import { DiscountAction } from './actions/DiscountAction'

const plugin: IAbstractPlugin = {
    onInit() {
        container.bind(coreshopProductServiceIds.productPriceRuleActionRegistry)
          .to(ActionRegistry)
          .inSingletonScope()

        const actionRegistry = container.get<ActionRegistry>(
          coreshopProductServiceIds.productPriceRuleActionRegistry
        )

        actionRegistry.register('discount', DiscountAction)
    }
}
```

### 4. Use in RuleManager

```typescript
// ProductPriceRuleManager.tsx
import { RuleManager } from '@coreshop/rule/src/rules/components'
import { productPriceRuleApi } from './api'
import { coreshopProductServiceIds } from './service-ids'

export const ProductPriceRuleManager = () => (
  <RuleManager
    api={productPriceRuleApi}
    conditionRegistryId={coreshopProductServiceIds.productPriceRuleConditionRegistry}
    actionRegistryId={coreshopProductServiceIds.productPriceRuleActionRegistry}
  />
)
```

## Best Practices

### 1. Separate Registries Per Rule Type

**⚠️ Each rule type MUST have its own registries!**

```typescript
// ✅ GOOD - Separate registries
const productConditionRegistry = container.get(productServiceIds.conditionRegistry)
const cartConditionRegistry = container.get(cartServiceIds.conditionRegistry)

// ❌ BAD - Sharing registries
const sharedRegistry = container.get(serviceIds.conditionRegistry)
```

### 2. Registry IDs are Mandatory

Always pass `registryId` to rule components:

```typescript
// ✅ GOOD
<RuleForm
  conditionRegistryId={serviceIds.conditionRegistry}
  actionRegistryId={serviceIds.actionRegistry}
/>

// ❌ BAD - Missing registryId (will not work)
<RuleForm rule={data} onChange={setData} />
```

### 3. Bind Registries in onInit

Bind registries during plugin initialization:

```typescript
onInit() {
  // ✅ GOOD - Bind in onInit
  container.bind(serviceIds.registry).to(ConditionRegistry).inSingletonScope()
}

onStartup() {
  // ❌ BAD - Too late, components may need it earlier
  container.bind(serviceIds.registry).to(ConditionRegistry).inSingletonScope()
}
```

### 4. Type Your Components

Use proper TypeScript types:

```typescript
// ✅ GOOD
export const MyCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => {
  const conditionData = data as MyConditionData
  // ...
}

// ❌ BAD - No types
export const MyCondition = ({ data, onChange }) => {
  // ...
}
```

## Real-World Examples

See the [Extending Rule Actions](../../01_Extending_Guide/04_Extending_Rule_Actions.md) and [Extending Rule Conditions](../../01_Extending_Guide/05_Extending_Rule_Conditions.md) guides for complete examples.

## Next Steps

- [Building CRUD Features](03_Entity_CRUD.md) - Create complete CRUD interfaces
- [Extending Rule Actions](../../01_Extending_Guide/04_Extending_Rule_Actions.md) - Create custom actions
- [Extending Rule Conditions](../../01_Extending_Guide/05_Extending_Rule_Conditions.md) - Create custom conditions

# RuleBundle

The RuleBundle provides the infrastructure for all rule-based features in CoreShop (Cart Price Rules, Product Price Rules, Shipping Rules, etc.). It includes the RuleApi, Registry system, and reusable UI components.

## Schema-Driven Rule Forms (Default)

**Most rule conditions and actions no longer require custom JavaScript/React code.** The StudioFormBundle automatically renders configuration forms from PHP FormTypes. When you register a condition or action with a `form-type` attribute in its service tag, the frontend auto-generates the React component from the backend schema.

This means:

- **Adding a new condition/action** = PHP FormType + service tag. No React code needed.
- **The frontend calls `registerSchemaComponentsFromConfig()`** which creates React components dynamically from the `conditionSchemaByType` / `actionSchemaByType` maps returned by the `get-config` endpoint.
- **Only special components** that need custom UI behavior (e.g., `NestedCondition`, `TimespanCondition`) still require hand-written React components.

For the full pattern with examples, see [StudioFormBundle Examples — Rule Engine Integration](04_StudioFormBundle.md#rule-form-integrations) and [Example 13](05_StudioFormBundle_Examples.md#example-13--rule-conditionaction-as-schema-form).

### How It Works

1. PHP FormType defines the condition/action configuration fields
2. Service tag includes the `form-type` attribute pointing to the FormType class
3. A `RegisterFormTypesFromTagsPass` compiler pass collects the form types and registers them in the block prefix registry
4. The `get-config` endpoint returns `conditionSchemaByType` / `actionSchemaByType` maps plus embedded `schemas`
5. The frontend calls `registerSchemaComponentsFromConfig()` which uses `createSchemaCondition()` / `createSchemaAction()` to generate React wrappers around `SchemaForm`
6. `preSeedSchemaCache()` is called automatically inside `RuleApi.getConfig()` with the embedded schemas

### What Bundles Still Do in JS

Each bundle's `main.ts` still:

1. **Creates and binds registries** (ConditionRegistry, ActionRegistry)
2. **Registers special components** that cannot be schema-driven (nested, timespan, etc.)
3. **Hides rule collection block prefixes** in the widget registry (so they don't render inside generic entity forms)

Schema-based components are registered at runtime when `registerSchemaComponentsFromConfig()` is called, not at plugin init time.

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

Fetches available conditions, actions, and their schema mappings. **Automatically calls `preSeedSchemaCache()`** with the embedded schemas.

```typescript
const config = await cartPriceRuleApi.getConfig()
```

**Response Format:**
```typescript
interface RuleConfig {
  conditions: string[]                           // Available condition type keys
  actions: string[]                              // Available action type keys
  conditionSchemaByType: Record<string, string>  // type -> block prefix mapping
  actionSchemaByType: Record<string, string>     // type -> block prefix mapping
  schemas: Record<string, FormSchema>            // Embedded schemas (auto-seeded)
}
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

// Register a hand-written condition (only for special components)
conditionRegistry.register('nested', NestedCondition)

// Schema-based conditions are registered automatically via registerSchemaComponentsFromConfig()
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

// Schema-based actions are registered automatically via registerSchemaComponentsFromConfig()
```

### Multiple Registries

**Important:** Each rule type has its own registries!

```typescript
// Product Price Rules
const productConditionRegistry = container.get<ConditionRegistry>(
  coreshopProductServiceIds.productPriceRuleConditionRegistry
)

// Cart Price Rules
const cartConditionRegistry = container.get<ConditionRegistry>(
  coreshopOrderServiceIds.cartPriceRuleConditionRegistry
)
```

## Schema Registration Functions

### `registerSchemaComponentsFromConfig()`

Auto-registers schema-driven components from a `RuleConfig` response:

```typescript
import { registerSchemaComponentsFromConfig } from '@coreshop/rule/src/rules/registry'

const config = await ruleApi.getConfig()
registerSchemaComponentsFromConfig(conditionRegistry, actionRegistry, config)
```

This iterates over `conditionSchemaByType` and `actionSchemaByType`, creating `SchemaCondition` / `SchemaAction` wrappers. It **does not overwrite** hand-written components already registered in the registry.

### `registerSchemaComponentsFromMaps()`

Lower-level function if you need direct control:

```typescript
import { registerSchemaComponentsFromMaps } from '@coreshop/rule/src/rules/registry'

registerSchemaComponentsFromMaps(
  conditionRegistry,
  actionRegistry,
  conditionSchemaByType,
  actionSchemaByType,
  schemas,
  { overwriteExisting: false }
)
```

### `createSchemaCondition()` / `createSchemaAction()`

Factory functions that create a React component wrapping `SchemaForm`:

```typescript
import { createSchemaCondition, createSchemaAction } from '@coreshop/rule/src/rules/components'

// Manual registration (usually not needed — registerSchemaComponentsFromConfig does this)
conditionRegistry.register('amount', createSchemaCondition('coreshop_cart_price_rule_condition_amount'))
actionRegistry.register('surchargePercent', createSchemaAction('coreshop_cart_price_rule_action_surcharge_percent'))
```

## Rule Components

### RuleManager

A simple list+detail component built on `EntitySplitManager`. Loads config automatically.

```typescript
interface RuleManagerProps<T extends Rule> {
  api: RuleApi<T>
  renderForm: (rule: T, config: RuleConfig, onSave: (rule: T) => Promise<void>, onChange: (rule: T) => void) => React.ReactNode
  createEmptyRule: () => T
}
```

**Note:** `RuleManager` does **not** auto-register schema components. You must call `registerSchemaComponentsFromConfig()` yourself in the `renderForm` callback or in a `useEffect`.

### Recommended Pattern: EntityTabbedManager + RuleForm

In practice, most rule managers use `EntityTabbedManager` from ResourceBundle (for tabbed detail views with dirty tracking) rather than `RuleManager`. Here's the actual pattern used:

```typescript
import { EntityTabbedManager } from '@coreshop/resource'
import { RuleForm, registerSchemaComponentsFromConfig } from '@coreshop/rule/src/rules'
import type { RuleConfig } from '@coreshop/rule/src/rules'

export const ShippingRuleManager: React.FC = () => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const modal = useFormModal()
  const [config, setConfig] = React.useState<RuleConfig>({ conditions: [], actions: [] })

  // Load config and register schema components on mount
  React.useEffect(() => {
    shippingRuleApi.getConfig()
      .then((cfg) => {
        const conditionRegistry = container.get<ConditionRegistry>(
          coreshopShippingServiceIds.shippingRuleConditionRegistry
        )
        const actionRegistry = container.get<ActionRegistry>(
          coreshopShippingServiceIds.shippingRuleActionRegistry
        )
        registerSchemaComponentsFromConfig(conditionRegistry, actionRegistry, cfg)
        setConfig(cfg)
      })
      .catch(err => {
        void messageApi.error(getErrorMessage(err, 'Failed to load config'))
      })
  }, [])

  return (
    <EntityTabbedManager<ShippingRuleDetail>
      api={shippingRuleApi}
      dragType='coreshop:shipping_rule'
      leftRootTitle={t('coreshop_carriers_shipping_rule')}
      getTitle={(li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}`}
      buildSavePayload={(data) => data}
      onAdd={async () => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop_carriers_shipping_rule'),
          label: t('coreshop_name'),
          rule: { required: true, message: t('coreshop_name_required') },
          onOk: async (nameValue: string) => {
            const res = await shippingRuleApi.add({ name: nameValue })
            resolve(res.data.id!)
          }
        })
      })}
      renderDetail={(data, setData, ctx) => {
        if (!data) return <div>Select a shipping rule...</div>

        return (
          <RuleForm
            rule={data}
            config={config}
            conditionRegistryId={coreshopShippingServiceIds.shippingRuleConditionRegistry}
            actionRegistryId={coreshopShippingServiceIds.shippingRuleActionRegistry}
            currentLocale={ctx?.currentLocale ?? 'en'}
            locales={ctx?.locales}
            settingsComponent={
              <SettingsForm
                rule={data}
                onChange={setData}
                currentLocale={ctx?.currentLocale ?? 'en'}
                locales={ctx?.locales}
              />
            }
            onChange={setData}
            hideToolbar={true}
          />
        )
      }}
    />
  )
}
```

### RuleForm

Form component with Settings, Conditions, and Actions tabs:

```typescript
interface RuleFormProps {
  rule: Rule
  config: RuleConfig
  settingsComponent: React.ReactNode
  conditionRegistryId: symbol | string
  actionRegistryId: symbol | string
  currentLocale?: string
  locales?: string[]
  additionalTabs?: RuleFormTab[]
  onSave?: (rule: Rule) => Promise<void>
  onChange: (rule: Rule) => void
  hideToolbar?: boolean        // Hide save button (when managed by EntityTabbedManager)
}

// Additional tabs (e.g., Voucher Codes for Cart Price Rules)
interface RuleFormTab {
  key: string
  label: string
  component: React.ReactNode
  disabled?: boolean
}
```

### ConditionsPanel / ActionsPanel

```typescript
import { ConditionsPanel, ActionsPanel } from '@coreshop/rule/src/rules/components'

<ConditionsPanel
  conditions={rule.conditions}
  availableTypes={config.conditions}
  onChange={handleConditionsChange}
  registryId={serviceIds.conditionRegistry}
  currentLocale={currentLocale}
  locales={locales}
/>

<ActionsPanel
  actions={rule.actions}
  availableTypes={config.actions}
  onChange={handleActionsChange}
  registryId={serviceIds.actionRegistry}
  currentLocale={currentLocale}
  locales={locales}
/>
```

## Components That Still Need Custom JS

Only a few special components still require hand-written React code:

| Component | Reason |
|-----------|--------|
| `NestedCondition` | Recursively renders sub-conditions with AND/OR logic; cannot be expressed as a flat form |
| `TimespanCondition` | Uses custom date/time picker composition |
| `CartItemAction` | Renders nested cart-item condition/action panels |

All other conditions and actions (countries, stores, currencies, categories, products, customers, amount, weight, etc.) are rendered automatically from their PHP FormTypes.

## Adding a New Condition/Action (Preferred Approach)

### Step 1: Create the PHP FormType

```php
<?php

namespace App\Form\Type\Rule\Condition;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class MinimumWeightConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minWeight', IntegerType::class, [
                'label' => 'app_condition_min_weight',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_shipping_rule_condition_minimum_weight';
    }
}
```

### Step 2: Register the Service with `form-type`

```yaml
services:
    app.shipping_rule.condition.minimum_weight:
        class: App\Rule\Condition\MinimumWeightChecker
        tags:
            - name: coreshop.shipping_rule.condition
              type: minimumWeight
              form-type: App\Form\Type\Rule\Condition\MinimumWeightConfigurationType
```

### Step 3: Done

No React/TypeScript code needed. The condition form is rendered automatically in Studio.

### When You Need Custom JS (Rare)

If your condition/action requires custom UI behavior that cannot be expressed as a Symfony FormType (e.g., recursive nesting, complex interactive widgets), you can still register a hand-written React component:

```typescript
// In your bundle's main.ts
const conditionRegistry = container.get<ConditionRegistry>(serviceIds.conditionRegistry)
conditionRegistry.register('myCustomCondition', MyCustomCondition)
```

Hand-written components take priority over schema-generated ones — `registerSchemaComponentsFromConfig()` does not overwrite existing entries.

## Best Practices

### 1. Prefer Schema-Driven Components

Use PHP FormTypes instead of writing React components. This:
- Eliminates frontend/backend duplication
- Automatically supports all StudioFormBundle widgets (choices, autocomplete, collections, etc.)
- Requires zero JS build steps for new conditions/actions

### 2. Separate Registries Per Rule Type

**Each rule type MUST have its own registries!**

```typescript
// Separate registries
const productConditionRegistry = container.get(productServiceIds.conditionRegistry)
const cartConditionRegistry = container.get(cartServiceIds.conditionRegistry)
```

### 3. Bind Registries in onInit

```typescript
onInit() {
  container.bind(serviceIds.registry).to(ConditionRegistry).inSingletonScope()
}
```

### 4. Schema Cache is Automatic

`RuleApi.getConfig()` automatically calls `preSeedSchemaCache()` with the embedded schemas. No manual call needed.

## Real-World Examples

- [StudioFormBundle Examples — Example 13](05_StudioFormBundle_Examples.md#example-13--rule-conditionaction-as-schema-form) — Full end-to-end schema-driven rule component
- [StudioFormBundle Examples — Example 14](05_StudioFormBundle_Examples.md#example-14--cross-bundle-extension-formtypeextension) — Cross-bundle form extension
- [Extending Shipping Rules](../01_Extending_Shipping_Rules.md) — Shipping rule extensions

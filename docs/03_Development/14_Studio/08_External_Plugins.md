# External Plugins for CoreShop Studio v2

This guide explains how to create external plugins (Composer packages) that can extend CoreShop's Pimcore Studio v2 functionality.

## Overview

External plugins can:
- Access CoreShop's registries (ConditionRegistry, ActionRegistry)
- Use service IDs for container bindings
- Import and use types and interfaces
- Reuse components like Selects

## Setup

### 1. Create your plugin structure

```
my-coreshop-plugin/
├── src/
│   └── Resources/
│       └── assets/
│           └── pimcore-studio/
│               ├── src/
│               │   ├── main.ts           # Plugin entry point
│               │   └── conditions/       # Your custom conditions
│               │       └── MyCondition.tsx
│               ├── package.json
│               ├── tsconfig.json
│               └── rsbuild.config.ts
└── composer.json
```

### 2. Configure package.json

```json
{
  "name": "@my-company/coreshop-plugin",
  "version": "1.0.0",
  "main": "src/main.ts",
  "scripts": {
    "build": "rsbuild build",
    "dev": "rsbuild dev"
  },
  "dependencies": {
    "@pimcore/studio-ui-bundle": "1.0.0-canary.20251119-143005-1b35b01",
    "antd": "^5.12.0",
    "react": "18.3.x",
    "react-dom": "18.3.x"
  },
  "devDependencies": {
    "@rsbuild/core": "^1.0.0",
    "@rsbuild/plugin-react": "^1.0.0",
    "@module-federation/rsbuild-plugin": "^0.8.0",
    "typescript": "^5.3.0"
  }
}
```

### 3. Configure rsbuild.config.ts

```typescript
import { defineConfig } from '@rsbuild/core'
import { pluginReact } from '@rsbuild/plugin-react'
import { pluginModuleFederation } from '@module-federation/rsbuild-plugin'
import path from 'path'

// Resolve CoreShop vendor path - adjust based on your project structure
const coreshopPath = path.resolve(__dirname, '../../../../vendor/coreshop/core-shop/src/CoreShop/Bundle')

export default defineConfig({
  resolve: {
    alias: {
      // CoreShop bundle aliases
      '@coreshop/rule': path.join(coreshopPath, 'RuleBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/rule/src': path.join(coreshopPath, 'RuleBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/resource': path.join(coreshopPath, 'ResourceBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/resource/src': path.join(coreshopPath, 'ResourceBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/order': path.join(coreshopPath, 'OrderBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/order/src': path.join(coreshopPath, 'OrderBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/product': path.join(coreshopPath, 'ProductBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/product/src': path.join(coreshopPath, 'ProductBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/shipping': path.join(coreshopPath, 'ShippingBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/shipping/src': path.join(coreshopPath, 'ShippingBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/address': path.join(coreshopPath, 'AddressBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/address/src': path.join(coreshopPath, 'AddressBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/currency': path.join(coreshopPath, 'CurrencyBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/currency/src': path.join(coreshopPath, 'CurrencyBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/taxation': path.join(coreshopPath, 'TaxationBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/taxation/src': path.join(coreshopPath, 'TaxationBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/store': path.join(coreshopPath, 'StoreBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/store/src': path.join(coreshopPath, 'StoreBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/customer': path.join(coreshopPath, 'CustomerBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/customer/src': path.join(coreshopPath, 'CustomerBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/payment': path.join(coreshopPath, 'PaymentBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/payment/src': path.join(coreshopPath, 'PaymentBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/core': path.join(coreshopPath, 'CoreBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/core/src': path.join(coreshopPath, 'CoreBundle/Resources/assets/pimcore-studio/src'),
    }
  },
  plugins: [
    pluginReact(),
    pluginModuleFederation({
      name: 'my_coreshop_plugin',
      filename: 'static/js/remoteEntry.js',
      exposes: {
        '.': './src/main.ts'
      },
      shared: {
        // CoreShop bundles must be shared as singletons
        '@coreshop/rule': { singleton: true, eager: false },
        '@coreshop/resource': { singleton: true, eager: false },
        '@coreshop/order': { singleton: true, eager: false },
        '@coreshop/product': { singleton: true, eager: false },
        '@coreshop/shipping': { singleton: true, eager: false },
        '@coreshop/address': { singleton: true, eager: false },
        '@coreshop/currency': { singleton: true, eager: false },
        '@coreshop/taxation': { singleton: true, eager: false },
        '@coreshop/store': { singleton: true, eager: false },
        '@coreshop/customer': { singleton: true, eager: false },
        '@coreshop/payment': { singleton: true, eager: false },
        '@coreshop/core': { singleton: true, eager: false },
        // Standard shared modules
        react: { singleton: true, eager: false },
        'react-dom': { singleton: true, eager: false },
        'react/jsx-runtime': { singleton: true, eager: false },
        antd: { singleton: true, eager: false },
      },
      remotes: {
        '@pimcore/studio-ui-bundle': `promise new Promise(resolve => {
          const studioUIBundleRemoteUrl = window.StudioUIBundleRemoteUrl
          const script = document.createElement('script')
          script.src = studioUIBundleRemoteUrl
          script.onload = () => {
            resolve({
              get: (request) => window['pimcore_studio_ui_bundle'].get(request),
              init: (...arg) => {
                try {
                  return window['pimcore_studio_ui_bundle'].init(...arg)
                } catch(e) {
                  console.log('remote container already initialized')
                }
              }
            })
          }
          document.head.appendChild(script);
        })`
      }
    })
  ]
})
```

## Usage Examples

### Registering a Custom Condition for Cart Price Rules

```typescript
// src/main.ts
import { container, type IAbstractPlugin } from '@pimcore/studio-ui-bundle'
import type { ConditionRegistry } from '@coreshop/rule'
import { coreshopOrderServiceIds } from '@coreshop/order'
import { MyCustomCondition } from './conditions/MyCustomCondition'

const plugin: IAbstractPlugin = {
  name: 'my-coreshop-plugin',

  onInit() {
    // Get the Cart Price Rule condition registry
    const conditionRegistry = container.get<ConditionRegistry>(
      coreshopOrderServiceIds.cartPriceRuleConditionRegistry
    )

    // Register your custom condition
    conditionRegistry.register('myCustomCondition', MyCustomCondition)
  },

  onStartup({ moduleSystem }) {
    // Register modules if needed
  }
}

export { plugin }
export default plugin
```

### Creating a Custom Condition Component

```typescript
// src/conditions/MyCustomCondition.tsx
import React from 'react'
import { Form, InputNumber } from 'antd'
import type { ConditionComponentProps } from '@coreshop/rule'
import { useTranslation } from 'react-i18next'

interface MyConditionConfig {
  minValue: number
  maxValue: number
}

export const MyCustomCondition: React.FC<ConditionComponentProps<MyConditionConfig>> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()

  return (
    <>
      <Form.Item label={t('my_plugin.min_value')}>
        <InputNumber
          value={config?.minValue ?? 0}
          onChange={(value) => onChange({ ...config, minValue: value ?? 0 })}
        />
      </Form.Item>
      <Form.Item label={t('my_plugin.max_value')}>
        <InputNumber
          value={config?.maxValue ?? 100}
          onChange={(value) => onChange({ ...config, maxValue: value ?? 100 })}
        />
      </Form.Item>
    </>
  )
}
```

### Using CoreShop Select Components

```typescript
// src/conditions/CountryBasedCondition.tsx
import React from 'react'
import { Form } from 'antd'
import { CountryMultiSelectField } from '@coreshop/address'
import { StoreSelect } from '@coreshop/store'
import type { ConditionComponentProps } from '@coreshop/rule'

interface CountryConditionConfig {
  countries: number[]
  store: number | null
}

export const CountryBasedCondition: React.FC<ConditionComponentProps<CountryConditionConfig>> = ({
  config,
  onChange
}) => {
  return (
    <>
      <Form.Item label="Countries">
        <CountryMultiSelectField
          value={config?.countries ?? []}
          onChange={(countries) => onChange({ ...config, countries })}
        />
      </Form.Item>
      <Form.Item label="Store">
        <StoreSelect
          value={config?.store}
          onChange={(store) => onChange({ ...config, store })}
        />
      </Form.Item>
    </>
  )
}
```

### Registering for Multiple Rule Types

```typescript
// src/main.ts
import { container, type IAbstractPlugin } from '@pimcore/studio-ui-bundle'
import type { ConditionRegistry } from '@coreshop/rule'
import { coreshopOrderServiceIds } from '@coreshop/order'
import { coreshopProductServiceIds } from '@coreshop/product'
import { coreshopShippingServiceIds } from '@coreshop/shipping'
import { MySharedCondition } from './conditions/MySharedCondition'

const plugin: IAbstractPlugin = {
  name: 'my-coreshop-plugin',

  onInit() {
    // Register for Cart Price Rules
    const cartConditionRegistry = container.get<ConditionRegistry>(
      coreshopOrderServiceIds.cartPriceRuleConditionRegistry
    )
    cartConditionRegistry.register('myCondition', MySharedCondition)

    // Register for Product Price Rules
    const productConditionRegistry = container.get<ConditionRegistry>(
      coreshopProductServiceIds.productPriceRuleConditionRegistry
    )
    productConditionRegistry.register('myCondition', MySharedCondition)

    // Register for Shipping Rules
    const shippingConditionRegistry = container.get<ConditionRegistry>(
      coreshopShippingServiceIds.shippingRuleConditionRegistry
    )
    shippingConditionRegistry.register('myCondition', MySharedCondition)
  }
}
```

## Available Package Exports

### @coreshop/rule

```typescript
// Registry classes
import { ConditionRegistry, ActionRegistry } from '@coreshop/rule'

// Types
import type { ConditionComponentProps, ActionComponentProps, RuleCondition, RuleAction } from '@coreshop/rule'

// Components
import { ConditionsPanel, ActionsPanel, RuleManager } from '@coreshop/rule'
```

### @coreshop/order

```typescript
// Service IDs for registries
import { coreshopOrderServiceIds } from '@coreshop/order'
// Contains:
// - cartPriceRuleConditionRegistry
// - cartPriceRuleActionRegistry
// - cartItemConditionRegistry
// - cartItemActionRegistry

// Conditions and Actions
import { AmountCondition, VoucherCondition } from '@coreshop/order'
import { SurchargePercentAction, SurchargeAmountAction } from '@coreshop/order'
```

### @coreshop/product

```typescript
// Service IDs
import { coreshopProductServiceIds } from '@coreshop/product'
// Contains:
// - productPriceRuleConditionRegistry
// - productPriceRuleActionRegistry
// - productSpecificPriceRuleConditionRegistry
// - productSpecificPriceRuleActionRegistry

// Components
import { ProductMultiSelectField, CategoryMultiSelectField, ProductUnitSelect } from '@coreshop/product'
```

### @coreshop/shipping

```typescript
// Service IDs
import { coreshopShippingServiceIds } from '@coreshop/shipping'
// Contains:
// - shippingRuleConditionRegistry
// - shippingRuleActionRegistry

// Components
import { CarrierSelect, CarrierMultiSelectField, ShippingRuleSelect } from '@coreshop/shipping'
```

### @coreshop/address

```typescript
// Components
import {
  CountrySelect,
  CountrySelectField,
  CountryMultiSelectField,
  StateSelect,
  StateSelectField,
  ZoneMultiSelectField
} from '@coreshop/address'
```

### @coreshop/currency

```typescript
// Components
import { CurrencySelect, CurrencySelectField, CurrencyMultiSelectField } from '@coreshop/currency'
```

### @coreshop/store

```typescript
// Components
import { StoreSelect, StoreMultiSelect, StoreMultiSelectField } from '@coreshop/store'

// API
import { storeApi } from '@coreshop/store'
```

### @coreshop/taxation

```typescript
// API and components
import { taxRateApi, TaxRateManager, TaxRateForm } from '@coreshop/taxation'
import { taxRuleGroupApi, TaxRuleGroupManager } from '@coreshop/taxation'
```

### @coreshop/customer

```typescript
// Components
import { CustomerMultiSelectField, CustomerGroupMultiSelectField } from '@coreshop/customer'
```

### @coreshop/resource

```typescript
// Entity management
import { EntityManager, EntityList, EntityDetail } from '@coreshop/resource'

// Extensions
import {
  entityFormExtensionsServiceId,
  entityTableColumnExtensionsServiceId,
  entitySaveDecoratorsServiceId,
  entityTabExtensionsServiceId,
  entityActionExtensionsServiceId,
  entityValidationExtensionsServiceId,
  entityLifecycleHooksServiceId
} from '@coreshop/resource'

// Form Builder
import { FormBuilder } from '@coreshop/resource'
```

## PHP Backend Integration

Remember to also implement the PHP backend for your custom conditions/actions:

```php
// src/Condition/MyCustomConditionChecker.php
namespace MyPlugin\Condition;

use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;

class MyCustomConditionChecker implements ConditionCheckerInterface
{
    public function isValid(
        ResourceInterface $subject,
        array $configuration,
        array $params = []
    ): bool {
        $minValue = $configuration['minValue'] ?? 0;
        $maxValue = $configuration['maxValue'] ?? 100;

        // Your validation logic here
        return true;
    }
}
```

```yaml
# config/services.yaml
services:
    MyPlugin\Condition\MyCustomConditionChecker:
        tags:
            - { name: 'coreshop.cart_price_rule.condition', type: 'myCustomCondition', form-type: 'MyPlugin\Form\Type\MyCustomConditionType' }
```

## Important Notes

1. **Singleton Pattern**: All CoreShop bundles MUST be configured as singletons in Module Federation to ensure registries are shared properly.

2. **TypeScript Types**: CoreShop provides full TypeScript type definitions. Use `import type` for type-only imports.

3. **Translation Keys**: Register your translation keys in your bundle's translation files.

4. **Build Order**: Ensure CoreShop bundles are built before your plugin during development.

5. **Version Compatibility**: Keep your `@pimcore/studio-ui-bundle` version aligned with CoreShop's version.

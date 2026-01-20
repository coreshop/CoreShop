# Extending Payment Provider Rules in Pimcore Studio v2

This guide explains how to extend Payment Provider Rules with custom Actions, Conditions, and Gateway Configurators in Pimcore Studio (React/TypeScript).

## Overview

Payment Provider Rules in CoreShop allow you to define conditions and price modifications for payment methods. The system consists of:

- **Conditions**: Define when a payment rule applies (e.g., cart amount, customer group)
- **Actions**: Define price modifications (e.g., surcharge, discount, fixed price)
- **Gateway Configurators**: Define configuration UI for payment gateways (e.g., PayPal, Stripe)

## Architecture

### Registry Structure

PaymentBundle creates three registries:

```typescript
// Payment Provider Rule registries
container.bind(coreshopPaymentServiceIds.paymentProviderRuleConditionRegistry)
    .to(ConditionRegistry).inSingletonScope()
container.bind(coreshopPaymentServiceIds.paymentProviderRuleActionRegistry)
    .to(ActionRegistry).inSingletonScope()

// Gateway configurator registry
container.bind(coreshopPaymentServiceIds.gatewayConfiguratorRegistry)
    .to(GatewayRegistry).inSingletonScope()
```

### Bundle Responsibilities

- **PaymentBundle**: Registers payment-specific conditions/actions and gateway configurators
- **CoreBundle**: Registers shared conditions/actions (categories, customers, countries, etc.)

### Service IDs

```typescript
// src/CoreShop/Bundle/PaymentBundle/.../service-ids.ts
export const coreshopPaymentServiceIds = {
  paymentProviderRuleConditionRegistry: Symbol.for('coreshop.payment.payment_provider_rule.condition_registry'),
  paymentProviderRuleActionRegistry: Symbol.for('coreshop.payment.payment_provider_rule.action_registry'),
  gatewayConfiguratorRegistry: Symbol.for('coreshop.payment.gateway_configurator_registry')
}
```

## Adding a Custom Condition

### Step 1: Create the React Component

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/payment-provider-rules/conditions/MinItemCountCondition.tsx

import React from 'react'
import { Form, InputNumber } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'

export const MinItemCountCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const minItems = data?.minItems || 1

  const handleChange = (value: number | null) => {
    onChange({ ...data, minItems: value || 1 })
  }

  return (
    <Form.Item
      label={t('coreshop_condition_min_items', { defaultValue: 'Minimum Items' })}
      help="Minimum number of items in cart for this rule to apply"
    >
      <InputNumber
        value={minItems}
        onChange={handleChange}
        min={1}
        precision={0}
        style={{ width: '100%' }}
      />
    </Form.Item>
  )
}
```

### Step 2: Register the Condition

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/main.ts

import { IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import type { ConditionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopPaymentServiceIds } from '@coreshop/payment/src/modules/payment-provider-rules/service-ids'
import { MinItemCountCondition } from './modules/payment-provider-rules/conditions'

const plugin: IAbstractPlugin = {
    name: 'your-bundle',

    onInit() {
        const conditionRegistry = container.get<ConditionRegistry>(
            coreshopPaymentServiceIds.paymentProviderRuleConditionRegistry
        )
        conditionRegistry.register('minItemCount', MinItemCountCondition)
    }
}

export default plugin
```

## Adding a Custom Action

### Step 1: Create the React Component

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/payment-provider-rules/actions/PercentageSurchargeAction.tsx

import React from 'react'
import { Form, InputNumber } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ActionComponentProps } from '@coreshop/rule/src/rules/types'

export const PercentageSurchargeAction: React.FC<ActionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
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
        label={t('coreshop_action_surcharge_percent', { defaultValue: 'Surcharge Percentage' })}
        name="percent"
        help="Percentage to add to payment cost"
        rules={[{ required: true, message: 'Percentage is required' }]}
      >
        <InputNumber
          min={0}
          max={100}
          precision={2}
          addonAfter="%"
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}
```

### Step 2: Register the Action

```typescript
const actionRegistry = container.get<ActionRegistry>(
    coreshopPaymentServiceIds.paymentProviderRuleActionRegistry
)
actionRegistry.register('percentageSurcharge', PercentageSurchargeAction)
```

## Built-in Components

### Conditions (PaymentBundle)

| Key | Component | Description |
|-----|-----------|-------------|
| `amount` | AmountCondition | Cart amount range (min/max, gross/net, total) |
| `paymentProviderRule` | PaymentProviderRuleCondition | Reference another payment provider rule |

### Conditions (Shared from CoreBundle)

| Key | Component | Description |
|-----|-----------|-------------|
| `carriers` | CarriersCondition | Selected shipping carriers |
| `categories` | CategoriesCondition | Product categories in cart |
| `countries` | CountriesCondition | Customer/billing country |
| `currencies` | CurrenciesCondition | Order currency |
| `customerGroups` | CustomerGroupsCondition | Customer groups |
| `customers` | CustomersCondition | Specific customers |
| `guest` | GuestCondition | Guest checkout |
| `nested` | NestedCondition | Combine conditions with AND/OR |
| `products` | ProductsCondition | Specific products in cart |
| `stores` | StoresCondition | Store selection |
| `timespan` | TimespanCondition | Date/time range |
| `zones` | ZonesCondition | Geographic zones |

### Actions (PaymentBundle)

| Key | Component | Description |
|-----|-----------|-------------|
| `additionPercent` | AdditionPercentAction | Add percentage surcharge |
| `additionAmount` | AdditionAmountAction | Add fixed amount surcharge |
| `discountPercent` | DiscountPercentAction | Percentage discount |
| `price` | PriceAction | Fixed price override |
| `paymentProviderRule` | PaymentProviderRuleAction | Apply another payment provider rule |

### Actions (Shared from CoreBundle)

| Key | Component | Description |
|-----|-----------|-------------|
| `discountAmount` | DiscountAmountAction | Fixed amount discount |
| `discountPercent` | DiscountPercentAction | Percentage discount |

## Gateway Configurators

Gateway configurators provide custom configuration UI for payment gateways (PayPal, Stripe, etc.).

### Architecture

```typescript
export interface GatewayConfiguratorProps {
  config: Record<string, any>
  onChange: (config: Record<string, any>) => void
}

export type GatewayConfigurator = React.FC<GatewayConfiguratorProps>
```

### Creating a Custom Gateway Configurator

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/payment-providers/gateways/StripeConfigurator.tsx

import React from 'react'
import { Form, Input, Switch, Select } from 'antd'
import { useTranslation } from 'react-i18next'
import type { GatewayConfiguratorProps } from '@coreshop/payment/src/modules/payment-providers/gateways'

export const StripeConfigurator: React.FC<GatewayConfiguratorProps> = ({
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const [form] = Form.useForm()

  React.useEffect(() => {
    form.setFieldsValue(config ?? {})
  }, [config])

  return (
    <Form
      form={form}
      layout="vertical"
      onValuesChange={(_, allValues) => {
        onChange(allValues)
      }}
    >
      <Form.Item
        label={t('coreshop_gateway_stripe_publishable_key', { defaultValue: 'Publishable Key' })}
        name="publishable_key"
        rules={[{ required: true, message: 'Publishable key is required' }]}
      >
        <Input placeholder="pk_live_..." />
      </Form.Item>

      <Form.Item
        label={t('coreshop_gateway_stripe_secret_key', { defaultValue: 'Secret Key' })}
        name="secret_key"
        rules={[{ required: true, message: 'Secret key is required' }]}
      >
        <Input.Password placeholder="sk_live_..." />
      </Form.Item>

      <Form.Item
        label={t('coreshop_gateway_stripe_sandbox', { defaultValue: 'Sandbox Mode' })}
        name="sandbox"
        valuePropName="checked"
      >
        <Switch />
      </Form.Item>

      <Form.Item
        label={t('coreshop_gateway_stripe_payment_methods', { defaultValue: 'Payment Methods' })}
        name="payment_methods"
      >
        <Select
          mode="multiple"
          options={[
            { value: 'card', label: 'Credit Card' },
            { value: 'sepa_debit', label: 'SEPA Direct Debit' },
            { value: 'ideal', label: 'iDEAL' },
            { value: 'giropay', label: 'Giropay' },
          ]}
        />
      </Form.Item>
    </Form>
  )
}
```

### Registering a Gateway Configurator

```typescript
import { GatewayRegistry } from '@coreshop/payment/src/modules/payment-providers/gateways'
import { coreshopPaymentServiceIds } from '@coreshop/payment/src/modules/payment-provider-rules/service-ids'

const plugin: IAbstractPlugin = {
    name: 'your-bundle',

    onInit() {
        const gatewayRegistry = container.get<GatewayRegistry>(
            coreshopPaymentServiceIds.gatewayConfiguratorRegistry
        )

        // Register with the factory name (lowercase)
        gatewayRegistry.register('stripe', StripeConfigurator)
    }
}
```

### Built-in Gateway Configurators

| Factory Name | Configurator | Description |
|--------------|--------------|-------------|
| `paypal_express_checkout` | PayPalExpressCheckoutConfigurator | PayPal Express settings |
| `sofort` | SofortConfigurator | Sofort/Klarna settings |

## File Structure

```
PaymentBundle/Resources/assets/pimcore-studio/src/
├── main.ts                              # Plugin entry, registry setup
├── components/
│   ├── index.ts
│   └── PaymentProviderSelect.tsx        # Reusable payment provider select
├── modules/
│   ├── icon-library/
│   │   └── index.ts
│   ├── payment-providers/
│   │   ├── index.ts
│   │   ├── api.ts                       # Payment provider API client
│   │   ├── PaymentProviderManager.tsx   # Payment provider manager widget
│   │   ├── PaymentProviderForm.tsx      # Payment provider form
│   │   ├── PaymentProviderRuleGroupPanel.tsx
│   │   └── gateways/
│   │       ├── index.ts
│   │       ├── GatewayRegistry.ts       # Gateway configurator registry
│   │       ├── GatewayConfigPanel.tsx   # Gateway config wrapper
│   │       ├── PayPalExpressCheckoutConfigurator.tsx
│   │       └── SofortConfigurator.tsx
│   └── payment-provider-rules/
│       ├── index.ts
│       ├── service-ids.ts               # Registry service IDs
│       ├── types.ts                     # TypeScript types
│       ├── api.ts                       # Payment provider rule API
│       ├── PaymentProviderRuleManager.tsx
│       ├── PaymentProviderRuleFormBuilder.ts
│       ├── form-builder-module.ts
│       ├── components/
│       │   ├── index.ts
│       │   ├── SettingsForm.tsx
│       │   └── PaymentProviderRuleSelect.tsx
│       ├── conditions/
│       │   ├── index.ts
│       │   ├── AmountCondition.tsx
│       │   └── PaymentProviderRuleCondition.tsx
│       └── actions/
│           ├── index.ts
│           ├── AdditionPercentAction.tsx
│           ├── AdditionAmountAction.tsx
│           ├── DiscountPercentAction.tsx
│           ├── PriceAction.tsx
│           └── PaymentProviderRuleAction.tsx
└── dynamic-types/
    ├── index.ts
    ├── DynamicTypeObjectDataCoreShopPaymentProvider.tsx
    └── DynamicTypeObjectDataCoreShopPaymentProviderMultiselect.tsx
```

## Backend Implementation

### PHP Condition Checker

```php
<?php

namespace App\CoreShop\Condition;

use CoreShop\Component\Payment\Rule\Condition\PaymentConditionCheckerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;

class MinItemCountConditionChecker implements PaymentConditionCheckerInterface
{
    public function isValid(
        PaymentProviderInterface $paymentProvider,
        CartInterface $cart,
        array $configuration
    ): bool {
        $minItems = $configuration['minItems'] ?? 1;
        $itemCount = count($cart->getItems());

        return $itemCount >= $minItems;
    }
}
```

### PHP Action Processor

```php
<?php

namespace App\CoreShop\Action;

use CoreShop\Component\Payment\Rule\Action\PaymentPriceActionProcessorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;

class PercentageSurchargeActionProcessor implements PaymentPriceActionProcessorInterface
{
    public function getPrice(
        PaymentProviderInterface $paymentProvider,
        CartInterface $cart,
        int $price,
        array $configuration
    ): int {
        $percent = $configuration['percent'] ?? 0;
        $surcharge = (int) round($price * ($percent / 100));

        return $price + $surcharge;
    }
}
```

### Service Registration

```yaml
# config/services.yaml

# Condition
App\CoreShop\Condition\MinItemCountConditionChecker:
  tags:
    - { name: coreshop.payment_provider_rule.condition, type: minItemCount, form-type: App\Form\Type\MinItemCountConditionType }

# Action
App\CoreShop\Action\PercentageSurchargeActionProcessor:
  tags:
    - { name: coreshop.payment_provider_rule.action, type: percentageSurcharge, form-type: App\Form\Type\PercentageSurchargeActionType }
```

## Testing Your Extension

1. **Build the bundle**: `npm run dev:single YourBundle`
2. **Clear browser cache**: Hard refresh (Ctrl+Shift+R)
3. **Test conditions/actions**:
   - Navigate to CoreShop -> Payment -> Payment Provider Rules
   - Create a new rule
   - Add your custom condition/action
   - Verify the form displays correctly
   - Save and verify persistence

4. **Test gateway configurator**:
   - Navigate to CoreShop -> Payment -> Payment Providers
   - Create/edit a payment provider
   - Select your gateway type
   - Verify the configuration form displays
   - Save and verify persistence

## Best Practices

1. **Use TypeScript**: Type-safe components prevent runtime errors
2. **Cache API calls**: Implement module-level caching for select options
3. **Validate required fields**: Use Form.Item `rules` for validation
4. **Add help text**: Guide users with clear descriptions
5. **Handle currencies properly**: Always specify currency for amount-based actions
6. **Test with multiple payment providers**: Ensure rules work across different providers
7. **Secure gateway credentials**: Use Input.Password for sensitive fields

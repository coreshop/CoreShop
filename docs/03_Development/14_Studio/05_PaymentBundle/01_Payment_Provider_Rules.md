# Extending Payment Provider Rules in Pimcore Studio v2

This guide explains how to extend Payment Provider Rules with custom Actions, Conditions, and Gateway Configurators in Pimcore Studio (React/TypeScript).

## Overview

Payment Provider Rules in CoreShop allow you to define conditions and price modifications for payment methods. The system consists of:

- **Conditions**: Define when a payment rule applies (e.g., cart amount, customer group)
- **Actions**: Define price modifications (e.g., surcharge, discount, fixed price)
- **Gateway Configurators**: Define configuration UI for payment gateways (e.g., PayPal, Stripe)

## Schema-Driven Forms (No Custom JS Needed)

**Most payment provider rule conditions and actions no longer require custom React components.** The configuration forms are rendered automatically from PHP FormTypes via the StudioFormBundle.

To add a new condition or action, you only need:

1. A PHP FormType defining the configuration fields
2. A service registration with the `form-type` attribute

The frontend auto-generates the React form from the backend schema at runtime.

### Adding a Custom Condition (PHP Only)

```php
<?php

namespace App\Form\Type\Rule\Condition;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class MinItemCountConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minItems', IntegerType::class, [
                'label' => 'app_condition_min_items',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_payment_provider_rule_condition_min_item_count';
    }
}
```

```yaml
services:
    app.payment_provider_rule.condition.min_item_count:
        class: App\Rule\Condition\MinItemCountConditionChecker
        tags:
            - name: coreshop.payment_provider_rule.condition
              type: minItemCount
              form-type: App\Form\Type\Rule\Condition\MinItemCountConfigurationType
```

**Done.** No React/TypeScript code needed. The condition form renders automatically.

### Adding a Custom Action (PHP Only)

```php
<?php

namespace App\Form\Type\Rule\Action;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

final class PercentageSurchargeConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('percent', NumberType::class, [
                'label' => 'app_action_surcharge_percent',
                'required' => true,
                'scale' => 2,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_payment_provider_rule_action_percentage_surcharge';
    }
}
```

```yaml
services:
    app.payment_provider_rule.action.percentage_surcharge:
        class: App\Rule\Action\PercentageSurchargeActionProcessor
        tags:
            - name: coreshop.payment_provider_rule.action
              type: percentageSurcharge
              form-type: App\Form\Type\Rule\Action\PercentageSurchargeConfigurationType
```

For the full schema-driven pattern explanation, see [StudioFormBundle Examples — Example 13](../02_Base_Infrastructure/05_StudioFormBundle_Examples.md#example-13--rule-conditionaction-as-schema-form).

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

- **PaymentBundle**: Creates registries; all condition/action forms are schema-driven from PHP FormTypes
- **CoreBundle**: Registers `NestedCondition` (the only hand-written component) across all rule type registries
- **Schema system**: Automatically generates and registers all other condition/action forms at runtime

### Service IDs

```typescript
// src/CoreShop/Bundle/PaymentBundle/.../service-ids.ts
export const coreshopPaymentServiceIds = {
  paymentProviderRuleConditionRegistry: Symbol.for('coreshop.payment.payment_provider_rule.condition_registry'),
  paymentProviderRuleActionRegistry: Symbol.for('coreshop.payment.payment_provider_rule.action_registry'),
  gatewayConfiguratorRegistry: Symbol.for('coreshop.payment.gateway_configurator_registry')
}
```

## Built-in Components

### Conditions (All schema-driven from PHP FormTypes)

| Key | Source Bundle | Description |
|-----|-------------|-------------|
| `amount` | PaymentBundle | Cart amount range (min/max, gross/net, total) |
| `paymentProviderRule` | PaymentBundle | Reference another payment provider rule |
| `carriers` | CoreBundle | Selected shipping carriers |
| `categories` | CoreBundle | Product categories in cart |
| `countries` | CoreBundle | Customer/billing country |
| `currencies` | CoreBundle | Order currency |
| `customerGroups` | CoreBundle | Customer groups |
| `customers` | CoreBundle | Specific customers |
| `guest` | CoreBundle | Guest checkout |
| `products` | CoreBundle | Specific products in cart |
| `stores` | CoreBundle | Store selection |
| `zones` | CoreBundle | Geographic zones |

### Conditions (Hand-written — require custom JS)

| Key | Source Bundle | Reason |
|-----|-------------|--------|
| `nested` | CoreBundle | Recursively renders sub-conditions with AND/OR logic |
| `timespan` | CoreBundle | Uses custom date/time picker composition |

### Actions (All schema-driven from PHP FormTypes)

| Key | Source Bundle | Description |
|-----|-------------|-------------|
| `additionPercent` | PaymentBundle | Add percentage surcharge |
| `additionAmount` | PaymentBundle | Add fixed amount surcharge |
| `discountPercent` | PaymentBundle | Percentage discount |
| `discountAmount` | CoreBundle | Fixed amount discount |
| `price` | PaymentBundle | Fixed price override |
| `paymentProviderRule` | PaymentBundle | Apply another payment provider rule |

## Gateway Configurators

Gateway configurators provide custom configuration UI for payment gateways (PayPal, Stripe, etc.). These still use hand-written React components because they are not part of the rule system.

### Creating a Custom Gateway Configurator

```typescript
import React from 'react'
import { Form, Input, Switch } from 'antd'
import type { GatewayConfiguratorProps } from '@coreshop/payment/src/modules/payment-providers/gateways'

export const StripeConfigurator: React.FC<GatewayConfiguratorProps> = ({
  config,
  onChange
}) => {
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
      <Form.Item label="Publishable Key" name="publishable_key" rules={[{ required: true }]}>
        <Input placeholder="pk_live_..." />
      </Form.Item>
      <Form.Item label="Secret Key" name="secret_key" rules={[{ required: true }]}>
        <Input.Password placeholder="sk_live_..." />
      </Form.Item>
      <Form.Item label="Sandbox Mode" name="sandbox" valuePropName="checked">
        <Switch />
      </Form.Item>
    </Form>
  )
}
```

### Registering a Gateway Configurator

```typescript
import { GatewayRegistry } from '@coreshop/payment/src/modules/payment-providers/gateways'
import { coreshopPaymentServiceIds } from '@coreshop/payment/src/modules/payment-provider-rules/service-ids'

const gatewayRegistry = container.get<GatewayRegistry>(
    coreshopPaymentServiceIds.gatewayConfiguratorRegistry
)

// Register with the factory name (lowercase)
gatewayRegistry.register('stripe', StripeConfigurator)
```

### Built-in Gateway Configurators

| Factory Name | Description |
|--------------|-------------|
| `paypal_express_checkout` | PayPal Express settings |
| `sofort` | Sofort/Klarna settings |

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

class PercentageSurchargeActionProcessor implements PaymentPriceActionProcessorInterface
{
    public function getPrice(
        $paymentProvider,
        $cart,
        int $price,
        array $configuration
    ): int {
        $percent = $configuration['percent'] ?? 0;
        $surcharge = (int) round($price * ($percent / 100));

        return $price + $surcharge;
    }
}
```

## Testing Your Extension

1. **Clear the Symfony cache**: `docker compose exec php bin/console cache:clear`
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

1. **Prefer PHP FormTypes**: Use the schema-driven approach for all new conditions/actions
2. **Gateway configurators still need React**: They are not part of the rule system
3. **Cache API calls**: Implement module-level caching for select options in hand-written components
4. **Secure gateway credentials**: Use `Input.Password` for sensitive fields in gateway configurators
5. **Test with multiple payment providers**: Ensure rules work across different providers

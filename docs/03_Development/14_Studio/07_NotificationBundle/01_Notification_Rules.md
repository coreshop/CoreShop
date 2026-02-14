# Extending Notification Rules in Pimcore Studio v2

This guide explains how to extend Notification Rules with custom Actions and Conditions in Pimcore Studio (React/TypeScript).

## Overview

Notification Rules in CoreShop allow you to trigger notifications (emails, webhooks, etc.) based on various events in the eCommerce workflow. The system consists of:

- **Notification Types**: Categories of events (order, payment, invoice, shipment, quote, user, messaging)
- **Conditions**: Define when a notification should be sent (e.g., order state, payment state)
- **Actions**: Define what notification to send (e.g., email with specific template)

### Key Difference from Other Rule Systems

Notification Rules use **type-prefixed conditions and actions**. This means:
- Conditions are registered as `{type}.{condition}` (e.g., `order.orderState`)
- Actions are registered as `{type}.{action}` (e.g., `order.mail`)
- The same action component (e.g., `MailAction`) can be reused across different notification types

## Schema-Driven Forms

Like all other rule types in CoreShop, notification rule conditions and actions can be **schema-driven** — their forms rendered automatically from PHP FormTypes via the StudioFormBundle. To add a new condition or action, you only need a PHP FormType and a service registration with the `form-type` attribute. No React code needed for standard forms.

For the full schema-driven pattern, see [StudioFormBundle Examples — Example 13](../02_Base_Infrastructure/05_StudioFormBundle_Examples.md#example-13--rule-conditionaction-as-schema-form).

Notification-specific conditions (state selectors, transition selectors) and mail actions still use hand-written React components because they have specialized UI requirements (language tabs, state dropdowns populated from the backend workflow configuration, etc.).

## Architecture

### Notification Types

```typescript
export type NotificationRuleType =
  | 'order'      // Order lifecycle events
  | 'payment'    // Payment events
  | 'invoice'    // Invoice events
  | 'shipment'   // Shipment events
  | 'quote'      // Quote events
  | 'user'       // User registration, password reset, etc.
  | 'messaging'  // Contact forms, inquiries
```

### Registry Structure

NotificationBundle creates the registries, CoreBundle registers the conditions:

```typescript
// NotificationBundle creates registries
container.bind(coreshopNotificationServiceIds.notificationRuleConditionRegistry)
    .to(ConditionRegistry).inSingletonScope()
container.bind(coreshopNotificationServiceIds.notificationRuleActionRegistry)
    .to(ActionRegistry).inSingletonScope()

// CoreBundle registers conditions with type prefixes
conditionRegistry.register('order.orderState', OrderStateCondition)
conditionRegistry.register('order.orderTransition', OrderTransitionCondition)
// ... etc
```

### Service IDs

```typescript
// src/CoreShop/Bundle/NotificationBundle/.../service-ids.ts
export const coreshopNotificationServiceIds = {
  notificationRuleConditionRegistry: Symbol.for('coreshop.notification.notification_rule.condition_registry'),
  notificationRuleActionRegistry: Symbol.for('coreshop.notification.notification_rule.action_registry')
}
```

## Built-in Conditions

### Common Conditions (All Notification Types)

| Key | Component | Description |
|-----|-----------|-------------|
| `stores` | StoresCondition | Limit to specific stores |
| `{type}.stores` | StoresCondition | Store condition with type prefix |

### Order Notification Conditions

| Key | Component | Description |
|-----|-----------|-------------|
| `order.orderState` | OrderStateCondition | Order workflow state |
| `order.orderTransition` | OrderTransitionCondition | Order state transition |
| `order.orderPaymentState` | OrderPaymentStateCondition | Payment state of order |
| `order.orderPaymentTransition` | OrderPaymentTransitionCondition | Payment state transition |
| `order.orderShippingState` | OrderShippingStateCondition | Shipping state of order |
| `order.orderShippingTransition` | OrderShippingTransitionCondition | Shipping state transition |
| `order.orderInvoiceState` | OrderInvoiceStateCondition | Invoice state of order |
| `order.orderInvoiceTransition` | OrderInvoiceTransitionCondition | Invoice state transition |
| `order.saleState` | SaleStateCondition | General sale state |
| `order.carriers` | CarriersCondition | Specific carriers |
| `order.payment` | PaymentCondition | Payment provider condition |
| `order.comment` | CommentCondition | Order has comment |
| `order.backendCreated` | BackendCreatedCondition | Order created from backend |

### Payment Notification Conditions

| Key | Component | Description |
|-----|-----------|-------------|
| `payment.paymentState` | PaymentStateCondition | Payment state |
| `payment.paymentTransition` | PaymentTransitionCondition | Payment state transition |

### Invoice Notification Conditions

| Key | Component | Description |
|-----|-----------|-------------|
| `invoice.invoiceState` | InvoiceStateCondition | Invoice state |
| `invoice.invoiceTransition` | InvoiceTransitionCondition | Invoice state transition |

### Shipment Notification Conditions

| Key | Component | Description |
|-----|-----------|-------------|
| `shipment.shipmentState` | ShipmentStateCondition | Shipment state |
| `shipment.shipmentTransition` | ShipmentTransitionCondition | Shipment state transition |

### Quote Notification Conditions

| Key | Component | Description |
|-----|-----------|-------------|
| `quote.quoteState` | QuoteStateCondition | Quote state |
| `quote.quoteTransition` | QuoteTransitionCondition | Quote state transition |

### User Notification Conditions

| Key | Component | Description |
|-----|-----------|-------------|
| `user.userType` | UserTypeCondition | Type of user event (register, password reset, etc.) |

### Messaging Notification Conditions

| Key | Component | Description |
|-----|-----------|-------------|
| `messaging.messageType` | MessageTypeCondition | Type of message (contact, inquiry, etc.) |

## Built-in Actions

### Mail Actions

| Key | Component | Description |
|-----|-----------|-------------|
| `mail` | MailAction | Basic email with language-specific templates |
| `storeMail` | StoreMailAction | Store-specific email templates |
| `orderMail` | OrderMailAction | Order-specific email with order data |
| `storeOrderMail` | StoreOrderMailAction | Combined store and order email |

All mail actions are registered for each notification type:
- `order.mail`, `order.storeMail`, `order.orderMail`, `order.storeOrderMail`
- `payment.mail`, `payment.storeMail`, etc.

## Adding a Custom Condition

### Step 1: Create the Condition Component

For state-based conditions, extend `AbstractStateCondition`:

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/notification-rules/conditions/CustomStateCondition.tsx

import React from 'react'
import { AbstractStateCondition } from '@coreshop/notification/src/modules/notification-rules/conditions'

export const CustomStateCondition: React.FC<any> = (props) => {
  const states = [
    { value: 'pending', label: 'Pending' },
    { value: 'processing', label: 'Processing' },
    { value: 'completed', label: 'Completed' }
  ]

  return (
    <AbstractStateCondition
      {...props}
      label="Custom State"
      states={states}
    />
  )
}
```

For non-state conditions:

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/notification-rules/conditions/MinAmountCondition.tsx

import React from 'react'
import { Form, InputNumber } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'

export const MinAmountCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const config = data ?? {}

  return (
    <Form.Item label={t('coreshop_min_amount', { defaultValue: 'Minimum Amount' })}>
      <InputNumber
        value={config.minAmount}
        onChange={(value) => onChange({ ...config, minAmount: value })}
        min={0}
        precision={2}
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
import { coreshopNotificationServiceIds } from '@coreshop/notification/src/modules/notification-rules/service-ids'
import { MinAmountCondition } from './modules/notification-rules/conditions'

const plugin: IAbstractPlugin = {
    name: 'your-bundle',

    onInit() {
        const conditionRegistry = container.get<ConditionRegistry>(
            coreshopNotificationServiceIds.notificationRuleConditionRegistry
        )

        // Register for specific notification type(s)
        conditionRegistry.register('order.minAmount', MinAmountCondition)

        // Or register for multiple types
        const types = ['order', 'quote']
        types.forEach(type => {
            conditionRegistry.register(`${type}.minAmount`, MinAmountCondition)
        })
    }
}

export default plugin
```

## Adding a Custom Action

### Step 1: Create the Action Component

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/notification-rules/actions/WebhookAction.tsx

import React from 'react'
import { Form, Input, Select } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ActionComponentProps } from '@coreshop/rule/src/rules/types'

interface WebhookActionConfig {
  url?: string
  method?: 'GET' | 'POST' | 'PUT'
  headers?: string
}

export const WebhookAction: React.FC<ActionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const config = (data ?? {}) as WebhookActionConfig

  const handleChange = (field: string, value: any) => {
    onChange({ ...config, [field]: value })
  }

  return (
    <>
      <Form.Item
        label={t('coreshop_webhook_url', { defaultValue: 'Webhook URL' })}
        required
      >
        <Input
          value={config.url}
          onChange={(e) => handleChange('url', e.target.value)}
          placeholder="https://api.example.com/webhook"
        />
      </Form.Item>

      <Form.Item
        label={t('coreshop_webhook_method', { defaultValue: 'HTTP Method' })}
      >
        <Select
          value={config.method || 'POST'}
          onChange={(value) => handleChange('method', value)}
          options={[
            { value: 'GET', label: 'GET' },
            { value: 'POST', label: 'POST' },
            { value: 'PUT', label: 'PUT' }
          ]}
        />
      </Form.Item>

      <Form.Item
        label={t('coreshop_webhook_headers', { defaultValue: 'Custom Headers (JSON)' })}
      >
        <Input.TextArea
          value={config.headers}
          onChange={(e) => handleChange('headers', e.target.value)}
          placeholder='{"Authorization": "Bearer xxx"}'
          rows={3}
        />
      </Form.Item>
    </>
  )
}
```

### Step 2: Register the Action

```typescript
const actionRegistry = container.get<ActionRegistry>(
    coreshopNotificationServiceIds.notificationRuleActionRegistry
)

// Register for all notification types
const notificationTypes = ['order', 'payment', 'invoice', 'shipment', 'quote', 'user', 'messaging']
notificationTypes.forEach(type => {
    actionRegistry.register(`${type}.webhook`, WebhookAction)
})

// Or register without prefix (generic)
actionRegistry.register('webhook', WebhookAction)
```

## State and Transition Conditions

### State Conditions

State conditions check the current state of an entity:

```typescript
// StateConditionBase.tsx pattern
export const createStateCondition = (
  label: string,
  stateKey: string,
  states: Array<{ value: string; label: string }>
) => {
  const StateCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => {
    return (
      <Form.Item label={label}>
        <Select
          value={data?.[stateKey]}
          onChange={(value) => onChange({ ...data, [stateKey]: value })}
          options={states}
        />
      </Form.Item>
    )
  }
  return StateCondition
}
```

### Transition Conditions

Transition conditions check if a specific state change is occurring:

```typescript
// TransitionConditionBase.tsx pattern
export const createTransitionCondition = (
  label: string,
  transitions: Array<{ value: string; label: string }>
) => {
  const TransitionCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => {
    return (
      <Form.Item label={label}>
        <Select
          value={data?.transition}
          onChange={(value) => onChange({ ...data, transition: value })}
          options={transitions}
        />
      </Form.Item>
    )
  }
  return TransitionCondition
}
```

## Mail Action with Language Support

The `MailAction` component supports multi-language email templates:

```typescript
const MailAction: React.FC<ActionComponentProps> = ({ data, onChange }) => {
  const languages = getAvailableLanguages() // ['en', 'de', 'fr']
  const config = data as MailActionConfig

  // Renders tabs for each language
  // Each tab has a DocumentSelect for email template
  // config.mails = { en: 123, de: 456, fr: 789 }
}
```

Configuration structure:
```typescript
interface MailActionConfig {
  mails?: Record<string, number | null>  // Language -> Document ID
  doNotSendToDesignatedRecipient?: boolean
}
```

## File Structure

```
NotificationBundle/Resources/assets/pimcore-studio/src/
├── main.ts                              # Plugin entry, registry setup
├── modules/
│   ├── icon-library/
│   │   └── index.ts
│   └── notification-rules/
│       ├── index.ts
│       ├── service-ids.ts               # Registry service IDs
│       ├── types.ts                     # TypeScript types
│       ├── api.ts                       # API client
│       ├── NotificationRuleManager.tsx  # Manager widget
│       ├── NotificationRuleFormBuilder.ts
│       ├── form-builder-module.ts
│       ├── components/
│       │   ├── index.ts
│       │   └── SettingsForm.tsx
│       ├── conditions/
│       │   ├── index.ts
│       │   └── AbstractStateCondition.tsx
│       └── actions/
│           ├── index.ts
│           ├── MailAction.tsx
│           ├── StoreMailAction.tsx
│           ├── OrderMailAction.tsx
│           └── StoreOrderMailAction.tsx

CoreBundle/Resources/assets/pimcore-studio/src/modules/extension/notification-rules/
├── index.tsx                            # Extension module
└── conditions/
    ├── index.ts
    ├── StateConditionBase.tsx
    ├── TransitionConditionBase.tsx
    ├── StoresCondition.tsx
    ├── CarriersCondition.tsx
    ├── PaymentCondition.tsx
    ├── CommentCondition.tsx
    ├── BackendCreatedCondition.tsx
    ├── MessageTypeCondition.tsx
    ├── UserTypeCondition.tsx
    ├── OrderStateCondition.tsx
    ├── OrderTransitionCondition.tsx
    ├── OrderPaymentStateCondition.tsx
    ├── OrderPaymentTransitionCondition.tsx
    ├── OrderShippingStateCondition.tsx
    ├── OrderShippingTransitionCondition.tsx
    ├── OrderInvoiceStateCondition.tsx
    ├── OrderInvoiceTransitionCondition.tsx
    ├── SaleStateCondition.tsx
    ├── PaymentStateCondition.tsx
    ├── PaymentTransitionCondition.tsx
    ├── InvoiceStateCondition.tsx
    ├── InvoiceTransitionCondition.tsx
    ├── ShipmentStateCondition.tsx
    ├── ShipmentTransitionCondition.tsx
    ├── QuoteStateCondition.tsx
    └── QuoteTransitionCondition.tsx
```

## Backend Implementation

### PHP Condition Checker

```php
<?php

namespace App\CoreShop\Notification\Condition;

use CoreShop\Component\Notification\Rule\Condition\NotificationConditionCheckerInterface;

class MinAmountConditionChecker implements NotificationConditionCheckerInterface
{
    public function isValid(
        mixed $subject,
        array $params,
        array $configuration
    ): bool {
        if (!$subject instanceof OrderInterface) {
            return false;
        }

        $minAmount = $configuration['minAmount'] ?? 0;
        return $subject->getTotal() >= ($minAmount * 100); // Convert to cents
    }
}
```

### PHP Action Processor

```php
<?php

namespace App\CoreShop\Notification\Action;

use CoreShop\Component\Notification\Rule\Action\NotificationRuleProcessorInterface;

class WebhookActionProcessor implements NotificationRuleProcessorInterface
{
    public function apply(
        mixed $subject,
        array $params,
        array $configuration
    ): void {
        $url = $configuration['url'] ?? null;
        $method = $configuration['method'] ?? 'POST';
        $headers = json_decode($configuration['headers'] ?? '{}', true);

        if (!$url) {
            return;
        }

        // Send webhook request
        $this->httpClient->request($method, $url, [
            'headers' => $headers,
            'json' => $this->serializeSubject($subject)
        ]);
    }
}
```

### Service Registration

```yaml
# config/services.yaml

# Condition
App\CoreShop\Notification\Condition\MinAmountConditionChecker:
  tags:
    - { name: coreshop.notification_rule.condition, type: order.minAmount, form-type: App\Form\Type\MinAmountConditionType }

# Action
App\CoreShop\Notification\Action\WebhookActionProcessor:
  tags:
    - { name: coreshop.notification_rule.action, type: webhook, form-type: App\Form\Type\WebhookActionType }
```

## Async Registry Loading

CoreBundle uses async initialization to wait for NotificationBundle's registries:

```typescript
async function waitForRegistry(maxAttempts: number = 50, interval: number = 100): Promise<boolean> {
  let attempts = 0

  return new Promise((resolve) => {
    const checkRegistry = () => {
      attempts++

      if (container.isBound(coreshopNotificationServiceIds.notificationRuleConditionRegistry)) {
        resolve(true)
        return
      }

      if (attempts >= maxAttempts) {
        console.warn('[CoreShop Core] Timeout waiting for notification rule registry')
        resolve(false)
        return
      }

      setTimeout(checkRegistry, interval)
    }

    checkRegistry()
  })
}
```

This pattern ensures CoreBundle's conditions are registered only after NotificationBundle has created the registries.

## Best Practices

1. **Use type prefixes**: Always prefix conditions/actions with the notification type
2. **Reuse base components**: Use `AbstractStateCondition` for state-based conditions
3. **Support all languages**: Use tabs for language-specific configurations
4. **Handle async loading**: Use `waitForRegistry` pattern when extending registries
5. **Document email placeholders**: Document available placeholders for email templates
6. **Test state transitions**: Verify conditions work for both states and transitions
7. **Consider all notification types**: Register actions for all relevant notification types

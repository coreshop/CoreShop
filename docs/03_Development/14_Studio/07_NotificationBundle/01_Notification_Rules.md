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

Like all other rule types in CoreShop, notification rule conditions and actions are **schema-driven** — their forms rendered automatically from PHP FormTypes via the StudioFormBundle. **All conditions are now schema-generated from backend form types.** No hand-written React condition components exist in NotificationBundle.

To add a new condition or action, you only need a PHP FormType and a service registration with the `form-type` attribute. No React code needed for standard forms.

For the full schema-driven pattern, see [StudioFormBundle Examples — Example 13](../02_Base_Infrastructure/05_StudioFormBundle_Examples.md#example-13--rule-conditionaction-as-schema-form).

### CoreBundle Extension Module

CoreBundle previously registered hand-written condition components (state selectors, transition selectors) into the notification rule registries. These are now also schema-generated. The CoreBundle extension module at `CoreBundle/Resources/assets/pimcore-studio/src/modules/extension/notification-rules/` is kept as a placeholder for any future non-schema extensions.

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

NotificationBundle creates the registries. Schema components are registered at runtime from the backend config:

```typescript
// NotificationBundle creates registries in main.ts onInit()
container.bind(coreshopNotificationServiceIds.notificationRuleConditionRegistry)
    .to(ConditionRegistry).inSingletonScope()
container.bind(coreshopNotificationServiceIds.notificationRuleActionRegistry)
    .to(ActionRegistry).inSingletonScope()

// Schema components are registered in NotificationRuleManager via:
// registerSchemaComponentsFromMaps(conditionRegistry, actionRegistry, ...)
```

### Service IDs

```typescript
// src/CoreShop/Bundle/NotificationBundle/.../service-ids.ts
export const coreshopNotificationServiceIds = {
  notificationRuleConditionRegistry: Symbol.for('coreshop.notification.notification_rule.condition_registry'),
  notificationRuleActionRegistry: Symbol.for('coreshop.notification.notification_rule.action_registry')
}
```

## Built-in Conditions (All Schema-Driven)

### Order Notification Conditions

| Key | Description |
|-----|-------------|
| `order.orderState` | Order workflow state |
| `order.orderTransition` | Order state transition |
| `order.orderPaymentState` | Payment state of order |
| `order.orderPaymentTransition` | Payment state transition |
| `order.orderShippingState` | Shipping state of order |
| `order.orderShippingTransition` | Shipping state transition |
| `order.orderInvoiceState` | Invoice state of order |
| `order.orderInvoiceTransition` | Invoice state transition |
| `order.saleState` | General sale state |
| `order.carriers` | Specific carriers |
| `order.payment` | Payment provider condition |
| `order.comment` | Order has comment |
| `order.backendCreated` | Order created from backend |

### Payment Notification Conditions

| Key | Description |
|-----|-------------|
| `payment.paymentState` | Payment state |
| `payment.paymentTransition` | Payment state transition |

### Invoice Notification Conditions

| Key | Description |
|-----|-------------|
| `invoice.invoiceState` | Invoice state |
| `invoice.invoiceTransition` | Invoice state transition |

### Shipment Notification Conditions

| Key | Description |
|-----|-------------|
| `shipment.shipmentState` | Shipment state |
| `shipment.shipmentTransition` | Shipment state transition |

### Quote Notification Conditions

| Key | Description |
|-----|-------------|
| `quote.quoteState` | Quote state |
| `quote.quoteTransition` | Quote state transition |

### User / Messaging Notification Conditions

| Key | Description |
|-----|-------------|
| `user.userType` | Type of user event (register, password reset, etc.) |
| `messaging.messageType` | Type of message (contact, inquiry, etc.) |
| `{type}.stores` | Store condition (available for all types) |

## Built-in Actions

### Mail Actions

| Key | Description |
|-----|-------------|
| `mail` | Basic email with language-specific templates |
| `storeMail` | Store-specific email templates |
| `orderMail` | Order-specific email with order data |
| `storeOrderMail` | Combined store and order email |

All mail actions are registered for each notification type:
- `order.mail`, `order.storeMail`, `order.orderMail`, `order.storeOrderMail`
- `payment.mail`, `payment.storeMail`, etc.

## Adding a Custom Condition (Schema-Driven — Preferred)

### Step 1: Create the PHP FormType

```php
<?php

namespace App\Form\Type\Notification\Condition;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class MinAmountConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('minAmount', IntegerType::class, [
            'label' => 'Minimum Amount',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'app_notification_condition_min_amount';
    }
}
```

### Step 2: Register the Service

```yaml
services:
    App\CoreShop\Notification\Condition\MinAmountConditionChecker:
        tags:
            - { name: coreshop.notification_rule.condition, type: order.minAmount, form-type: App\Form\Type\Notification\Condition\MinAmountConditionType }
```

### Step 3: Done

No React/TypeScript code needed. The condition form is rendered automatically.

### When You Need Custom JS (Rare)

If your condition requires specialized UI that cannot be expressed as a FormType:

```typescript
// In your bundle's main.ts
import { coreshopNotificationServiceIds } from '@coreshop/notification/src/modules/notification-rules/service-ids'

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
```

## Adding a Custom Action

### Schema-Driven (Preferred)

Same pattern as conditions — create a PHP FormType and register with `form-type` tag:

```yaml
services:
    App\CoreShop\Notification\Action\WebhookActionProcessor:
        tags:
            - { name: coreshop.notification_rule.action, type: webhook, form-type: App\Form\Type\WebhookActionType }
```

### Hand-Written (When Needed)

```typescript
const actionRegistry = container.get<ActionRegistry>(
    coreshopNotificationServiceIds.notificationRuleActionRegistry
)

// Register for all notification types
const notificationTypes = ['order', 'payment', 'invoice', 'shipment', 'quote', 'user', 'messaging']
notificationTypes.forEach(type => {
    actionRegistry.register(`${type}.webhook`, WebhookAction)
})
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
        return $subject->getTotal() >= ($minAmount * 100);
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
        if (!$url) return;

        $this->httpClient->request($configuration['method'] ?? 'POST', $url, [
            'json' => $this->serializeSubject($subject)
        ]);
    }
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
│       │   └── index.ts                 # Empty — all schema-generated
│       └── actions/
│           └── index.ts                 # Empty — all schema-generated

CoreBundle/Resources/assets/pimcore-studio/src/modules/extension/notification-rules/
├── index.tsx                            # Placeholder module (all conditions now schema-generated)
└── api/
    └── workflow-api.ts                  # Workflow API for state/transition data
```

## Best Practices

1. **Use schema-driven forms**: All standard conditions/actions should use PHP FormTypes
2. **Use type prefixes**: Always prefix conditions/actions with the notification type
3. **Consider all notification types**: Register actions for all relevant notification types
4. **Test state transitions**: Verify conditions work for both states and transitions
5. **Document email placeholders**: Document available placeholders for email templates

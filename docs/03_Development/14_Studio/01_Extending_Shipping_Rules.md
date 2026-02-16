# Extending Shipping Rules in Pimcore Studio v2

This guide explains how to extend Shipping Rules with custom Actions and Conditions in the new Pimcore Studio (React/TypeScript).

## Overview

Shipping Rules in CoreShop allow you to define conditions under which shipping costs are calculated. The system consists of:

- **Conditions**: Define when a shipping rule applies (e.g., weight, amount, postcodes)
- **Actions**: Define how the shipping cost is calculated (e.g., fixed price, percentage discount)

## Schema-Driven Forms (No Custom JS Needed)

**Since the StudioFormBundle integration, most shipping rule conditions and actions no longer require custom React components.** The configuration forms are rendered automatically from PHP FormTypes.

To add a new condition or action, you only need:

1. A PHP FormType
2. A service registration with the `form-type` attribute

The frontend auto-generates the React form from the backend schema at runtime.

### Adding a Custom Condition (PHP Only)

**Step 1: Create the PHP FormType:**

```php
<?php

namespace App\Form\Type\Rule\Condition;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class VolumeConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minVolume', NumberType::class, [
                'label' => 'app_condition_min_volume',
                'required' => false,
                'scale' => 0,
            ])
            ->add('maxVolume', NumberType::class, [
                'label' => 'app_condition_max_volume',
                'required' => false,
                'scale' => 0,
            ])
            ->add('unit', ChoiceType::class, [
                'label' => 'app_volume_unit',
                'choices' => [
                    'Cubic cm' => 'cm3',
                    'Cubic m' => 'm3',
                    'Liters' => 'l',
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_shipping_rule_condition_volume';
    }
}
```

**Step 2: Register the service:**

```yaml
services:
    app.shipping_rule.condition.volume:
        class: App\Rule\Condition\VolumeConditionChecker
        tags:
            - name: coreshop.shipping_rule.condition
              type: volume
              form-type: App\Form\Type\Rule\Condition\VolumeConfigurationType
```

**Step 3: Done.** No React code needed. The condition form renders automatically in the Shipping Rule editor.

### Adding a Custom Action (PHP Only)

```php
<?php

namespace App\Form\Type\Rule\Action;

use CoreShop\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomSurchargeConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('surcharge', IntegerType::class, [
                'label' => 'app_action_surcharge',
            ])
            ->add('currency', CurrencyChoiceType::class, [
                'label' => 'app_currency',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_shipping_rule_action_custom_surcharge';
    }
}
```

```yaml
services:
    app.shipping_rule.action.custom_surcharge:
        class: App\Rule\Action\CustomSurchargeActionProcessor
        tags:
            - name: coreshop.shipping_rule.action
              type: customSurcharge
              form-type: App\Form\Type\Rule\Action\CustomSurchargeConfigurationType
```

### How It Works Behind the Scenes

1. The `RegisterFormTypesFromTagsPass` compiler pass collects the `form-type` attribute from all `coreshop.shipping_rule.condition` and `coreshop.shipping_rule.action` tags
2. These form types are registered in the `BlockPrefixFormTypeRegistry`
3. The `get-config` endpoint returns `conditionSchemaByType` / `actionSchemaByType` maps with embedded schemas
4. The frontend calls `registerSchemaComponentsFromConfig()` which auto-generates React components using `SchemaForm`
5. `preSeedSchemaCache()` loads all schemas in one request, avoiding per-type HTTP calls

For the full pattern explanation, see [StudioFormBundle Examples — Example 13](02_Base_Infrastructure/05_StudioFormBundle_Examples.md#example-13--rule-conditionaction-as-schema-form).

## Architecture

### Separate Registries

Each rule engine (Cart Price Rules, Product Price Rules, Shipping Rules) has **separate ConditionRegistry and ActionRegistry instances**:

```typescript
// ShippingBundle creates its own registries
container.bind(coreshopShippingServiceIds.shippingRuleConditionRegistry)
    .to(ConditionRegistry).inSingletonScope()
container.bind(coreshopShippingServiceIds.shippingRuleActionRegistry)
    .to(ActionRegistry).inSingletonScope()
```

### Bundle Responsibilities

- **ShippingBundle**: Creates registries; registers special hand-written components (if any)
- **CoreBundle**: Registers the `NestedCondition` component (the only hand-written component needed across rule types)
- **Schema system**: Automatically generates and registers all other condition/action forms from PHP FormTypes

## Built-in Shipping Rule Components

### Conditions (ShippingBundle — all schema-driven)
- `weight` - Weight-based condition
- `amount` - Cart value condition
- `postcodes` - Postcode validation
- `dimension` - Package dimensions (width, height, depth)
- `shippingRule` - Reference other shipping rules

### Conditions (Shared — schema-driven)
- `categories` - Product categories
- `products` - Specific products
- `customers` - Specific customers
- `customerGroups` - Customer groups
- `guest` - Guest checkout
- `countries` - Destination countries
- `zones` - Shipping zones
- `stores` - Store selection
- `currencies` - Currency selection

### Conditions (Hand-written — require custom JS)
- `nested` - Nested conditions (AND/OR logic) — requires recursive UI
- `timespan` - Time-based conditions — uses custom date picker composition

### Actions (ShippingBundle — all schema-driven)
- `price` - Fixed price
- `additionPercent` - Percentage surcharge
- `additionAmount` - Fixed amount surcharge
- `discountPercent` - Percentage discount
- `discountAmount` - Fixed amount discount
- `shippingRule` - Trigger another shipping rule

## When You Still Need Custom React Components

In rare cases where the configuration UI cannot be expressed as a standard Symfony FormType (e.g., recursive nesting, drag-and-drop, complex multi-step wizards), you can still write a hand-written React component:

```typescript
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'

export const MySpecialCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => {
  // Custom interactive UI that can't be expressed as a FormType
  return (/* ... */)
}

// Register in main.ts — hand-written components take priority over schema-generated ones
const conditionRegistry = container.get<ConditionRegistry>(
    coreshopShippingServiceIds.shippingRuleConditionRegistry
)
conditionRegistry.register('mySpecial', MySpecialCondition)
```

**Important: Data Handling** — Actions/conditions receive `data` as the configuration object directly, NOT `data.configuration`.

## Backend Implementation

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

### PHP Condition Checker

```php
<?php

namespace App\CoreShop\Condition;

use CoreShop\Component\Shipping\Rule\Condition\ShippingConditionCheckerInterface;

class VolumeConditionChecker implements ShippingConditionCheckerInterface
{
    public function isValid($carrier, $cartItem, array $configuration, array $context): bool
    {
        $volume = $this->calculateVolume($cartItem);
        $min = $configuration['minVolume'] ?? 0;
        $max = $configuration['maxVolume'] ?? PHP_INT_MAX;

        return $volume >= $min && $volume <= $max;
    }
}
```

## Testing Your Extension

1. **Clear the Symfony cache**: `docker compose exec php bin/console cache:clear`
2. **Clear browser cache**: Hard refresh (Ctrl+Shift+R / Cmd+Shift+R)
3. **Test the action/condition**:
   - Navigate to CoreShop -> Shipping -> Shipping Rules
   - Create a new rule
   - Add your custom action/condition
   - Verify the form displays correctly
   - Save and verify the data persists

## Best Practices

1. **Prefer PHP FormTypes**: Use the schema-driven approach for all new conditions/actions
2. **Use appropriate Symfony form types**: `ChoiceType` for selects, `NumberType` for numbers, `PimcoreRelationType` for Pimcore relations
3. **Use FormTypeExtensions**: To add cross-bundle fields (e.g., CoreBundle adding stores/currencies to other bundles' forms)
4. **Only write React for truly special UIs**: Recursive nesting, drag-and-drop, real-time validation that can't be server-driven
5. **Test with the schema endpoint**: `GET /pimcore-studio/api/coreshop-studio-form/schema/{blockPrefix}` to verify your form generates correctly

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

## Schema-Driven Forms (No Custom JS Needed)

**Quantity price rule conditions no longer require custom React components.** The configuration forms are rendered automatically from PHP FormTypes via the StudioFormBundle.

To add a new condition, you only need:

1. A PHP FormType defining the configuration fields
2. A service registration with the `form-type` attribute

The frontend auto-generates the React form from the backend schema at runtime.

### Adding a Custom Condition (PHP Only)

```php
<?php

namespace App\Form\Type\Rule\Condition;

use CoreShop\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

final class MinCartValueConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minValue', NumberType::class, [
                'label' => 'app_condition_min_value',
                'required' => true,
                'scale' => 2,
            ])
            ->add('currency', CurrencyChoiceType::class, [
                'label' => 'app_currency',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_quantity_price_rule_condition_min_cart_value';
    }
}
```

```yaml
services:
    app.quantity_price_rule.condition.min_cart_value:
        class: App\Rule\Condition\MinCartValueConditionChecker
        tags:
            - name: coreshop.product_quantity_price_rules.condition
              type: minCartValue
              form-type: App\Form\Type\Rule\Condition\MinCartValueConfigurationType
```

**Done.** No React/TypeScript code needed. The condition form renders automatically in the Quantity Price Rule editor.

For the full schema-driven pattern explanation, see [StudioFormBundle Examples — Example 13](../02_Base_Infrastructure/05_StudioFormBundle_Examples.md#example-13--rule-conditionaction-as-schema-form).

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
- **CoreBundle**: Registers `NestedCondition` (the only hand-written component)
- **Schema system**: Automatically generates and registers all other condition forms at runtime

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

## Built-in Conditions

### Conditions (All schema-driven from PHP FormTypes)

| Key | Source Bundle | Description |
|-----|-------------|-------------|
| `categories` | CoreBundle | Product categories |
| `products` | CoreBundle | Specific products |
| `customers` | CoreBundle | Specific customers |
| `customerGroups` | CoreBundle | Customer groups |
| `guest` | CoreBundle | Guest checkout |
| `countries` | CoreBundle | Customer countries |
| `zones` | CoreBundle | Geographic zones |
| `stores` | CoreBundle | Store selection |
| `currencies` | CoreBundle | Currency selection |

### Conditions (Hand-written — require custom JS)

| Key | Source Bundle | Reason |
|-----|-------------|--------|
| `nested` | CoreBundle | Recursively renders sub-conditions with AND/OR logic |
| `timespan` | CoreBundle | Uses custom date/time picker composition |

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

        return $cartValue >= $minValue;
    }
}
```

## Testing Your Extension

1. **Clear the Symfony cache**: `docker compose exec php bin/console cache:clear`
2. **Clear browser cache**: Hard refresh (Ctrl+Shift+R)
3. **Test the condition**:
   - Open a Product Data Object
   - Find the Quantity Price Rules field
   - Create a new rule
   - Add your custom condition
   - Verify the form displays correctly
   - Save the Data Object and verify persistence

## Best Practices

1. **Prefer PHP FormTypes**: Use the schema-driven approach for all new conditions
2. **Conditions Only**: Remember Quantity Price Rules don't have actions — pricing is defined via ranges
3. **Unit Awareness**: If products have unit definitions, consider them in your conditions
4. **Currency Handling**: For amount-based conditions, use `CurrencyChoiceType` which renders as a select automatically
5. **Test with the schema endpoint**: `GET /pimcore-studio/api/coreshop-studio-form/schema/{blockPrefix}` to verify your form generates correctly

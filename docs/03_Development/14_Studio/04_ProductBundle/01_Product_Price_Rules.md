# Extending Product Price Rules in Pimcore Studio v2

This guide explains how to extend Product Price Rules with custom Actions and Conditions in Pimcore Studio (React/TypeScript).

## Overview

Product Price Rules in CoreShop allow you to define dynamic pricing based on various conditions. The system consists of:

- **Conditions**: Define when a price rule applies (e.g., customer group, time period, product category)
- **Actions**: Define how the price is calculated (e.g., fixed price, percentage discount, amount discount)

### Product Price Rules vs Product Specific Price Rules

CoreShop has two types of product-related price rules:

| Type | Scope | Use Case |
|------|-------|----------|
| **Product Price Rules** | Global rules applied to all matching products | "10% off all electronics for VIP customers" |
| **Product Specific Price Rules** | Rules attached to individual products via Pimcore Data Object | "This specific laptop is 15% off until December" |

Both rule types share the same conditions and actions, but differ in how they're managed.

## Schema-Driven Forms (No Custom JS Needed)

**Most product price rule conditions and actions no longer require custom React components.** The configuration forms are rendered automatically from PHP FormTypes via the StudioFormBundle.

To add a new condition or action, you only need:

1. A PHP FormType defining the configuration fields
2. A service registration with the `form-type` attribute

The frontend auto-generates the React form from the backend schema at runtime.

### Adding a Custom Action (PHP Only)

```php
<?php

namespace App\Form\Type\Rule\Action;

use CoreShop\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomDiscountConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('discount', NumberType::class, [
                'label' => 'app_action_discount',
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
        return 'app_product_price_rule_action_custom_discount';
    }
}
```

```yaml
services:
    app.product_price_rule.action.custom_discount:
        class: App\Rule\Action\CustomDiscountActionProcessor
        tags:
            - name: coreshop.product_price_rule.action
              type: customDiscount
              form-type: App\Form\Type\Rule\Action\CustomDiscountConfigurationType
```

**Done.** No React/TypeScript code needed. The action form renders automatically in the Product Price Rule editor.

To also register for Product Specific Price Rules, add a second tag:

```yaml
            - name: coreshop.product_specific_price_rule.action
              type: customDiscount
              form-type: App\Form\Type\Rule\Action\CustomDiscountConfigurationType
```

### Adding a Custom Condition (PHP Only)

```php
<?php

namespace App\Form\Type\Rule\Condition;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class MinOrderQuantityConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minQuantity', IntegerType::class, [
                'label' => 'app_condition_min_quantity',
            ])
            ->add('maxQuantity', IntegerType::class, [
                'label' => 'app_condition_max_quantity',
                'required' => false,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_product_price_rule_condition_min_order_quantity';
    }
}
```

```yaml
services:
    app.product_price_rule.condition.min_order_quantity:
        class: App\Rule\Condition\MinOrderQuantityChecker
        tags:
            - name: coreshop.product_price_rule.condition
              type: minOrderQuantity
              form-type: App\Form\Type\Rule\Condition\MinOrderQuantityConfigurationType
```

For the full schema-driven pattern explanation, see [StudioFormBundle Examples — Example 13](../02_Base_Infrastructure/05_StudioFormBundle_Examples.md#example-13--rule-conditionaction-as-schema-form).

## Architecture

### Separate Registries

Each rule engine has **separate ConditionRegistry and ActionRegistry instances**:

```typescript
// ProductBundle creates its own registries
container.bind(coreshopProductServiceIds.productPriceRuleConditionRegistry)
    .to(ConditionRegistry).inSingletonScope()
container.bind(coreshopProductServiceIds.productPriceRuleActionRegistry)
    .to(ActionRegistry).inSingletonScope()

// Separate registries for Product Specific Price Rules
container.bind(coreshopProductServiceIds.productSpecificPriceRuleConditionRegistry)
    .to(ConditionRegistry).inSingletonScope()
container.bind(coreshopProductServiceIds.productSpecificPriceRuleActionRegistry)
    .to(ActionRegistry).inSingletonScope()
```

### Bundle Responsibilities

- **ProductBundle**: Creates registries; all condition/action forms are schema-driven from PHP FormTypes
- **CoreBundle**: Registers `NestedCondition` (the only hand-written component) across all rule type registries
- **Schema system**: Automatically generates and registers all other condition/action forms at runtime

### Service IDs

```typescript
// src/CoreShop/Bundle/ProductBundle/.../service-ids.ts
export const coreshopProductServiceIds = {
  productPriceRuleConditionRegistry: Symbol.for('coreshop.product.product_price_rule.condition_registry'),
  productPriceRuleActionRegistry: Symbol.for('coreshop.product.product_price_rule.action_registry'),
  productSpecificPriceRuleConditionRegistry: Symbol.for('coreshop.product.product_specific_price_rule.condition_registry'),
  productSpecificPriceRuleActionRegistry: Symbol.for('coreshop.product.product_specific_price_rule.action_registry')
}
```

## Built-in Product Price Rule Components

### Conditions (All schema-driven from PHP FormTypes)

| Key | Source Bundle | Description |
|-----|-------------|-------------|
| `weight` | ProductBundle | Product weight range (min/max) |
| `categories` | CoreBundle | Product categories |
| `products` | CoreBundle | Specific products |
| `customers` | CoreBundle | Specific customers |
| `customerGroups` | CoreBundle | Customer groups |
| `guest` | CoreBundle | Guest checkout |
| `countries` | CoreBundle | Customer countries |
| `zones` | CoreBundle | Geographic zones |
| `stores` | CoreBundle | Store selection |
| `currencies` | CoreBundle | Currency selection |
| `quantity` | CoreBundle | Cart quantity range |
| `not_combinable_with_cart_price_voucher_rule` | CoreBundle | Voucher exclusion |

### Conditions (Hand-written — require custom JS)

| Key | Source Bundle | Reason |
|-----|-------------|--------|
| `nested` | CoreBundle | Recursively renders sub-conditions with AND/OR logic |
| `timespan` | CoreBundle | Uses custom date/time picker composition |

### Actions (All schema-driven from PHP FormTypes)

| Key | Source Bundle | Description |
|-----|-------------|-------------|
| `discountAmount` | ProductBundle | Fixed amount discount |
| `discountPercent` | ProductBundle | Percentage discount |
| `price` | ProductBundle | Fixed price override |
| `discountPrice` | ProductBundle | Discounted fixed price |
| `notDiscountableCustomAttributes` | ProductBundle | Marks products as non-discountable |

## Hand-Written React Components (Rare)

If your condition/action needs custom interactive behavior that cannot be expressed as a Symfony FormType, you can still write a React component:

```typescript
import React from 'react'
import { Form, InputNumber } from 'antd'
import type { ActionComponentProps } from '@coreshop/rule/src/rules'

export const CustomDiscountAction: React.FC<ActionComponentProps> = ({
  data,
  onChange
}) => {
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

Register in your bundle's `main.ts` — hand-written components take priority over schema-generated ones:

```typescript
const actionRegistry = container.get<ActionRegistry>(
    coreshopProductServiceIds.productPriceRuleActionRegistry
)
actionRegistry.register('customDiscount', CustomDiscountAction)
```

**Important: Data Handling** — Actions/conditions receive `data` as the configuration object directly, NOT `data.configuration`.

## Backend Implementation

### PHP Action Processor

```php
<?php

namespace App\CoreShop\Action;

use CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface;
use CoreShop\Component\Core\Model\ProductInterface;

class CustomDiscountActionProcessor implements ProductPriceActionProcessorInterface
{
    public function getPrice(
        ProductInterface $product,
        array $context,
        array $configuration
    ): int {
        $originalPrice = $product->getStorePrice($context['store']);
        $discount = $configuration['discount'] ?? 0;

        return max(0, $originalPrice - $discount);
    }
}
```

### Service Registration

```yaml
# config/services.yaml
App\CoreShop\Action\CustomDiscountActionProcessor:
  tags:
    - { name: coreshop.product_price_rule.action, type: customDiscount, form-type: App\Form\Type\CustomDiscountType }
```

## File Structure

```
ProductBundle/Resources/assets/pimcore-studio/src/
├── main.ts                              # Plugin entry, registry setup
├── modules/
│   ├── product-price-rules/
│   │   ├── service-ids.ts               # Registry service IDs
│   │   ├── types.ts                     # TypeScript types
│   │   ├── api.ts                       # API client
│   │   ├── ProductPriceRuleManager.tsx  # Main manager widget
│   │   └── components/
│   │       └── SettingsForm.tsx
│   └── product-specific-price-rules/
│       ├── types.ts
│       ├── components/
│       │   ├── SettingsForm.tsx
│       │   └── ProductSpecificPriceRulesPanel.tsx
│       └── index.ts
├── components/
│   ├── CategoryMultiSelect.tsx
│   ├── ProductMultiSelect.tsx
│   └── ProductUnitSelect.tsx
└── dynamic-types/
    ├── index.ts
    └── DynamicTypeObjectDataCoreShopProductSpecificPriceRules.tsx
```

Note: There are no `conditions/` or `actions/` subdirectories — all condition/action forms are generated from PHP FormTypes at runtime.

## Testing Your Extension

1. **Clear the Symfony cache**: `docker compose exec php bin/console cache:clear`
2. **Clear browser cache**: Hard refresh (Ctrl+Shift+R / Cmd+Shift+R)
3. **Test the action/condition**:
   - Navigate to CoreShop -> Product -> Product Price Rules
   - Create a new rule
   - Add your custom action/condition
   - Verify the form displays correctly
   - Save and verify the data persists

## Best Practices

1. **Prefer PHP FormTypes**: Use the schema-driven approach for all new conditions/actions
2. **Use appropriate Symfony form types**: `ChoiceType` for selects, `NumberType` for numbers, `PimcoreRelationType` for Pimcore relations
3. **Test with both rule types**: Ensure your extension works for both Product Price Rules and Product Specific Price Rules
4. **Only write React for truly special UIs**: Recursive nesting, drag-and-drop, etc.
5. **Test with the schema endpoint**: `GET /pimcore-studio/api/coreshop-studio-form/schema/{blockPrefix}` to verify your form generates correctly

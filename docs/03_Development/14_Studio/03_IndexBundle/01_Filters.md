# Filters in Studio v2

Filters allow you to configure search and filtering capabilities for your indexed product data. They define which fields users can filter by in the frontend.

## Overview

The Filter system in CoreShop Studio v2 consists of:

- **FilterManager**: Main UI component for managing filters
- **Filter Settings**: Basic configuration (name, index, ordering, pagination)
- **Pre-Conditions**: Backend filters that always apply
- **User Conditions**: Frontend filters that users can interact with

## Architecture

### Schema-Driven Conditions

Like all rule types in CoreShop, filter conditions are **schema-driven** — their forms are rendered automatically from PHP FormTypes. Only `NestedCondition` requires a hand-written React component (for recursive sub-condition rendering).

All other condition types (range, select, multiselect, boolean, search) are auto-generated from their backend FormTypes via `registerFilterSchemaConditions`.

### Components Structure

```
IndexBundle/Resources/assets/pimcore-studio/src/modules/filters/
├── FilterManager.tsx         # Main manager using EntityTabbedManager
├── FilterDetail.tsx          # Tab container (Settings, Pre-Conditions, Conditions)
├── api.ts                    # FilterApi extending EntityApi
├── types.ts                  # TypeScript interfaces
├── service-ids.ts            # DI container service IDs
├── components/
│   ├── SettingsForm.tsx      # Basic filter settings
│   ├── ConditionsPanel.tsx   # Generic conditions panel
│   ├── ConditionItem.tsx     # Individual condition renderer
│   └── EmptyCondition.tsx    # Placeholder for unimplemented types
└── conditions/
    ├── ConditionRegistry.ts  # Registry for condition components
    ├── NestedCondition.tsx   # Hand-written: recursive condition groups
    └── index.ts              # Exports ConditionRegistry + NestedCondition
```

## API

### FilterApi

The FilterApi extends the ResourceBundle's EntityApi with filter-specific methods:

```typescript
import { filterApi } from './modules/filters/api'

// Standard CRUD operations (from EntityApi)
await filterApi.list()
await filterApi.get(id)
await filterApi.add(data)
await filterApi.save(id, data)
await filterApi.delete(id)
await filterApi.clone(id)

// Filter-specific methods
const config = await filterApi.getConfig()
// Returns: { success: true, pre_conditions: ['range', 'select', ...], user_conditions: [...] }

const fields = await filterApi.getFieldsForIndex(indexId)
// Returns: [{ name: 'price' }, { name: 'manufacturer' }, ...]

const values = await filterApi.getValuesForFilterField(indexId, 'manufacturer')
// Returns: [{ key: 1, value: 'Apple' }, { key: 2, value: 'Samsung' }, ...]
```

## Filter Model

```typescript
interface Filter {
  id?: number
  name: string
  resultsPerPage?: number
  orderDirection?: 'asc' | 'desc'
  orderKey?: string
  index?: number | null
  preConditions?: FilterCondition[]
  conditions?: FilterCondition[]
}

interface FilterCondition {
  id?: number
  type: string
  field?: string
  label?: string
  quantityUnit?: number
  configuration?: Record<string, any>
  sort?: number
}
```

## Condition Types

All condition types below are schema-generated from PHP FormTypes. They don't have hand-written React components.

### 1. Range Condition

For numeric ranges like price, weight, etc.

**Configuration:**
- `field`: Index field to filter
- `label`: Display label
- `stepCount`: Number of slider steps
- `preSelectMin`: Default minimum value
- `preSelectMax`: Default maximum value
- `quantityUnit`: Unit ID for quantity values

**Use Case:** Price filter ($10 - $100), Weight filter (1kg - 10kg)

### 2. Select Condition

Single-select dropdown filter.

**Configuration:**
- `field`: Index field to filter
- `label`: Display label
- `preSelect`: Default selected value
- `quantityUnit`: Unit ID

**Use Case:** Single manufacturer selection, Single color selection

### 3. Multiselect Condition

Multiple-select dropdown filter.

**Configuration:**
- `field`: Index field to filter
- `label`: Display label
- `preSelects`: Array of default selected values
- `quantityUnit`: Unit ID

**Use Case:** Multiple manufacturers, Multiple colors

### 4. Boolean Condition

Yes/No checkbox filter.

**Configuration:**
- `field`: Index field to filter
- `label`: Display label

**Use Case:** In stock filter, New products filter, On sale filter

### 5. Search Condition

Full-text search across multiple fields.

**Configuration:**
- `fields`: Array of index fields to search
- `label`: Display label
- `name`: Input name attribute
- `minLength`: Minimum query length
- `quantityUnit`: Unit ID

**Use Case:** Product name/description search

### 6. Nested Condition (Hand-Written)

Recursive container for grouping conditions. This is the **only** hand-written React component because it recursively renders sub-conditions with the registry.

**Configuration:**
- `label`: Display label for the group
- `conditions`: Array of nested FilterCondition objects

**Use Case:** Complex filter logic with grouped conditions

## Registering Filter Conditions

Conditions are registered in `main.ts` using separate registries for pre-conditions and user-conditions:

```typescript
import { container } from '@pimcore/studio-ui-bundle'
import { ConditionRegistry, NestedCondition } from './modules/filters/conditions'
import { serviceIds } from './modules/filters/service-ids'

// Create and bind registries
container.bind(serviceIds.preConditionRegistry).to(ConditionRegistry).inSingletonScope()
container.bind(serviceIds.userConditionRegistry).to(ConditionRegistry).inSingletonScope()

// Get registries
const preConditionRegistry = container.get<ConditionRegistry>(serviceIds.preConditionRegistry)
const userConditionRegistry = container.get<ConditionRegistry>(serviceIds.userConditionRegistry)

// Register the only hand-written condition
preConditionRegistry.register('nested', NestedCondition)
userConditionRegistry.register('nested', NestedCondition)

// All other conditions (range, select, multiselect, boolean, search) are registered
// at runtime via registerFilterSchemaConditions() when FilterManager loads the config
```

## Custom Widgets

IndexBundle provides custom widgets for filter-specific field selection:

- `FilterFieldSelect` — select a single index field
- `FilterFieldsMultiSelect` — select multiple index fields
- `FilterValueSelect` — select a value from an index field
- `FilterValueMultiSelect` — select multiple values from an index field

These are registered in the StudioFormBundle WidgetRegistry and used by the schema-generated condition forms.

## Creating Custom Condition Types

### Schema-Driven (Preferred — No React Code)

Create a PHP FormType and register with the `form-type` tag:

```php
<?php

namespace App\Form\Type\Filter\Condition;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('customValue', TextType::class, ['label' => 'Custom Value']);
    }

    public function getBlockPrefix(): string
    {
        return 'app_filter_condition_custom';
    }
}
```

```yaml
services:
    app.filter.condition.custom:
        class: App\Filter\Condition\CustomProcessor
        tags:
            - { name: coreshop.filter.condition_type, type: custom, form-type: App\Form\Type\Filter\Condition\CustomConditionType }
```

### Hand-Written (When Needed)

For conditions needing custom UI (rare):

```typescript
// In your bundle's main.ts
const preConditionRegistry = container.get<ConditionRegistry>(serviceIds.preConditionRegistry)
const userConditionRegistry = container.get<ConditionRegistry>(serviceIds.userConditionRegistry)

preConditionRegistry.register('myCustomCondition', MyCustomCondition)
userConditionRegistry.register('myCustomCondition', MyCustomCondition)
```

## Widget Registration

Filters are automatically available in the menu through the MenuBundle integration. The widget is registered in `main.ts`:

```typescript
import { FilterManager } from './modules/filters/FilterManager'

// Register widget
const widgetManager = container.get<WidgetRegistry>(pimcoreServiceIds.widgetManager)
widgetManager.registerWidget({
    name: 'coreshop-index-filter',
    component: FilterManager
})
```

**Important:** The widget name must match the backend menu configuration:

**Backend (IndexMenuBuilder.php):**
```php
$menuItem
    ->addChild('coreshop_filters')
    ->setLabel('coreshop_filters')
    ->setAttribute('permission', 'coreshop_permission_filter')
    ->setAttribute('iconCls', 'coreshop_nav_icon_filters')
    ->setAttribute('resource', 'coreshop.index')    // Resource name
    ->setAttribute('function', 'filter')            // Function name
;
```

The MenuBundle automatically creates the widget ID from: `{resource}-{function}` = `coreshop-index-filter`

## Pre-Conditions vs User Conditions

### Pre-Conditions
- **Purpose**: Backend filters that always apply
- **Visibility**: Not visible to frontend users
- **Use Case**: Restrict to specific categories, exclude disabled products, apply store-specific filters

### User Conditions
- **Purpose**: Frontend filters users can interact with
- **Visibility**: Visible as filter UI in frontend
- **Use Case**: Price range, manufacturer selection, color filters, search

Both use the same condition types and registry but serve different purposes in the filtering pipeline.

## Best Practices

1. **Index Selection**: Always select an index before adding conditions
2. **Field Names**: Use exact index field names for filter fields
3. **Labels**: Provide clear, user-friendly labels for all conditions
4. **Pre-Select Values**: Use sparingly to guide users without restricting them
5. **Nested Conditions**: Use for complex filter logic, but avoid excessive nesting
6. **Quantity Units**: Set appropriate quantity units for measurements
7. **Prefer schema-driven**: Use PHP FormTypes for new condition types

## Future Enhancements

Not yet implemented condition types (show EmptyCondition placeholder):
- `category_select` / `category_multiselect`
- `relational_select` / `relational_multiselect`
- `select_from_multiselect`
- `multiselect_from_multiselect`

These will be implemented as needed based on backend support and requirements.

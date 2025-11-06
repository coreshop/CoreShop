# Filters in Studio v2

Filters allow you to configure search and filtering capabilities for your indexed product data. They define which fields users can filter by in the frontend.

## Overview

The Filter system in CoreShop Studio v2 consists of:

- **FilterManager**: Main UI component for managing filters
- **Filter Settings**: Basic configuration (name, index, ordering, pagination)
- **Pre-Conditions**: Backend filters that always apply
- **User Conditions**: Frontend filters that users can interact with

## Architecture

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
    ├── RangeCondition.tsx    # Price/numeric range filter
    ├── SelectCondition.tsx   # Single-select dropdown
    ├── MultiselectCondition.tsx
    ├── BooleanCondition.tsx  # Yes/No filter
    ├── SearchCondition.tsx   # Full-text search
    └── NestedCondition.tsx   # Nested condition groups
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

### 6. Nested Condition

Recursive container for grouping conditions.

**Configuration:**
- `label`: Display label for the group
- `conditions`: Array of nested FilterCondition objects

**Use Case:** Complex filter logic with grouped conditions

## Registering Filter Conditions

Conditions are registered in `main.ts` using separate registries:

```typescript
import { container } from '@pimcore/studio-ui-bundle'
import { ConditionRegistry } from './modules/filters/conditions'
import { serviceIds } from './modules/filters/service-ids'
import { RangeCondition } from './modules/filters/conditions'

// Create and bind registries
container.bind(serviceIds.preConditionRegistry).to(ConditionRegistry).inSingletonScope()
container.bind(serviceIds.userConditionRegistry).to(ConditionRegistry).inSingletonScope()

// Get registries
const preConditionRegistry = container.get<ConditionRegistry>(serviceIds.preConditionRegistry)
const userConditionRegistry = container.get<ConditionRegistry>(serviceIds.userConditionRegistry)

// Register conditions
preConditionRegistry.register('range', RangeCondition)
userConditionRegistry.register('range', RangeCondition)
```

## Creating Custom Condition Types

To create a custom condition type:

1. **Create the component:**

```typescript
// MyCustomCondition.tsx
import React from 'react'
import { Form, Input } from 'antd'
import type { ConditionProps } from '../types'

export const MyCustomCondition: React.FC<ConditionProps> = ({
  data,
  onChange,
  indexId
}) => {
  return (
    <Form layout="vertical">
      <Form.Item label="Label">
        <Input
          value={data.label}
          onChange={(e) => onChange({ label: e.target.value })}
        />
      </Form.Item>

      <Form.Item label="Custom Field">
        <Input
          value={data.configuration?.customValue}
          onChange={(e) => onChange({
            configuration: {
              ...data.configuration,
              customValue: e.target.value
            }
          })}
        />
      </Form.Item>
    </Form>
  )
}
```

2. **Register the condition:**

```typescript
// In your bundle's main.ts
import { MyCustomCondition } from './conditions/MyCustomCondition'

const preConditionRegistry = container.get<ConditionRegistry>(serviceIds.preConditionRegistry)
const userConditionRegistry = container.get<ConditionRegistry>(serviceIds.userConditionRegistry)

preConditionRegistry.register('myCustomCondition', MyCustomCondition)
userConditionRegistry.register('myCustomCondition', MyCustomCondition)
```

3. **Register backend condition handler:**

The backend must also register the condition processor. See ExtJS documentation for backend configuration.

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

Therefore, the frontend widget must be registered with the exact name `coreshop-index-filter` to match.

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

## Example: Creating a Product Filter

1. **Create Filter**
   - Name: "Product Catalog Filter"
   - Index: Select your product index
   - Results Per Page: 20
   - Order Direction: desc
   - Order Key: name

2. **Add Pre-Conditions** (hidden backend filters)
   - Boolean condition: `enabled = true`
   - Select condition: `store = current_store`

3. **Add User Conditions** (visible frontend filters)
   - Range condition: Price (field: `price`, label: "Price")
   - Multiselect condition: Manufacturers (field: `manufacturer`)
   - Multiselect condition: Colors (field: `color`)
   - Boolean condition: In Stock (field: `inStock`, label: "In Stock Only")
   - Search condition: Product Search (fields: `['name', 'description']`)

## Future Enhancements

Not yet implemented condition types (show EmptyCondition placeholder):
- `category_select` / `category_multiselect`
- `relational_select` / `relational_multiselect`
- `select_from_multiselect`
- `multiselect_from_multiselect`

These will be implemented as needed based on backend support and requirements.

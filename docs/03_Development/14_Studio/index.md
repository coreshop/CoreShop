# Pimcore Studio (React)

CoreShop provides a modern React-based user interface for the Pimcore Studio. This documentation covers the architecture, patterns, and best practices for developing Studio components.

## Overview

The CoreShop Studio implementation uses:
- **React 18** with TypeScript for component development
- **Ant Design** as the UI component library
- **Module Federation** for micro-frontend architecture
- **InversifyJS** for dependency injection
- **Rsbuild** as the build tool

## Architecture

### Bundle-Plugin Pattern

Each CoreShop bundle can provide a Studio plugin that registers components, services, and menu items:

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/main.ts

import { IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'

const plugin: IAbstractPlugin = {
    name: 'your-bundle-name',

    onInit() {
        // Register services, bind registries
        // This runs BEFORE any components are rendered
    },

    onStartup({ moduleSystem }) {
        // Register modules, widgets, menu items
        // This runs during application startup
    }
}

export default plugin
```

### Module Federation

CoreShop uses Webpack Module Federation to share code across bundles:

```typescript
// Importing from other bundles
import { countryApi } from '@coreshop/address/src/modules/countries/api'
import { useEntitySelect } from '@coreshop/resource'
import { ConditionRegistry } from '@coreshop/rule/src/rules/registry'
```

**Key Bundles:**
- `@coreshop/resource` - Base CRUD infrastructure
- `@coreshop/rule` - Rule system (Actions, Conditions, Registries)
- `@coreshop/core` - Shared components and utilities
- `@coreshop/address` - Countries, States, Zones
- `@coreshop/order` - Cart Price Rules, Orders
- `@coreshop/product` - Product Price Rules, Products

### Dependency Injection

Services are managed through InversifyJS container:

```typescript
import { container } from '@pimcore/studio-ui-bundle'
import type { ConditionRegistry } from '@coreshop/rule/src/rules/registry'

// Bind a service
container.bind(serviceIds.myRegistry).to(ConditionRegistry).inSingletonScope()

// Get a service
const registry = container.get<ConditionRegistry>(serviceIds.myRegistry)
```

## Key Concepts

### 1. Entity Management Pattern

Most CRUD features follow the **EntityTabbedManager** pattern:

```
EntityTabbedManager
├── EntityListView (left panel - list of items)
└── EntityDetailView (right panel - form for selected item)
```

**Example:**
```typescript
import { EntityTabbedManager } from '@coreshop/resource'
import { countryApi } from './api'
import { CountryForm } from './CountryForm'

export const CountryManager = () => (
  <EntityTabbedManager
    api={countryApi}
    detailComponent={CountryForm}
  />
)
```

### 2. Rule System Pattern

Rules (Cart Price Rules, Product Price Rules, etc.) use a **Registry Pattern**:

```typescript
// Each rule type has its own registries
const conditionRegistry = container.get<ConditionRegistry>(serviceIds.conditionRegistry)
const actionRegistry = container.get<ActionRegistry>(serviceIds.actionRegistry)

// Register components
conditionRegistry.register('countries', CountriesCondition)
actionRegistry.register('discount', DiscountAction)
```

**Key Components:**
- `RuleManager` - Main rule management UI
- `RuleForm` - Form with Conditions and Actions panels
- `ConditionsPanel` - Manages rule conditions
- `ActionsPanel` - Manages rule actions

### 3. API Layer

All entities use the **EntityApi** base class:

```typescript
import { EntityApi } from '@coreshop/resource/src/entities'

export class CountryApi extends EntityApi<Country> {}

export const countryApi = new CountryApi({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/countries'
})
```

**Available Methods:**
- `list()` - Get all entities
- `get(id)` - Get single entity
- `add(data)` - Create entity
- `save(data)` - Update entity
- `delete(id)` - Delete entity

### 4. Hooks and Utilities

**useEntitySelect** - For entity selection dropdowns:
```typescript
import { useEntitySelect } from '@coreshop/resource'
import { countryApi } from '@coreshop/address/src/modules/countries/api'

const [options, value, handleChange, loading] = useEntitySelect(countryApi, selectedIds)
```

This hook automatically:
- Loads all available entities
- Loads missing entities (for saved selections)
- Prevents duplicates
- Provides loading state

## Bundle Independence

**⚠️ CRITICAL: NO BUNDLE HAS A DEPENDENCY TO COREBUNDLE!**

Individual bundles (ProductBundle, OrderBundle, etc.) MUST NOT import from CoreBundle. Instead:

- Shared components go in appropriate shared bundles
- CoreBundle acts as a **glue layer** that registers shared components into bundle-specific registries
- Use Module Federation aliases for cross-bundle imports

**Example:**
```typescript
// ❌ WRONG - ProductBundle importing from CoreBundle
import { SomeComponent } from '@coreshop/core/...'

// ✅ CORRECT - Import from appropriate bundle
import { SomeComponent } from '@coreshop/rule/src/rules'
```

## Project Structure

```
src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/
├── src/
│   ├── main.ts                          # Plugin entry point
│   ├── modules/
│   │   ├── your-feature/
│   │   │   ├── YourFeatureManager.tsx   # Main component
│   │   │   ├── YourFeatureForm.tsx      # Form component
│   │   │   ├── api.ts                   # API client
│   │   │   └── types.ts                 # TypeScript interfaces
│   │   └── icon-library/
│   │       └── index.ts                 # Icon definitions
│   └── components/                      # Reusable components
├── package.json
├── tsconfig.json
└── rsbuild.config.ts                    # Build configuration
```

## Getting Started

### 1. Create a New Bundle Plugin

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/main.ts

import { IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import type { WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { YourFeatureManager } from './modules/your-feature/YourFeatureManager'

const plugin: IAbstractPlugin = {
    name: 'your-bundle',

    onInit() {
        // Bind services if needed
    },

    onStartup({ moduleSystem }) {
        // Register widget
        const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)
        widgets.registerWidget({
            name: 'your-bundle-feature',
            component: YourFeatureManager
        })
    }
}

export default plugin
```

### 2. Create an Entity Manager

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/your-feature/YourFeatureManager.tsx

import React from 'react'
import { EntityTabbedManager } from '@coreshop/resource'
import { yourFeatureApi } from './api'
import { YourFeatureForm } from './YourFeatureForm'

export const YourFeatureManager: React.FC = () => {
  return (
    <EntityTabbedManager
      api={yourFeatureApi}
      detailComponent={YourFeatureForm}
      listColumns={[
        { key: 'name', title: 'Name', dataIndex: 'name' },
        { key: 'active', title: 'Active', dataIndex: 'active' }
      ]}
    />
  )
}
```

### 3. Create an API Client

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/your-feature/api.ts

import { EntityApi } from '@coreshop/resource/src/entities'
import type { YourFeature } from './types'

export class YourFeatureApi extends EntityApi<YourFeature> {}

export const yourFeatureApi = new YourFeatureApi({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/your-features'
})
```

### 4. Create a Form Component

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/your-feature/YourFeatureForm.tsx

import React from 'react'
import { Form, Input, Switch } from 'antd'
import type { EntityDetailComponentProps } from '@coreshop/resource'
import type { YourFeature } from './types'

export const YourFeatureForm: React.FC<EntityDetailComponentProps<YourFeature>> = ({
  data,
  onChange
}) => {
  return (
    <Form layout="vertical">
      <Form.Item label="Name" required>
        <Input
          value={data?.name || ''}
          onChange={(e) => onChange({ ...data, name: e.target.value })}
        />
      </Form.Item>

      <Form.Item label="Active">
        <Switch
          checked={data?.active || false}
          onChange={(checked) => onChange({ ...data, active: checked })}
        />
      </Form.Item>
    </Form>
  )
}
```

### 5. Build and Deploy

```bash
# Development mode (single bundle)
npm run dev:single YourBundle

# Production build (all bundles)
npm run build

# The output will be in:
# src/CoreShop/Bundle/YourBundle/Resources/public/studio/[build-id]/
```

## Next Steps

- [Bundle Plugin System](01_Bundle_Plugin_System.md) - Deep dive into the plugin architecture
- [Base Infrastructure](02_Base_Infrastructure/01_ResourceBundle.md) - ResourceBundle and RuleBundle details
- [Components](03_Components/01_EntityTabbedManager.md) - Reusable component library
- [Extending Rule Actions](../01_Extending_Guide/04_Extending_Rule_Actions.md) - Creating custom rule actions
- [Extending Rule Conditions](../01_Extending_Guide/05_Extending_Rule_Conditions.md) - Creating custom rule conditions

## Best Practices

### TypeScript

- Always define interfaces for your data types
- Use proper typing for component props
- Leverage type inference where possible

```typescript
interface YourFeature {
  id: number
  name: string
  active: boolean
}

interface YourFeatureFormProps {
  data: YourFeature
  onChange: (data: YourFeature) => void
}
```

### Component Structure

- Keep components focused and single-purpose
- Use composition over inheritance
- Extract reusable logic into hooks

### Performance

- Use React.memo for expensive components
- Implement proper dependency arrays in useEffect
- Lazy load components when appropriate

### Testing

- Write unit tests for business logic
- Use React Testing Library for component tests
- Mock API calls in tests

## Common Patterns

### Loading Data on Mount

```typescript
const [data, setData] = useState([])
const [loading, setLoading] = useState(false)

useEffect(() => {
  const loadData = async () => {
    setLoading(true)
    try {
      const result = await api.list()
      setData(result)
    } catch (error) {
      console.error('Failed to load data:', error)
    } finally {
      setLoading(false)
    }
  }

  loadData()
}, [])
```

### Form Handling

```typescript
const handleChange = (field: string, value: any) => {
  onChange({
    ...data,
    [field]: value
  })
}

<Input
  value={data.name}
  onChange={(e) => handleChange('name', e.target.value)}
/>
```

### Multi-Select with useEntitySelect

```typescript
const selectedIds = data.countries || []
const [options, value, handleSelectChange, loading] = useEntitySelect(
  countryApi,
  selectedIds
)

const handleChange = (ids: number[]) => {
  handleSelectChange(ids)
  onChange({ ...data, countries: ids })
}

<Select
  mode="multiple"
  value={value}
  onChange={handleChange}
  options={options}
  loading={loading}
/>
```

## Troubleshooting

### Module Not Found Errors

If you see module federation errors:
1. Ensure the bundle is built (`npm run build`)
2. Check the import alias is correct (`@coreshop/bundle-name`)
3. Verify the bundle is loaded in the Pimcore Studio

### Registry Not Found

If a registry service is not found:
1. Check the service is bound in `onInit()`
2. Verify the service ID is correct
3. Ensure you're getting it after binding, not before

### Component Not Rendering

If your component doesn't appear:
1. Check the widget is registered in `onStartup()`
2. Verify the widget name matches the menu configuration
3. Check browser console for errors

## Resources

- [React Documentation](https://react.dev/)
- [TypeScript Handbook](https://www.typescriptlang.org/docs/)
- [Ant Design Components](https://ant.design/components/)
- [InversifyJS Documentation](https://inversify.io/)
- [Pimcore Studio UI Bundle](https://github.com/pimcore/studio-ui-bundle)

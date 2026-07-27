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

CoreShop uses Module Federation to share code across bundles:

```typescript
// Importing from other bundles
import { countryApi } from '@coreshop/address/src/modules/countries/api'
import { useEntitySelect } from '@coreshop/resource'
import { ConditionRegistry } from '@coreshop/rule/src/rules/registry'
```

**Key Bundles:**
- `@coreshop/resource` - Base CRUD infrastructure
- `@coreshop/rule` - Rule system (Actions, Conditions, Registries)
- `@coreshop/studio-form` - Schema-driven form system
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
├── EntityList (left panel - tree view of items)
└── EntityTabbedLayout (right panel - tabbed detail forms)
```

**Example:**
```typescript
import { EntityTabbedManager } from '@coreshop/resource'
import { taxRateApi } from './api'

export const TaxRateManager: React.FC = () => {
  const { t } = useTranslation()
  const modal = useFormModal()

  return (
    <EntityTabbedManager<TaxRateDetail>
      api={taxRateApi}
      localizable
      leftRootTitle={t('coreshop_tax_rate')}
      getTitle={(li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}`}
      buildSavePayload={(data) => data}
      onAdd={async () => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop_tax_rate'),
          label: t('coreshop_name'),
          onOk: async (value: string) => {
            const res = await taxRateApi.add({ name: value })
            resolve(res.data.id)
          }
        })
      })}
      renderDetail={(data, setData, ctx) => {
        if (!data) return <div>Select an item...</div>
        return (
          <TaxRateForm
            data={data}
            onChange={(draft) => setData(draft)}
            currentLocale={ctx?.currentLocale ?? 'en'}
            locales={ctx?.locales}
          />
        )
      }}
    />
  )
}
```

For grouped entities (e.g., Countries by Zone), use `GroupedEntityTabbedManager`.

### 2. Rule System Pattern

Rules (Cart Price Rules, Product Price Rules, etc.) use a **Registry Pattern**:

```typescript
// Each rule type has its own registries
const conditionRegistry = container.get<ConditionRegistry>(serviceIds.conditionRegistry)
const actionRegistry = container.get<ActionRegistry>(serviceIds.actionRegistry)

// Register hand-written components (only for special cases)
conditionRegistry.register('nested', NestedCondition)

// Schema-based components are registered at runtime from backend config
registerSchemaComponentsFromConfig(conditionRegistry, actionRegistry, config)
```

**Key Components:**
- `RuleForm` - Form with Settings, Conditions, and Actions tabs
- `ConditionsPanel` - Manages rule conditions
- `ActionsPanel` - Manages rule actions

### 3. Extension System

CoreShop provides 7 extension types for customizing entities without modifying core code. All imported from `@coreshop/resource/src/entities`:

| Type | Service ID | Purpose |
|------|-----------|---------|
| Form Extensions | `entityFormExtensionsServiceId` | Add fields to entity forms |
| Table Column Extensions | `entityTableColumnExtensionsServiceId` | Add columns to nested tables |
| Save Decorators | `entitySaveDecoratorsServiceId` | Transform save payloads |
| Tab Extensions | `entityTabExtensionsServiceId` | Add tabs to entity detail views |
| Action Extensions | `entityActionExtensionsServiceId` | Add toolbar/context-menu/footer buttons |
| Validation Extensions | `entityValidationExtensionsServiceId` | Custom validation before save |
| Lifecycle Hooks | `entityLifecycleHooksServiceId` | beforeLoad/afterLoad/beforeSave/afterSave hooks |

**Slot naming:** `{bundle}.{resource}.{component}` (e.g., `coreshop.address.country.form`)

Register extensions in AbstractModule's `onInit()`, then register module in bundle's `main.ts` via `onStartup({ moduleSystem })`.

### 4. API Layer

All entities use the **EntityApi** base class:

```typescript
import { EntityApi } from '@coreshop/resource/src/entities'

export const countryApi = new EntityApi<CountryDetail>({
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

### 5. Hooks and Utilities

**useEntitySelect** - For entity selection dropdowns:
```typescript
import { useEntitySelect } from '@coreshop/resource'
import { countryApi } from '@coreshop/address/src/modules/countries/api'

const [options, value, handleChange, loading] = useEntitySelect(
  countryApi,
  selectedIds,
  'name'  // optional: label key (default: 'name')
)
```

This hook automatically:
- Loads all available entities
- Normalizes IDs (string to number)
- Provides loading state

## Bundle Independence

**No bundle has a dependency to CoreBundle!**

Individual bundles (ProductBundle, OrderBundle, etc.) MUST NOT import from CoreBundle. Instead:

- Shared components go in appropriate shared bundles
- CoreBundle acts as a **glue layer** that registers shared components into bundle-specific registries
- Use Module Federation aliases for cross-bundle imports

```typescript
// WRONG - ProductBundle importing from CoreBundle
import { SomeComponent } from '@coreshop/core/...'

// CORRECT - Import from appropriate bundle
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
│   └── dynamic-types/                   # Pimcore Data Object field types
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
import { EntityTabbedManager, EntityApi } from '@coreshop/resource'

const yourFeatureApi = new EntityApi<YourFeature>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/your-features'
})

export const YourFeatureManager: React.FC = () => {
  const modal = useFormModal()

  return (
    <EntityTabbedManager<YourFeature>
      api={yourFeatureApi}
      buildSavePayload={(data) => data}
      onAdd={async () => await new Promise<number>((resolve) => {
        modal.input({
          title: 'Add Feature',
          label: 'Name',
          onOk: async (value: string) => {
            const res = await yourFeatureApi.add({ name: value })
            resolve(res.data.id)
          }
        })
      })}
      renderDetail={(data, setData) => {
        if (!data) return <div>Select an item...</div>
        return <YourFeatureForm data={data} onChange={setData} />
      }}
    />
  )
}
```

### 3. Build and Deploy

```bash
# Development mode (single bundle)
npm run dev:single YourBundle

# Production build (all bundles)
npm run build

# The output will be in:
# src/CoreShop/Bundle/YourBundle/Resources/public/studio/[build-id]/
```

## Next Steps

- [Base Infrastructure](02_Base_Infrastructure/01_ResourceBundle.md) - ResourceBundle and RuleBundle details
- [FormBuilder](02_Base_Infrastructure/03_FormBuilder.md) - Decorator-based form builder
- [StudioFormBundle](02_Base_Infrastructure/04_StudioFormBundle.md) - Schema-driven forms from PHP FormTypes
- [Extending Rule Actions](../01_Extending_Guide/04_Extending_Rule_Actions.md) - Creating custom rule actions
- [Extending Rule Conditions](../01_Extending_Guide/05_Extending_Rule_Conditions.md) - Creating custom rule conditions

## Best Practices

### TypeScript

- Always define interfaces for your data types
- Use proper typing for component props
- Leverage type inference where possible

### Component Structure

- Keep components focused and single-purpose
- Use composition over inheritance
- Extract reusable logic into hooks

### Performance

- Use React.memo for expensive components
- Implement proper dependency arrays in useEffect
- Lazy load components when appropriate

## Common Patterns

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

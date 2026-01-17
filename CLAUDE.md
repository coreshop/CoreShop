# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

CoreShop is a Symfony-based Pimcore eCommerce platform built with a modular bundle/component architecture. The codebase follows Domain-Driven Design principles and is structured as a monorepo containing multiple related packages.

## Commands

### Development Commands

```bash
# Validate code syntax and configuration
bin/console lint:yaml src
bin/console lint:twig src
bin/console lint:container
bin/console doctrine:schema:validate --skip-sync

# Static Analysis
vendor/bin/phpstan                    # PHPStan analysis (level 3)
vendor/bin/psalm                      # Psalm static analysis

# Code Style
vendor/bin/ecs                        # Easy Coding Standard
vendor/bin/ecs --fix                  # Fix coding standard issues

# Testing
vendor/bin/behat                      # Run Behat tests
vendor/bin/behat --profile=default    # Run specific Behat profile

# Validation
composer validate                     # Validate composer.json
```

### Database & Cache
```bash
bin/console cache:clear --env=dev
bin/console doctrine:migrations:migrate
bin/console pimcore:install
```

## Architecture

### CRITICAL: Documentation Requirements
**⚠️ EVERY CODE CHANGE MUST UPDATE DOCUMENTATION!**
- When adding/modifying React components → Update Studio React docs in `docs/03_Development/14_Studio/`
- When adding/modifying ExtJS components → Update ExtJS docs in `docs/03_Development/`
- When adding Rules (Actions/Conditions) → Update both ExtJS and React docs
- When creating new features → Document architecture, API, and usage
- Documentation is NOT optional - it's part of the implementation

### CRITICAL: Bundle Dependencies
**⚠️ NO BUNDLE HAS A DEPENDENCY TO COREBUNDLE!**
- Individual bundles (ProductBundle, OrderBundle, etc.) MUST NOT import from CoreBundle
- Bundles are independent and habe cross-bundle dependencies. check the composer.json to see which bundle has which dependencies
- CoreBundle acts as a glue layer, not as a shared dependency layer

### Bundle-Component Pattern
CoreShop follows a strict Bundle-Component separation pattern:
- **Components** (`src/CoreShop/Component/`): Domain logic, business rules, interfaces
- **Bundles** (`src/CoreShop/Bundle/`): Symfony integration, DI configuration, controllers

### Core Architecture Layers

#### Components (Business Logic)
- `Core/`: Central business logic and models
- `Product/`: Product management and catalog functionality  
- `Order/`: Order processing and cart management
- `Customer/`: Customer and user management
- `Payment/`: Payment processing abstractions
- `Shipping/`: Shipping calculation and management
- `Index/`: Search and indexing functionality
- `Currency/`: Multi-currency support
- `Address/`: Address management
- `Store/`: Multi-store functionality
- `Taxation/`: Tax calculation rules
- `Rule/`: Business rule engine

#### Bundles (Symfony Integration)
- `CoreBundle/`: Main bundle providing core services
- `AdminBundle/`: Pimcore admin interface integration
- `FrontendBundle/`: Frontend controllers and templates
- `ResourceBundle/`: Generic CRUD operations
- Corresponding bundles for each Component (e.g., `ProductBundle/`, `OrderBundle/`)

### Key Design Patterns
- **Factory Pattern**: Extensive use for object creation
- **Specification Pattern**: Business rules and validation
- **Event-Driven Architecture**: Symfony events for extensibility
- **Repository Pattern**: Data access layer
- **State Machine**: Order and payment workflows

## Configuration

### Environment Files
- `.env`: Base environment configuration
- `.env.local`: Local overrides (not committed)

### Key Configuration Files
- `config/`: Symfony configuration
- `phpstan.neon`: Static analysis configuration (level 3)
- `psalm.xml`: Psalm configuration
- `ecs.php`: Coding standards configuration
- `behat.yml.dist`: BDD testing configuration

## Localization

### Translation Resources
- **CoreBundle**: `src/CoreShop/Bundle/CoreBundle/Resources/translations/studio.*.yaml`
- **StoreBundle**: `src/CoreShop/Bundle/StoreBundle/Resources/translations/studio.*.yaml`
- **CurrencyBundle**: `src/CoreShop/Bundle/CurrencyBundle/Resources/translations/studio.*.yaml`
- **Other Bundles**: Follow the pattern `src/CoreShop/Bundle/{BundleName}/Resources/translations/studio.*.yaml`

You can also add new keys, but double check existing ones first. Use `studio.en.yml` as the source of truth for English keys.

## Testing Strategy

### Test Types
- **Behat**: BDD integration tests for business scenarios
- **PHPUnit**: Unit tests (configured via composer.json)
- **Static Analysis**: PHPStan (level 3) and Psalm

### Test Environment
- Uses MySQL database for integration tests
- Pimcore test environment with specific kernel (`BehatKernel.php`)
- Environment variables required for Pimcore licensing

## Development Workflow

### Code Quality Standards
- PHP 8.3+ required
- Strict PSR-12 coding standards via ECS
- PHPStan level 3 analysis required
- All YAML, Twig, and container configuration must be valid
- Doctrine schema validation required

### Branch Strategy
- Main branches: `4.0`, `4.1`, `5.0`, `next`
- Base branch for PRs: `master`

### Before Committing
Always run the full validation suite:
```bash
composer validate
bin/console lint:yaml src
bin/console lint:twig src
bin/console lint:container
bin/console doctrine:schema:validate --skip-sync
vendor/bin/phpstan
vendor/bin/psalm
vendor/bin/ecs
```

## Pimcore Integration

This is a Pimcore Bundle project requiring:
- Pimcore ^12.0
- Specific Pimcore environment variables and licensing
- Integration with Pimcore's data objects and admin interface
- Pimcore-specific kernels for different environments

## Key Dependencies

- **Symfony**: 6.3+ or 7.0+ for framework components
- **Doctrine ORM**: 3.0+ for data persistence
- **Payum**: Payment processing integration
- **Sylius ThemeBundle**: Theme management
- **JMS Serializer**: API serialization
- **KnpMenuBundle**: Navigation management

## Pimcore Studio v2 Architecture (React/TypeScript)

### Separation of Concerns - CRITICALLY IMPORTANT

**Each Bundle creates and owns its own Registries:**

Each Rule Engine (Cart Price Rules, Product Price Rules, Shipping Rules, etc.) has **separate ConditionRegistry and ActionRegistry instances**. This is fundamental to the architecture.

#### Pattern: Bundle Registry Ownership

**RuleBundle provides generic registries:**
- `ConditionRegistry` - Generic class for condition registration
- `ActionRegistry` - Generic class for action registration
- Both classes are reusable with the same implementation (register, get, has, getAll)
- Type-safe through TypeScript generics

**Each bundle creates separate instances:**

1. **ProductBundle** creates and binds:
   - New instance of `ConditionRegistry` as ProductPriceRule condition registry
   - New instance of `ActionRegistry` as ProductPriceRule action registry
   - Registers ONLY what ProductBundle knows about (based on its composer.json dependencies)
   - Example: nested, timespan, weight conditions + discount/price actions
   - Does NOT know about Countries, Currencies, Customers (no dependency)

2. **OrderBundle** creates and binds:
   - New instance of `ConditionRegistry` as CartPriceRule condition registry
   - New instance of `ActionRegistry` as CartPriceRule action registry
   - Also creates CartItem registries (separate instances for nested rules)
   - Registers ONLY what OrderBundle knows about
   - Example: amount, voucher conditions + surcharge actions
   - Does NOT know about Countries, Currencies, Customers (no dependency)

3. **CoreBundle** acts as the "Glue":
   - Has dependencies on ALL bundles (see composer.json)
   - Retrieves registries via `container.get()` from other bundles
   - Registers shared conditions/actions into EACH registry
   - Example: CategoriesCondition, ProductsCondition, CustomersCondition, etc.
   - Registers bundle-specific extensions (e.g., QuantityCondition for Product Price Rules)

#### Key Principles

- **Each Bundle is independent**: Only depends on what's in its composer.json
- **No cross-bundle knowledge**: ProductBundle doesn't know CustomerBundle exists
- **CoreBundle orchestrates**: Has all dependencies, connects everything
- **Separate Registries**: Same action name (e.g., 'discountAmount') can exist in multiple registries with different implementations
- **Future-proof**: New rule engines follow the same pattern (ShippingRuleRegistry, etc.)

#### Checking Dependencies

When uncertain about which bundle should register a component:
```bash
# Check bundle dependencies
cat src/CoreShop/Bundle/{BundleName}/composer.json | grep require
```

If a condition/action needs CustomerBundle but ProductBundle doesn't depend on it → CoreBundle must register it.

### Studio v2 Structure Pattern

```
BundleX/Resources/assets/pimcore-studio/src/
├── modules/
│   ├── rule-type/                    # e.g., product-price-rules/
│   │   ├── actions/
│   │   │   ├── SpecificAction.tsx   # Bundle-specific action
│   │   │   └── index.ts
│   │   └── conditions/
│   │       ├── SpecificCondition.tsx
│   │       └── index.ts
│   └── icon-library/
└── main.ts                           # Registry creation + registration
```

**main.ts pattern:**
```typescript
import { container } from '@pimcore/studio-ui-bundle'
import { ConditionRegistry, ActionRegistry } from '@coreshop/rule/src/rules/registry'

// 1. Create and bind own registries (using generic RuleBundle classes)
container.bind(serviceIds.myConditionRegistry).to(ConditionRegistry).inSingletonScope()
container.bind(serviceIds.myActionRegistry).to(ActionRegistry).inSingletonScope()

// 2. Get own registries
const conditionRegistry = container.get<ConditionRegistry>(serviceIds.myConditionRegistry)
const actionRegistry = container.get<ActionRegistry>(serviceIds.myActionRegistry)

// 3. Register ONLY what this bundle knows
conditionRegistry.register('myCondition', MyCondition)
actionRegistry.register('myAction', MyAction)
```

**CoreBundle extends all registries:**
```typescript
import type { ConditionRegistry, ActionRegistry } from '@coreshop/rule/src/rules/registry'

// Get registries from other bundles (they're all ConditionRegistry/ActionRegistry instances)
const productConditionRegistry = container.get<ConditionRegistry>(productServiceIds.conditionRegistry)
const cartConditionRegistry = container.get<ConditionRegistry>(cartServiceIds.conditionRegistry)

// Register shared components into BOTH
productConditionRegistry.register('categories', CategoriesCondition)
cartConditionRegistry.register('categories', CategoriesCondition)
```

### Select Components with Module-Level Caching

**IMPORTANT:** When creating Select components that load data from APIs (e.g., TaxRuleGroupSelect, ShippingRuleSelect, StoreSelect), ALWAYS implement module-level caching to prevent multiple API calls when multiple select instances are rendered.

#### ❌ **WRONG** - No Caching (API called for each Select instance):

```typescript
export const ShippingRuleSelect: React.FC<SelectProps> = (props) => {
  const [options, setOptions] = React.useState<Array<{ value: number, label: string }>>([])
  const [loading, setLoading] = React.useState(false)

  React.useEffect(() => {
    const load = async () => {
      setLoading(true)
      const rules = await shippingRuleApi.list()
      setOptions(rules.map(r => ({ value: r.id!, label: r.name ?? `#${r.id}` })))
      setLoading(false)
    }
    void load()
  }, [])

  return <Select {...props} loading={loading} options={options} />
}
```

**Problem:** If 4 ShippingRuleSelects are rendered, API is called 4 times!

#### ✅ **CORRECT** - With Module-Level Caching:

```typescript
import React from 'react'
import { Select, type SelectProps } from 'antd'
import { shippingRuleApi } from '../modules/shipping-rules/api'

// Module-level cache to avoid multiple API calls
let cachedOptions: Array<{ value: number, label: string }> | null = null
let loadPromise: Promise<Array<{ value: number, label: string }>> | null = null

const loadShippingRules = async (): Promise<Array<{ value: number, label: string }>> => {
  // Return cached data if available
  if (cachedOptions) {
    return cachedOptions
  }

  // If already loading, return the existing promise (prevents race conditions)
  if (loadPromise) {
    return loadPromise
  }

  // Start new load
  loadPromise = (async () => {
    try {
      const rules = await shippingRuleApi.list()
      cachedOptions = rules.map(rule => ({
        value: rule.id!,
        label: rule.name ?? `#${rule.id}`
      }))
      return cachedOptions
    } catch (err) {
      console.error('Failed to load shipping rules:', err)
      throw err
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

// Export function to clear cache if needed (e.g., after creating new item)
export const clearShippingRuleCache = () => {
  cachedOptions = null
  loadPromise = null
}

export const ShippingRuleSelect: React.FC<SelectProps> = (props) => {
  const [options, setOptions] = React.useState<Array<{ value: number, label: string }>>(cachedOptions || [])
  const [loading, setLoading] = React.useState(!cachedOptions)

  React.useEffect(() => {
    void (async () => {
      if (!cachedOptions) {
        setLoading(true)
      }
      try {
        const opts = await loadShippingRules()
        setOptions(opts)
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  return (
    <Select
      {...props}
      loading={loading}
      options={options}
      placeholder={props.placeholder ?? 'Select a shipping rule'}
      showSearch
      filterOption={(input, option) =>
        (option?.label ?? '').toLowerCase().includes(input.toLowerCase())
      }
    />
  )
}
```

**Benefits:**
- ✅ API called only ONCE even with multiple Select instances
- ✅ Loading state shows immediately for first render
- ✅ Subsequent Selects instantly show cached data
- ✅ Prevents race conditions with `loadPromise` check
- ✅ Optional `clearCache()` function for invalidation
- ✅ Uses EntityApi `.list()` method (not raw fetch)

**Key Points:**
1. **Use EntityApi**: Import and use the Api class (e.g., `shippingRuleApi.list()`), NOT raw `fetch()`
2. **Module-level variables**: `cachedOptions` and `loadPromise` outside component
3. **Promise sharing**: Return same promise if already loading (prevents duplicate calls)
4. **Initial state from cache**: `useState(cachedOptions || [])` shows data immediately if cached
5. **Conditional loading**: Only show spinner if cache is empty

## Extension System

CoreShop Studio v2 provides a comprehensive extension system that allows bundles and projects to customize and extend ANY entity in the system. This system enables full customization without modifying core code.

### Available Extension Types

#### 1. **Form Extensions** - Add fields to entity forms

Add custom fields to any entity form (Country, TaxRate, Product, etc.).

**Use Cases:**
- Add custom fields from external bundles
- Integrate third-party services
- Add computed/derived fields

**Example:**
```typescript
import { container } from '@pimcore/studio-ui-bundle'
import { entityFormExtensionsServiceId, type EntityFormExtensionRegistry } from '@coreshop/resource/src/entities'
import { Input, Form } from 'antd'

const registry = container.get<EntityFormExtensionRegistry>(entityFormExtensionsServiceId)

registry.add('coreshop.address.country.form', ({ data, onChange, form }) => {
  return (
    <Form.Item label="Custom Field" name="customField">
      <Input onChange={(e) => onChange({ customField: e.target.value })} />
    </Form.Item>
  )
})
```

**Slot Naming Convention:** `{bundle}.{resource}.{component}`
- Example: `coreshop.address.country.form`
- Example: `coreshop.taxation.tax_rate.form`

#### 2. **Table Column Extensions** - Add columns to nested tables

Add custom columns to inline tables (e.g., tax rules table in TaxRuleGroup).

**Use Cases:**
- Add relational data columns
- Add computed columns
- Integrate external data

**Example:**
```typescript
import { entityTableColumnExtensionsServiceId, type EntityTableColumnExtensionRegistry } from '@coreshop/resource/src/entities'

const registry = container.get<EntityTableColumnExtensionRegistry>(entityTableColumnExtensionsServiceId)

registry.add('coreshop.taxation.tax_rule_group.tax_rules', ({ updateRecord }) => [
  {
    title: 'Country',
    dataIndex: 'country',
    width: 150,
    render: (value, record, index) => (
      <CountrySelect
        value={value}
        onChange={(newValue) => updateRecord(index, 'country', newValue)}
      />
    )
  }
])
```

#### 3. **Save Decorator Extensions** - Transform save payloads

Modify entity data before it's sent to the backend.

**Use Cases:**
- Add computed fields
- Transform data formats
- Inject additional data
- Clean up temporary fields

**Example:**
```typescript
import { entitySaveDecoratorsServiceId, type EntitySaveDecoratorRegistry } from '@coreshop/resource/src/entities'

const registry = container.get<EntitySaveDecoratorRegistry>(entitySaveDecoratorsServiceId)

registry.add('coreshop.address.country', (payload, data) => {
  return {
    ...payload,
    // Add timestamp
    lastModified: new Date().toISOString(),
    // Add computed field
    displayName: `${data.name} (${data.isoCode})`
  }
})
```

#### 4. **Tab Extensions** - Add custom tabs to entity managers

Add entire new tabs to entity detail views.

**Use Cases:**
- Add settings/configuration tabs
- Add related data tabs
- Add integration tabs

**Example:**
```typescript
import { entityTabExtensionsServiceId, type EntityTabExtensionRegistry } from '@coreshop/resource/src/entities'

const registry = container.get<EntityTabExtensionRegistry>(entityTabExtensionsServiceId)

registry.add('coreshop.address.country', ({ data }) => ({
  key: 'custom-tab',
  label: 'Custom Settings',
  icon: 'settings',
  render: (tabData, onChange, ctx) => (
    <div style={{ padding: 20 }}>
      <h3>Custom Tab Content</h3>
      <Form layout="vertical">
        <Form.Item label="Custom Setting">
          <Input
            value={tabData?.customSetting}
            onChange={(e) => onChange({ customSetting: e.target.value })}
          />
        </Form.Item>
      </Form>
    </div>
  )
}))
```

#### 5. **Action Extensions** - Add custom buttons/actions

Add custom buttons to toolbars, context menus, or footers.

**Use Cases:**
- Export/Import functions
- Duplicate/Clone operations
- External integrations
- Bulk operations

**Example:**
```typescript
import { entityActionExtensionsServiceId, type EntityActionExtensionRegistry } from '@coreshop/resource/src/entities'

const registry = container.get<EntityActionExtensionRegistry>(entityActionExtensionsServiceId)

registry.add('coreshop.address.country', ({ data, position }) => {
  if (position !== 'toolbar') return null

  return {
    key: 'export',
    label: 'Export',
    type: 'default',
    onClick: async (entityData) => {
      // Implement export logic
      await exportToCSV(entityData)
    }
  }
})
```

**Positions:**
- `toolbar` - Top toolbar buttons
- `context-menu` - Right-click context menu
- `footer` - Bottom footer actions

#### 6. **Validation Extensions** - Add custom validation

Add custom validation logic that runs before save.

**Use Cases:**
- Business rule validation
- Cross-field validation
- Async validation (uniqueness checks, API validation)
- Complex validation logic

**Example:**
```typescript
import { entityValidationExtensionsServiceId, type EntityValidationExtensionRegistry } from '@coreshop/resource/src/entities'

const registry = container.get<EntityValidationExtensionRegistry>(entityValidationExtensionsServiceId)

registry.add('coreshop.address.country', async (data, context) => {
  const errors: Record<string, string[]> = {}

  // Validate ISO code format
  if (data.isoCode && !/^[A-Z]{2}$/.test(data.isoCode)) {
    errors.isoCode = ['ISO code must be 2 uppercase letters']
  }

  // Async validation - check uniqueness
  if (data.name) {
    const exists = await checkNameExists(data.name, data.id)
    if (exists) {
      errors.name = ['Country name already exists']
    }
  }

  return {
    valid: Object.keys(errors).length === 0,
    errors: Object.keys(errors).length > 0 ? errors : undefined
  }
})
```

#### 7. **Lifecycle Hook Extensions** - Hook into entity lifecycle

Execute code at specific lifecycle events.

**Use Cases:**
- Data enrichment
- Logging/auditing
- Cache invalidation
- Triggering side effects
- Data cleanup

**Hook Types:**
- `beforeLoad` - Before entity is loaded from API
- `afterLoad` - After entity is loaded (enrich data)
- `beforeSave` - Before save payload is sent (final cleanup)
- `afterSave` - After successful save (side effects)
- `beforeDelete` - Before deletion (validation)
- `afterDelete` - After deletion (cleanup)

**Example:**
```typescript
import { entityLifecycleHooksServiceId, type EntityLifecycleHookRegistry } from '@coreshop/resource/src/entities'

const registry = container.get<EntityLifecycleHookRegistry>(entityLifecycleHooksServiceId)

// Enrich data after loading
registry.add('coreshop.address.country', 'afterLoad', (data, context) => {
  return {
    ...data,
    _loadedAt: new Date().toISOString()
  }
})

// Clean up before save
registry.add('coreshop.address.country', 'beforeSave', (data, context) => {
  return {
    ...data,
    // Remove temporary fields
    _loadedAt: undefined
  }
})

// Side effects after save
registry.add('coreshop.address.country', 'afterSave', (data, context) => {
  // Invalidate cache
  invalidateCountryCache()

  // Log audit event
  logAuditEvent('country_saved', context?.id)

  return data
})
```

### Extension Module Pattern

All extensions should be registered in a dedicated AbstractModule:

```typescript
import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { entityFormExtensionsServiceId } from '@coreshop/resource/src/entities'

export const MyExtensionModule: AbstractModule = {
  onInit(): void {
    const formRegistry = container.get(entityFormExtensionsServiceId)

    formRegistry.add('coreshop.address.country.form', ({ data, onChange }) => {
      // Your extension
    })
  }
}
```

Then register the module in your bundle's `main.ts`:

```typescript
const plugin: IAbstractPlugin = {
  name: 'my-bundle',

  onInit() {
    // Bundle initialization
  },

  onStartup({ moduleSystem }) {
    moduleSystem.registerModule(MyExtensionModule)
  }
}
```

### Complete Example

See `CoreBundle/Resources/assets/pimcore-studio/src/modules/extension/comprehensive-example/index.tsx` for a complete working example demonstrating all 7 extension types.

### Extension Slot Reference

**Common Slot Patterns:**

Forms:
- `coreshop.address.country.form`
- `coreshop.address.state.form`
- `coreshop.address.zone.form`
- `coreshop.taxation.tax_rate.form`
- `coreshop.taxation.tax_rule_group.form`
- `coreshop.currency.currency.form`

Tables:
- `coreshop.taxation.tax_rule_group.tax_rules` (nested tax rules table)

Entity Keys (for save, validation, lifecycle, tabs, actions):
- `coreshop.address.country`
- `coreshop.address.state`
- `coreshop.address.zone`
- `coreshop.taxation.tax_rate`
- `coreshop.taxation.tax_rule_group`
- `coreshop.currency.currency`

### Best Practices

1. **Always use descriptive slot names** following the convention
2. **Keep extensions focused** - one extension per concern
3. **Handle errors gracefully** - extensions should never break the UI
4. **Use TypeScript** for type safety
5. **Document your extensions** - others need to know what you added
6. **Test extensions** - verify they work with the core system
7. **Consider bundle dependencies** - only extend entities your bundle depends on
8. **Use proper service IDs** - always import from `@coreshop/resource/src/entities`

## Form Builder Pattern (Decorator-Based)

CoreShop Studio v2 implements a **Decorator Pattern** for building flexible, extensible entity forms. This pattern allows bundles to define base forms and other bundles to extend them without creating tight coupling.

### Architecture

**FormBuilder** uses decorators to compose form configurations:
- Base bundle creates a FormBuilder with core fields
- Extension bundles add decorators to extend functionality
- Decorators can add fields, sections, validation, or modify existing configuration
- Similar to Pimcore Studio's ListingBuilder pattern

### Key Concepts

#### 1. **FormBuilder Class**

```typescript
export class FormBuilder<T> {
  private decorators: Array<{ name: string, decorator: FormDecorator<T> }> = []
  private baseConfig: FormBuilderConfig<T>

  constructor(baseConfig: FormBuilderConfig<T>) {
    this.baseConfig = baseConfig
  }

  addDecorator(name: string, decorator: FormDecorator<T>): this
  overrideDecorator(name: string, decorator: FormDecorator<T>): this
  removeDecorator(name: string): this
  build(data?: T): FormBuilderConfig<T>
  copy(): FormBuilder<T>
}
```

#### 2. **FormDecorator Type**

```typescript
export interface FormDecorator<T> {
  (config: FormBuilderConfig<T>, data?: T): FormBuilderConfig<T>
}
```

Decorators are pure functions that transform form configuration.

### Bundle Architecture

**AddressBundle** (Base):
```typescript
// CountryFormBuilder.ts
export const createCountryFormBuilder = (): FormBuilder<CountryDetail> => {
  const builder = new FormBuilder<CountryDetail>({
    fields: [
      { name: 'name', label: 'Name', component: Input },
      { name: 'isoCode', label: 'ISO Code', component: Input },
      { name: 'active', label: 'Active', component: Switch },
      { name: 'zone', label: 'Zone', component: ZoneSelect }  // ✅ AddressBundle dependency
    ]
  })

  builder.addDecorator('section-general', addSectionDecorator({
    key: 'general',
    title: 'General Settings',
    order: 10
  }))

  return builder
}

// main.ts
export const CountryFormBuilderModule: AbstractModule = {
  onInit(): void {
    const builder = createCountryFormBuilder()
    container.bind('CoreShop/Address/Country/FormBuilder').toConstantValue(builder)
  }
}
```

**CoreBundle** (Extension):
```typescript
// country-form-extension.ts
export const CountryFormExtensionModule: AbstractModule = {
  onInit(): void {
    const builder = container.get<FormBuilder<CountryDetail>>(
      'CoreShop/Address/Country/FormBuilder'
    )

    // CoreBundle has dependency on CurrencyBundle
    builder.addDecorator('currency-section', addSectionDecorator({
      key: 'currency',
      title: 'Currency Settings',
      order: 30
    }))

    builder.addDecorator('currency-field', addFieldDecorator({
      name: 'currency',
      label: 'Default Currency',
      component: CurrencySelect,  // ✅ CoreBundle knows CurrencyBundle
      section: 'currency'
    }))
  }
}
```

### Common Decorator Patterns

#### Add Fields
```typescript
const addFieldDecorator = (field: FieldDefinition): FormDecorator<T> => {
  return (config) => ({
    ...config,
    fields: [...config.fields, field]
  })
}
```

#### Add Sections
```typescript
const addSectionDecorator = (section: SectionDefinition): FormDecorator<T> => {
  return (config) => ({
    ...config,
    sections: [...(config.sections ?? []), section]
  })
}
```

#### Conditional Fields
```typescript
const conditionalDecorator: FormDecorator<CountryDetail> = (config, data) => {
  if (!data?.active) return config

  return {
    ...config,
    fields: [...config.fields, { name: 'activeOnlyField', ... }]
  }
}
```

#### Add Validation
```typescript
const validationDecorator: FormDecorator<T> = (config) => ({
  ...config,
  validationRules: [
    ...(config.validationRules ?? []),
    { field: 'isoCode', rules: [{ pattern: /^[A-Z]{2}$/ }] }
  ]
})
```

### Usage in Components

```typescript
export const CountryForm: React.FC<CountryFormProps> = ({ data, onChange }) => {
  const builder = container.get<FormBuilder<CountryDetail>>(
    'CoreShop/Address/Country/FormBuilder'
  )

  const config = React.useMemo(() => builder.build(data), [data])

  return <DynamicForm config={config} data={data} onChange={onChange} />
}
```

### Key Principles

1. ✅ **Bundle Independence**: Base bundle doesn't know about extensions
2. ✅ **Decorator Composition**: Multiple decorators can be combined
3. ✅ **Type Safety**: TypeScript ensures type-safe transformations
4. ✅ **Testability**: Decorators are pure functions
5. ✅ **Order Control**: Decorators execute in registration order
6. ✅ **Override Support**: `overrideDecorator()` for replacing decorators

### Dependency Chain

```
AddressBundle
  ↓ Creates base FormBuilder
  ↓ Registers AddressBundle-specific fields (zone, addressFormat)
  ↓ Binds to container: 'CoreShop/Address/Country/FormBuilder'

CoreBundle
  ↓ Has dependency on AddressBundle
  ↓ Has dependency on CurrencyBundle
  ↓ Gets FormBuilder from container
  ↓ Adds decorators for cross-bundle fields (currency)
```

### Benefits

- **Flexible**: Decorators can add, remove, or modify any part of form config
- **Composable**: Multiple bundles can extend the same form
- **Conditional**: Decorators can use data to make decisions
- **Testable**: Pure functions are easy to unit test
- **Similar to Pimcore**: Follows same pattern as Pimcore Studio's ListingBuilder

### Example: Complete Form Structure

After all decorators are applied:

**Sections:**
1. **General Settings** (order: 10) - AddressBundle
   - name, isoCode, active, zone
2. **Address Configuration** (order: 20) - AddressBundle
   - addressFormat, salutations
3. **Currency Settings** (order: 30) - CoreBundle
   - currency (cross-bundle extension)

## Dynamic Types for Pimcore Data Objects

CoreShop extends Pimcore's Data Object field types with custom dynamic types for eCommerce-specific data. These types allow selection of CoreShop entities (Countries, Currencies, Stores, etc.) directly in Pimcore Data Object class definitions.

### Architecture

**Pimcore Studio v2** uses a `DynamicTypeObjectDataRegistry` to manage custom field types:

```typescript
import { container } from '@pimcore/studio-ui-bundle'
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'

const registry = container.get<DynamicTypeObjectDataRegistry>(
  serviceIds['DynamicTypes/ObjectDataRegistry']
)

registry.registerDynamicType(new MyCustomType())
```

### Available Abstract Classes

From `@pimcore/studio-ui-bundle/modules/element`:

- **`DynamicTypeObjectDataAbstract`** - Base class for all field types
- **`DynamicTypeObjectDataAbstractSelect`** - For single-select fields (extends Abstract)
- **`DynamicTypeObjectDataAbstractMultiSelect`** - For multi-select fields (extends Abstract)

### Implementation Pattern

**1. Create the Dynamic Type Class:**

```typescript
// dynamic-types/DynamicTypeObjectDataCoreShopCountry.tsx
import {
  DynamicTypeObjectDataAbstractSelect,
  DynamicTypeFieldFilterMultiselect
} from '@pimcore/studio-ui-bundle/modules/element'

export class DynamicTypeObjectDataCoreShopCountry extends DynamicTypeObjectDataAbstractSelect {
  // The id MUST match the PHP CoreExtension type name
  readonly id = 'coreShopCountry'
  readonly dynamicTypeFieldFilterType = new DynamicTypeFieldFilterMultiselect()
}
```

**2. Register in main.ts (onInit lifecycle):**

```typescript
import { container, IAbstractPlugin } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { DynamicTypeObjectDataCoreShopCountry } from './dynamic-types'

const plugin: IAbstractPlugin = {
  name: 'my-plugin',

  onInit() {
    const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
      serviceIds['DynamicTypes/ObjectDataRegistry']
    )

    objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopCountry())
  },

  onStartup({ moduleSystem }) {
    // Widget registration etc.
  }
}
```

### Key Points

1. **ID must match PHP type**: The `id` property must exactly match the PHP CoreExtension field type name (e.g., `coreShopCountry`)
2. **Options from backend**: The select options are provided by the PHP backend - the frontend just renders them
3. **Registration in onInit**: Dynamic types MUST be registered in `onInit()`, not `onStartup()`
4. **Abstract classes handle rendering**: `DynamicTypeObjectDataAbstractSelect` provides grid support, batch edit, and version handling

### CoreShop Dynamic Types List

| Type | Bundle | Base Class |
|------|--------|------------|
| `coreShopCountry` | AddressBundle | AbstractSelect |
| `coreShopCountryMultiselect` | AddressBundle | AbstractMultiSelect |
| `coreShopState` | AddressBundle | AbstractSelect |
| `coreShopAddressIdentifier` | AddressBundle | AbstractSelect |
| `coreShopCurrency` | CurrencyBundle | AbstractSelect |
| `coreShopCurrencyMultiselect` | CurrencyBundle | AbstractMultiSelect |
| `coreShopStore` | StoreBundle | AbstractSelect |
| `coreShopStoreMultiselect` | StoreBundle | AbstractMultiSelect |
| `coreShopCarrier` | ShippingBundle | AbstractSelect |
| `coreShopCarrierMultiselect` | ShippingBundle | AbstractMultiSelect |
| `coreShopPaymentProvider` | PaymentBundle | AbstractSelect |
| `coreShopPaymentProviderMultiselect` | PaymentBundle | AbstractMultiSelect |
| `coreShopTaxRate` | TaxationBundle | AbstractSelect |
| `coreShopTaxRuleGroup` | TaxationBundle | AbstractSelect |
| `coreShopFilter` | IndexBundle | AbstractSelect |
| `coreShopCartPriceRule` | OrderBundle | AbstractSelect |
| `coreShopProductUnit` | ProductBundle | AbstractSelect |

### File Structure

```
BundleX/Resources/assets/pimcore-studio/src/
├── dynamic-types/
│   ├── DynamicTypeObjectDataCoreShopXxx.tsx
│   ├── DynamicTypeObjectDataCoreShopXxxMultiselect.tsx
│   └── index.ts
└── main.ts  # Registration in onInit()
```

## Knowledge Graph
Use the knowledge-graph-mcp before and after every task you do.

## TODO
- Complex Dynamic Types: StoreValues, Money, DynamicDropdown, ProductUnitDefinitions, ProductSpecificPriceRules
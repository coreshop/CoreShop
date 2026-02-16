# FormBuilder Pattern

The FormBuilder provides a **decorator-based pattern** for building flexible, extensible entity forms. Inspired by Pimcore Studio's ListingBuilder pattern, it enables bundles to define base forms and other bundles to extend them without creating tight coupling.

> **Note:** The FormBuilder system lives in the **StudioFormBundle** (`@coreshop/studio-form`). Import from `@coreshop/studio-form/src/form-builder`.
>
> StudioFormBundle also provides a **Schema Adapter** that can automatically generate FormBuilderConfig from Symfony Form Types via a JSON API. See [StudioFormBundle](04_StudioFormBundle.md) for details.

## Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
- [Basic Usage](#basic-usage)
- [FormBuilder API](#formbuilder-api)
- [DynamicForm Component](#dynamicform-component)
- [Standard Decorators](#standard-decorators)
- [Bundle Architecture](#bundle-architecture)
- [Advanced Examples](#advanced-examples)
- [Best Practices](#best-practices)

## Overview

### Key Concepts

The FormBuilder pattern enables:

- ✅ **Composable Forms**: Build forms incrementally with decorators
- ✅ **Bundle Independence**: Base bundle doesn't know about extensions
- ✅ **Type Safety**: Full TypeScript type checking
- ✅ **Extensibility**: Any bundle can extend any form
- ✅ **Reusability**: Share decorators across multiple forms

### How It Works

1. **Base Bundle** creates a FormBuilder with core fields
2. **Extension Bundles** add decorators to extend functionality
3. **DynamicForm** renders the final configuration

```typescript
// AddressBundle creates base
const builder = new FormBuilder({ fields: [...] })

// CoreBundle extends
builder.addDecorator('currency', addFieldDecorator({ ... }))

// DynamicForm renders
const config = builder.build(data)
<DynamicForm config={config} data={data} onChange={onChange} />
```

## Architecture

### FormBuilder Class

The core class that manages form configuration:

```typescript
class FormBuilder<T = any> {
  constructor(baseConfig: FormBuilderConfig<T>)

  // Decorator management
  addDecorator(name: string, decorator: FormDecorator<T>): this
  overrideDecorator(name: string, decorator: FormDecorator<T>): this
  removeDecorator(name: string): this
  getDecorator(name: string): FormDecorator<T> | undefined
  getDecoratorNames(): string[]

  // Build final config
  build(context?: FormDecoratorContext<T>): FormBuilderConfig<T>

  // Utilities
  copy(): FormBuilder<T>
  getBaseConfig(): FormBuilderConfig<T>
}
```

### FormDecorator Type

Decorators are pure functions that transform configuration:

```typescript
interface FormDecorator<T = any> {
  (
    config: FormBuilderConfig<T>,
    context?: FormDecoratorContext<T>
  ): FormBuilderConfig<T>
}
```

### FormBuilderConfig

The configuration object used by DynamicForm:

```typescript
interface FormBuilderConfig<T = any> {
  fields: FieldDefinition<T>[]
  sections?: SectionDefinition[]
  layout?: 'vertical' | 'horizontal' | 'inline'
  columns?: number
  formProps?: Record<string, any>
}
```

### FieldDefinition

```typescript
interface FieldDefinition<T = any> {
  name: string                                    // Field name (key in data)
  label: string                                   // Display label (can be i18n key)
  component: React.ComponentType<any>            // Component to render
  section?: string                                // Section this field belongs to
  required?: boolean                              // Field is required
  disabled?: boolean                              // Field is disabled
  hidden?: boolean                                // Field is hidden
  rules?: Rule[]                                  // Ant Design validation rules
  tooltip?: string                                // Help text
  componentProps?: Record<string, any> | (() => Promise<Record<string, any>>)  // Props for component
  span?: number                                   // Grid column span (1-24)
  wrapper?: (children: React.ReactNode) => React.ReactNode
  visible?: (data?: T) => boolean                // Conditional visibility
  localized?: boolean                             // Append locale to label
}
```

**Important:** `componentProps` can be:
- A static object: `{ placeholder: 'Enter value' }`
- An **async function**: `async () => { const data = await api.list(); return { options: data } }`

This enables dynamic loading of options for Select components and other dynamic configuration.

### SectionDefinition

Sections group related fields:

```typescript
interface SectionDefinition {
  key: string                    // Unique section key
  title: string                  // Display title (can be i18n key)
  description?: string           // Section description
  collapsible?: boolean          // Section can collapse
  defaultCollapsed?: boolean     // Default collapsed state
  order?: number                 // Section order/priority
  icon?: React.ReactNode         // Custom icon
}
```

## Basic Usage

### Step 1: Create FormBuilder

Create a base FormBuilder in your bundle:

```typescript
// CountryFormBuilder.ts
import { FormBuilder } from '@coreshop/studio-form/src/form-builder'
import { Input, Switch } from 'antd'
import type { CountryDetail } from './api'
import { ZoneSelect } from '../zones/ZoneSelect'

export const createCountryFormBuilder = (): FormBuilder<CountryDetail> => {
  const builder = new FormBuilder<CountryDetail>({
    fields: [
      {
        name: 'name',
        label: 'coreshop_country',
        component: Input,
        required: true,
        rules: [{ required: true, message: 'Name is required' }]
      },
      {
        name: 'isoCode',
        label: 'coreshop_country_isoCode',
        component: Input,
        required: true,
        rules: [{ required: true, message: 'ISO Code is required' }]
      },
      {
        name: 'active',
        label: 'active',
        component: Switch
      },
      {
        name: 'zone',
        label: 'coreshop_zone',
        component: ZoneSelect
      }
    ]
  })

  return builder
}
```

### Step 2: Register in Container

Register the builder in your bundle's module:

```typescript
// form-builder-module.ts
import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { createCountryFormBuilder } from './CountryFormBuilder'

export const CountryFormBuilderModule: AbstractModule = {
  onInit(): void {
    const builder = createCountryFormBuilder()
    container.bind('CoreShop/Address/Country/FormBuilder').toConstantValue(builder)
  }
}
```

Register the module in your `main.ts`:

```typescript
const plugin: IAbstractPlugin = {
  name: 'coreshop-address-bundle',

  onStartup({ moduleSystem }) {
    moduleSystem.registerModule(CountryFormBuilderModule)
  }
}
```

### Step 3: Use in Form Component

```typescript
// CountryForm.tsx
import React from 'react'
import { container } from '@pimcore/studio-ui-bundle'
import { DynamicForm, type FormBuilder } from '@coreshop/studio-form/src/form-builder'
import type { CountryDetail } from './api'

export interface CountryFormProps {
  data?: CountryDetail
  onChange: (draft: Partial<CountryDetail>) => void
  currentLocale: string
}

export const CountryForm: React.FC<CountryFormProps> = ({
  data,
  onChange,
  currentLocale
}) => {
  // Get builder from container
  const builder = container.get<FormBuilder<CountryDetail>>(
    'CoreShop/Address/Country/FormBuilder'
  )

  // Build final config
  const config = React.useMemo(() => {
    return builder.build({ data, locale: currentLocale })
  }, [builder, data, currentLocale])

  return (
    <DynamicForm
      config={config}
      data={data}
      onChange={onChange}
      currentLocale={currentLocale}
    />
  )
}
```

## FormBuilder API

### Adding Decorators

Decorators are applied in the order they are added:

```typescript
builder.addDecorator('validation', validationDecorator)
builder.addDecorator('sections', sectionDecorator)
```

### Overriding Decorators

Replace an existing decorator:

```typescript
builder.overrideDecorator('validation', newValidationDecorator)
```

### Removing Decorators

```typescript
builder.removeDecorator('unwanted-decorator')
```

### Copying Builder

Create a copy with same config and decorators:

```typescript
const readonlyBuilder = builder.copy()
readonlyBuilder.addDecorator('readonly', readonlyDecorator)
```

### Building Configuration

Build the final config with all decorators applied:

```typescript
const config = builder.build({
  data: currentData,
  locale: 'en',
  form: formInstance
})
```

## DynamicForm Component

The DynamicForm component renders forms based on FormBuilderConfig.

### Props

```typescript
interface DynamicFormProps<T = any> {
  config: FormBuilderConfig<T>         // Configuration from FormBuilder
  data?: T                              // Current form data
  onChange: (draft: Partial<T>) => void // Change handler
  currentLocale?: string                // Current locale for localized fields
  form?: FormInstance                   // Ant Design form instance
}
```

### Basic Usage

```typescript
<DynamicForm
  config={config}
  data={data}
  onChange={(draft) => setData(prev => ({ ...prev, ...draft }))}
/>
```

### With Localization

```typescript
<DynamicForm
  config={config}
  data={data}
  onChange={onChange}
  currentLocale="en"
/>
```

### With Form Instance

```typescript
const [form] = Form.useForm()

<DynamicForm
  config={config}
  data={data}
  onChange={onChange}
  form={form}
/>
```

### Features

- ✅ **Automatic Rendering**: Renders all fields from config
- ✅ **Section Support**: Groups fields into collapsible sections
- ✅ **Grid Layout**: Supports multi-column layouts
- ✅ **Localization**: i18n support for labels and tooltips
- ✅ **Validation**: Ant Design validation rules
- ✅ **Conditional Fields**: Show/hide based on data
- ✅ **Custom Components**: Any React component works

## Standard Decorators

ResourceBundle provides commonly used decorators.

### addFieldDecorator

Add a field to the form:

```typescript
import { addFieldDecorator } from '@coreshop/studio-form/src/form-builder'

builder.addDecorator('currency', addFieldDecorator({
  name: 'currency',
  label: 'coreshop_currency',
  component: CurrencySelect
}))
```

**Position control:**

```typescript
// Add at start
addFieldDecorator(field, 'start')

// Add at end (default)
addFieldDecorator(field, 'end')

// Add at specific index
addFieldDecorator(field, 2)
```

### removeFieldDecorator

Remove a field by name:

```typescript
import { removeFieldDecorator } from '@coreshop/studio-form/src/form-builder'

builder.addDecorator('remove-zone', removeFieldDecorator('zone'))
```

### addSectionDecorator

Add a section for grouping fields:

```typescript
import { addSectionDecorator } from '@coreshop/studio-form/src/form-builder'

builder.addDecorator('general-section', addSectionDecorator({
  key: 'general',
  title: 'General Settings',
  order: 10,
  collapsible: true
}))
```

### transformFieldDecorator

Transform an existing field:

```typescript
import { transformFieldDecorator } from '@coreshop/studio-form/src/form-builder'

builder.addDecorator('modify-name', transformFieldDecorator('name', (field) => ({
  ...field,
  label: 'Custom Name',
  componentProps: {
    ...field.componentProps,
    placeholder: 'Enter custom name'
  }
})))
```

### addValidationDecorator

Add validation rules to a field:

```typescript
import { addValidationDecorator } from '@coreshop/studio-form/src/form-builder'

builder.addDecorator('email-validation', addValidationDecorator('email', [
  { type: 'email', message: 'Invalid email' }
]))
```

### requiredFieldDecorator

Make a field required:

```typescript
import { requiredFieldDecorator } from '@coreshop/studio-form/src/form-builder'

builder.addDecorator('require-iso', requiredFieldDecorator('isoCode', 'ISO Code is required'))
```

### conditionalFieldsDecorator

Show/hide fields based on condition:

```typescript
import { conditionalFieldsDecorator } from '@coreshop/studio-form/src/form-builder'

builder.addDecorator('conditional', conditionalFieldsDecorator(
  (data) => data?.active === true,
  ['advancedSettings', 'options']
))
```

### readonlyDecorator

Make all fields readonly:

```typescript
import { readonlyDecorator } from '@coreshop/studio-form/src/form-builder'

builder.addDecorator('readonly', readonlyDecorator)
```

### groupFieldsDecorator

Group fields into sections automatically:

```typescript
import { groupFieldsDecorator } from '@coreshop/studio-form/src/form-builder'

builder.addDecorator('grouping', groupFieldsDecorator({
  'general': ['name', 'active'],
  'advanced': ['zone', 'addressFormat']
}))
```

## Bundle Architecture

### Base Bundle Pattern (AddressBundle)

The base bundle creates the FormBuilder with core fields:

```typescript
// AddressBundle/CountryFormBuilder.ts
export const createCountryFormBuilder = (): FormBuilder<CountryDetail> => {
  const builder = new FormBuilder<CountryDetail>({
    fields: [
      { name: 'name', label: 'Name', component: Input },
      { name: 'isoCode', label: 'ISO Code', component: Input },
      { name: 'zone', label: 'Zone', component: ZoneSelect }  // ✅ AddressBundle knows ZoneSelect
    ]
  })

  return builder
}
```

**Key Points:**
- ✅ Only includes fields from bundles it depends on
- ✅ No knowledge of CurrencyBundle, StoreBundle, etc.
- ✅ Exports a factory function, not singleton

### Extension Bundle Pattern (CoreBundle)

CoreBundle extends forms from other bundles:

```typescript
// CoreBundle/country-form-extension.ts
import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import type { FormBuilder } from '@coreshop/studio-form/src/form-builder'
import { addFieldDecorator } from '@coreshop/studio-form/src/form-builder'
import type { CountryDetail } from '@coreshop/address/src/modules/countries/api'
import { CurrencySelectField } from '@coreshop/currency/src/components/CurrencySelectField'

export const CountryFormExtensionModule: AbstractModule = {
  onInit(): void {
    const builder = container.get<FormBuilder<CountryDetail>>(
      'CoreShop/Address/Country/FormBuilder'
    )

    // CoreBundle has dependency on CurrencyBundle
    builder.addDecorator('currency-field', addFieldDecorator({
      name: 'currency',
      label: 'coreshop_country_currency',
      component: CurrencySelectField  // ✅ CoreBundle knows CurrencyBundle
    }))
  }
}
```

**Key Points:**
- ✅ Gets builder from container
- ✅ Adds cross-bundle fields
- ✅ Respects bundle dependencies

### Dependency Chain

```
AddressBundle
  ↓ Creates base FormBuilder
  ↓ Registers fields: name, isoCode, zone, addressFormat
  ↓ Binds to container: 'CoreShop/Address/Country/FormBuilder'

CoreBundle
  ↓ Has dependency on AddressBundle (composer.json)
  ↓ Has dependency on CurrencyBundle
  ↓ Gets FormBuilder from container
  ↓ Adds decorators: currency field
```

## Advanced Examples

### Async Component Props

Load options dynamically for Select components:

```typescript
export const createStoreFormBuilder = (): FormBuilder<StoreDetail> => {
  const builder = new FormBuilder<StoreDetail>({
    fields: [
      {
        name: 'siteId',
        label: 'coreshop_store_site',
        component: Select,
        componentProps: async () => {
          const sites = await loadSites()
          return {
            options: sites.map(site => ({ value: site.id, label: site.name })),
            placeholder: 'Select a site',
            showSearch: true
          }
        }
      }
    ]
  })

  return builder
}
```

**Module-level caching for async props:**

```typescript
// Cache sites at module level
let cachedSites: Site[] | null = null
let loadPromise: Promise<Site[]> | null = null

const loadSites = async (): Promise<Site[]> => {
  if (cachedSites) return cachedSites
  if (loadPromise) return loadPromise

  loadPromise = (async () => {
    try {
      cachedSites = await listSites()
      return cachedSites
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

export const clearSitesCache = () => {
  cachedSites = null
  loadPromise = null
}
```

### Multi-Column Layout

```typescript
const builder = new FormBuilder<TaxRate>({
  columns: 2,
  fields: [
    { name: 'name', label: 'Name', component: Input, span: 12 },
    { name: 'rate', label: 'Rate', component: InputNumber, span: 12 },
    { name: 'description', label: 'Description', component: Input.TextArea, span: 24 }
  ]
})
```

### Sections with Icons

```typescript
import { SettingOutlined } from '@ant-design/icons'

builder.addDecorator('settings-section', addSectionDecorator({
  key: 'settings',
  title: 'Advanced Settings',
  description: 'Configure advanced options',
  icon: <SettingOutlined />,
  collapsible: true,
  defaultCollapsed: true,
  order: 20
}))
```

### Custom Decorator

Write a custom decorator:

```typescript
const addTimestampsDecorator: FormDecorator<any> = (config, context) => {
  if (!context?.data?.id) {
    // New entity - don't show timestamps
    return config
  }

  return {
    ...config,
    fields: [
      ...config.fields,
      {
        name: 'createdAt',
        label: 'Created At',
        component: Input,
        disabled: true
      },
      {
        name: 'updatedAt',
        label: 'Updated At',
        component: Input,
        disabled: true
      }
    ]
  }
}

builder.addDecorator('timestamps', addTimestampsDecorator)
```

### Conditional Section

```typescript
const conditionalSectionDecorator: FormDecorator<Country> = (config, context) => {
  if (!context?.data?.active) {
    return config
  }

  return {
    ...config,
    sections: [
      ...(config.sections ?? []),
      {
        key: 'active-only',
        title: 'Active Country Settings',
        order: 30
      }
    ],
    fields: [
      ...config.fields,
      {
        name: 'specialSettings',
        label: 'Special Settings',
        component: Input,
        section: 'active-only'
      }
    ]
  }
}
```

### Localized Form

```typescript
const builder = new FormBuilder<Country>({
  fields: [
    {
      name: 'name',
      label: 'coreshop_country',
      component: Input,
      localized: true  // Will append "(EN)", "(DE)", etc.
    }
  ]
})

// In component:
<DynamicForm
  config={config}
  data={data}
  onChange={onChange}
  currentLocale="en"  // Label becomes "Country (EN)"
/>
```

## Best Practices

### 1. Use Factory Functions

Export factory functions, not singletons:

```typescript
// ✅ GOOD - Factory function
export const createCountryFormBuilder = (): FormBuilder<CountryDetail> => {
  return new FormBuilder({ ... })
}

// ❌ BAD - Singleton
export const countryFormBuilder = new FormBuilder({ ... })
```

### 2. Respect Bundle Dependencies

Only add fields from bundles you depend on:

```typescript
// Check composer.json first!
cat src/CoreShop/Bundle/AddressBundle/composer.json | grep require

// If no dependency on CurrencyBundle, don't add currency field
// Let CoreBundle add it via decorator
```

### 3. Use Descriptive Decorator Names

```typescript
// ✅ GOOD
builder.addDecorator('currency-field', ...)
builder.addDecorator('validation-rules', ...)

// ❌ BAD
builder.addDecorator('dec1', ...)
builder.addDecorator('x', ...)
```

### 4. Keep Decorators Focused

Each decorator should do one thing:

```typescript
// ✅ GOOD - Focused decorators
builder.addDecorator('currency-field', addFieldDecorator({ name: 'currency', ... }))
builder.addDecorator('validation', addValidationDecorator('currency', [{ required: true }]))

// ❌ BAD - Decorator does too much
builder.addDecorator('everything', (config) => ({
  ...config,
  fields: [...config.fields, field1, field2, field3],
  sections: [...newSections],
  // etc...
}))
```

### 5. Use TypeScript Generics

Always specify the entity type:

```typescript
// ✅ GOOD
const builder = new FormBuilder<CountryDetail>({ ... })
const config = builder.build({ data: countryData })

// ❌ BAD
const builder = new FormBuilder({ ... })
```

### 6. Cache Async Data

Use module-level caching for async componentProps:

```typescript
// ✅ GOOD - Module-level cache
let cachedOptions: Option[] | null = null

const loadOptions = async () => {
  if (cachedOptions) return cachedOptions
  cachedOptions = await api.list()
  return cachedOptions
}

// ❌ BAD - Loads on every render
componentProps: async () => {
  const data = await api.list()  // Called multiple times!
  return { options: data }
}
```

### 7. Use Sections for Organization

Group related fields into sections:

```typescript
builder.addDecorator('sections', groupFieldsDecorator({
  'general': ['name', 'active'],
  'location': ['zone', 'addressFormat'],
  'currency': ['currency', 'currencySymbol']
}))
```

### 8. Document Your Extensions

Add comments explaining why extensions are needed:

```typescript
export const CountryFormExtensionModule: AbstractModule = {
  onInit(): void {
    const builder = container.get<FormBuilder<CountryDetail>>(
      'CoreShop/Address/Country/FormBuilder'
    )

    // CoreBundle has dependency on CurrencyBundle, so it can add currency field.
    // AddressBundle doesn't know about currencies.
    builder.addDecorator('currency-field', addFieldDecorator({
      name: 'currency',
      label: 'coreshop_country_currency',
      component: CurrencySelectField
    }))
  }
}
```

### 9. Handle Missing Builders Gracefully

```typescript
export const ExtensionModule: AbstractModule = {
  onInit(): void {
    try {
      const builder = container.get<FormBuilder>(serviceId)
      builder.addDecorator('extension', decorator)
    } catch (err) {
      console.error('[MyBundle] Failed to extend form:', err)
      // Don't crash the app
    }
  }
}
```

### 10. Test Decorators

Decorators are pure functions - easy to test:

```typescript
import { addFieldDecorator } from '@coreshop/studio-form/src/form-builder'

describe('addFieldDecorator', () => {
  it('should add field at end by default', () => {
    const config = { fields: [{ name: 'a', ... }] }
    const decorator = addFieldDecorator({ name: 'b', ... })
    const result = decorator(config)

    expect(result.fields).toHaveLength(2)
    expect(result.fields[1].name).toBe('b')
  })
})
```

## Summary

The FormBuilder pattern provides:

- ✅ **Composability**: Build forms incrementally with decorators
- ✅ **Extensibility**: Any bundle can extend any form
- ✅ **Type Safety**: Full TypeScript support
- ✅ **Flexibility**: Custom decorators for any use case
- ✅ **Separation of Concerns**: Base bundle independent of extensions

**Next Steps:**

- [ResourceBundle](01_ResourceBundle.md) - Entity APIs and managers
- [RuleBundle](02_RuleBundle.md) - Rule system infrastructure
- [Extension System](../04_Extension_System/) - Other extension points

# StudioFormBundle

The StudioFormBundle is a **reusable Pimcore Studio form system** that provides two complementary approaches for building entity forms:

1. **FormBuilder** - Manual, decorator-based form configuration (extracted from ResourceBundle)
2. **Schema Adapter** - Automatic form generation from Symfony Form Types via JSON API

## Overview

### Problem

Entity forms in CoreShop were defined twice: as Symfony Form Types in PHP and as TypeScript FormBuilderConfigs in the frontend. This led to maintenance overhead and potential inconsistencies.

### Solution

StudioFormBundle bridges this gap:

- **FormBuilder** (existing pattern, now extracted): Full control over form layout and behavior
- **Schema Adapter** (new): Introspects Symfony Form Types → generates JSON schema → renders Ant Design forms automatically

Both approaches produce `FormBuilderConfig` objects consumed by the `DynamicForm` component.

## Installation

StudioFormBundle has no CoreShop domain dependencies. It can be used in any Pimcore Studio project.

**PHP:**
```json
{
    "require": {
        "coreshop/studio-form-bundle": "^5.0"
    }
}
```

**Frontend:**
```json
{
    "dependencies": {
        "@coreshop/studio-form": "*"
    }
}
```

## Package: `@coreshop/studio-form`

### Exports

```typescript
// Form Builder (manual approach)
import { FormBuilder, DynamicForm } from '@coreshop/studio-form/src/form-builder'
import { addFieldDecorator, transformFieldDecorator } from '@coreshop/studio-form/src/form-builder/decorators'

// Schema Adapter (automatic approach)
import { useFormSchema, SchemaForm, WidgetRegistry } from '@coreshop/studio-form/src/schema-adapter'
import { widgetRegistryServiceId } from '@coreshop/studio-form/src/services'
```

## Schema Adapter

### Architecture

```
Symfony Form Type (PHP)
    ↓ FormSchemaGenerator introspects form tree
    ↓ FormTypeMapperRegistry resolves widget types
    ↓ FormSchemaEnrichers add tabs/sections
JSON Schema (API response)
    ↓ FormSchemaAdapter converts to FormBuilderConfig
    ↓ WidgetRegistry resolves React components
FormBuilderConfig → DynamicForm renders
```

### Backend: Schema Generator

The `FormSchemaGenerator` introspects Symfony Form Types and produces JSON schemas.

#### API Endpoint

```
GET /pimcore-studio/api/coreshop-studio-form/schema/{alias}
```

#### Configuration

Register form type aliases in your bundle config:

```yaml
# config/packages/coreshop_studio_form.yaml
core_shop_studio_form:
    aliases:
        coreshop.country: CoreShop\Bundle\AddressBundle\Form\Type\CountryType
        coreshop.tax_rate: CoreShop\Bundle\TaxationBundle\Form\Type\TaxRateType
```

Or register aliases via tagged services from any bundle.

#### Type Mappers

Type mappers convert Symfony form types to widget descriptors.

**Built-in mappings (BuiltinTypeMapper):**

| Symfony Type | Widget | Frontend Component |
|---|---|---|
| `TextType` | `input` | `Input` |
| `TextareaType` | `textarea` | `Input.TextArea` |
| `IntegerType` | `inputNumber` | `InputNumber` |
| `NumberType` | `inputNumber` | `InputNumber` |
| `CheckboxType` | `switch` | `Switch` |
| `ChoiceType` | `select` | `Select` |
| `HiddenType` | `hidden` | not rendered |
| `CollectionType` | `collection` | Repeater |

**Custom type mappers:**

```php
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormTypeMapperInterface;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\UiTypeDescriptor;
use Symfony\Component\Form\FormInterface;

final class ZoneChoiceTypeMapper implements FormTypeMapperInterface
{
    public function supports(FormInterface $field): bool
    {
        return $field->getConfig()->getType()->getInnerType()
            instanceof \CoreShop\Bundle\AddressBundle\Form\Type\ZoneChoiceType;
    }

    public function map(FormInterface $field, array $options): UiTypeDescriptor
    {
        return new UiTypeDescriptor('entitySelect', [
            'entityType' => 'coreshop.zone',
        ]);
    }
}
```

Register with tag:
```yaml
services:
    App\Form\Schema\ZoneChoiceTypeMapper:
        tags:
            - { name: coreshop_studio_form.type_mapper, priority: 10 }
```

Higher priority mappers are checked first. The builtin mapper has priority `-100`.

#### Schema Enrichers

Enrichers modify the generated schema, adding tabs, sections, or widget overrides.

```php
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaEnricherInterface;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchema;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\TabSchema;

final class CarrierSchemaEnricher implements FormSchemaEnricherInterface
{
    public function supports(string $formTypeClass): bool
    {
        return $formTypeClass === CarrierType::class;
    }

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        $schema->tabs[] = new TabSchema('settings', 'General', order: 10);
        $schema->tabs[] = new TabSchema('shippingRules', 'Shipping Rules',
            order: 20, widget: 'shippingRuleManager');

        foreach ($schema->fields as $field) {
            $field->tab = 'settings';
        }

        return $schema;
    }
}
```

Register with tag:
```yaml
services:
    App\Form\Schema\CarrierSchemaEnricher:
        tags:
            - { name: coreshop_studio_form.enricher }
```

### Frontend: Schema Consumer

#### WidgetRegistry

Maps widget type strings from the backend to React components.

```typescript
import { container } from '@pimcore/studio-ui-bundle'
import { WidgetRegistry } from '@coreshop/studio-form/src/schema-adapter'
import { widgetRegistryServiceId } from '@coreshop/studio-form/src/services'
import { ZoneSelect } from './components/ZoneSelect'

// In your bundle's main.ts onInit():
const registry = container.get<WidgetRegistry>(widgetRegistryServiceId)

registry.register('coreshop.zone', () => ({
    component: ZoneSelect,
    props: { allowClear: true, showSearch: true }
}))
```

**Default widgets** (registered by StudioFormBundle):
- `input` → `Input`
- `textarea` → `Input.TextArea`
- `inputNumber` → `InputNumber`
- `switch` → `Switch`
- `select` → `Select` (with choices from schema)
- `entitySelect` → `Select` (fallback, typically overridden)
- `hidden` → hidden field

#### useFormSchema Hook

Fetches schema, converts to FormBuilder, applies decorators:

```typescript
import { useFormSchema } from '@coreshop/studio-form/src/schema-adapter'
import { transformFieldDecorator } from '@coreshop/studio-form/src/form-builder'

const { builder, loading, error } = useFormSchema<CountryDetail>('coreshop.country', [
    { name: 'iso-hint', decorator: transformFieldDecorator('isoCode', (f) => ({
        ...f, tooltip: 'ISO 3166-1 alpha-2'
    }))}
])

if (loading || !builder) return <Spin />

const config = builder.build({ data })
return <DynamicForm config={config} data={data} onChange={onChange} />
```

#### SchemaForm Component

One-liner convenience component:

```typescript
import { SchemaForm } from '@coreshop/studio-form/src/schema-adapter'

<SchemaForm
    alias="coreshop.country"
    data={countryData}
    onChange={handleChange}
    currentLocale={locale}
/>
```

Includes built-in loading spinner and error display.

### JSON Schema Example

`GET /pimcore-studio/api/coreshop-studio-form/schema/coreshop.country`

```json
{
    "blockPrefix": "coreshop_country",
    "fields": [
        {
            "name": "translations",
            "blockPrefix": "coreshop_translations",
            "required": false,
            "uiType": { "widget": "coreshop_translations" },
            "children": {
                "blockPrefix": "coreshop_country_translation",
                "fields": [
                    {
                        "name": "name",
                        "blockPrefix": "text",
                        "required": true,
                        "uiType": { "widget": "input" }
                    }
                ],
                "tabs": [],
                "sections": []
            }
        },
        {
            "name": "isoCode",
            "blockPrefix": "text",
            "required": true,
            "uiType": { "widget": "input" }
        },
        {
            "name": "active",
            "blockPrefix": "checkbox",
            "required": false,
            "uiType": { "widget": "switch" }
        },
        {
            "name": "zone",
            "blockPrefix": "coreshop_zone_choice",
            "required": false,
            "uiType": { "widget": "entitySelect", "entityType": "coreshop.zone" }
        }
    ],
    "tabs": [],
    "sections": []
}
```

## Migration Guide

### Updating Imports

The FormBuilder system was extracted from ResourceBundle. Update imports:

```typescript
// Old (deprecated, still works via re-exports)
import { FormBuilder } from '@coreshop/resource/src/entities/form-builder'

// New
import { FormBuilder } from '@coreshop/studio-form/src/form-builder'
```

Add `@coreshop/studio-form` to your bundle's `package.json` dependencies:

```json
{
    "dependencies": {
        "@coreshop/studio-form": "*"
    }
}
```

### Migrating to Schema-Based Forms

To migrate an existing manual FormBuilder to schema-based:

**Before (manual):**
```typescript
export const createCountryFormBuilder = (): FormBuilder<CountryDetail> => {
    return new FormBuilder<CountryDetail>({
        fields: [
            { name: 'name', label: 'coreshop_country', component: Input, localized: true },
            { name: 'isoCode', label: 'coreshop_isoCode', component: Input },
            { name: 'active', label: 'active', component: Switch, valuePropName: 'checked' },
            { name: 'zone', label: 'coreshop_zone', component: ZoneSelect },
        ]
    })
}
```

**After (schema-based):**
```typescript
const { builder, loading } = useFormSchema<CountryDetail>('coreshop.country', [
    // Only UI-specific overrides that can't be expressed in PHP
    { name: 'iso-hint', decorator: transformFieldDecorator('isoCode', (f) => ({
        ...f, tooltip: 'ISO 3166-1 alpha-2'
    }))}
])
```

The PHP CountryType already defines all fields - no need to re-specify them in TypeScript.

## Bundle Structure

```
src/CoreShop/Bundle/StudioFormBundle/
├── CoreShopStudioFormBundle.php
├── composer.json
├── Controller/
│   └── FormSchemaController.php
├── Form/Schema/
│   ├── FormSchemaGenerator.php
│   ├── FormSchema.php
│   ├── FieldSchema.php
│   ├── TabSchema.php
│   ├── SectionSchema.php
│   ├── UiTypeDescriptor.php
│   ├── FormSchemaEnricherInterface.php
│   ├── FormTypeMapperInterface.php
│   ├── FormTypeMapperRegistry.php
│   ├── FormSchemaAliasRegistry.php
│   └── Mapper/
│       └── BuiltinTypeMapper.php
├── DependencyInjection/
│   ├── CoreShopStudioFormExtension.php
│   ├── Configuration.php
│   └── Compiler/
│       ├── RegisterFormTypeMapperPass.php
│       └── RegisterFormSchemaEnricherPass.php
├── Studio/
│   └── WebpackEntryPointProvider.php
└── Resources/
    ├── config/
    │   ├── services.yml
    │   └── pimcore/
    │       └── routing.yml
    └── assets/pimcore-studio/
        ├── package.json
        └── src/
            ├── main.ts
            ├── index.ts
            ├── services.ts
            ├── form-builder/
            │   ├── FormBuilder.ts
            │   ├── types.ts
            │   ├── index.ts
            │   ├── components/
            │   │   └── DynamicForm.tsx
            │   └── decorators/
            │       └── index.ts
            └── schema-adapter/
                ├── types.ts
                ├── WidgetRegistry.ts
                ├── FormSchemaAdapter.ts
                ├── useFormSchema.ts
                ├── SchemaForm.tsx
                ├── defaultWidgets.ts
                ├── api.ts
                └── index.ts
```

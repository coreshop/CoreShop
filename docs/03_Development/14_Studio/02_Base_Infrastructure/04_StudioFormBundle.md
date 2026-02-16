# StudioFormBundle

The `StudioFormBundle` is the shared form infrastructure for CoreShop Studio.

It provides:

1. `FormBuilder` + `DynamicForm` for explicit frontend form definitions.
2. A schema adapter that generates forms directly from Symfony Form Types.

The second mode is now the default pattern in most Studio modules.

## What It Solves

Without StudioFormBundle, forms are defined twice:

- once in PHP (`FormType`)
- once in React/TypeScript (`FormBuilderConfig`)

StudioFormBundle removes most of that duplication by serializing Symfony form metadata into JSON and rendering it in Studio with a widget registry.

## Runtime Flow

```text
Symfony FormType
  -> FormSchemaGenerator (PHP)
  -> JSON schema endpoint
  -> useFormSchema / SchemaForm (TS)
  -> FormSchemaAdapter -> FormBuilderConfig
  -> DynamicForm (Ant Design)
```

## Backend API

### Schema Endpoint

```http
GET /pimcore-studio/api/coreshop-studio-form/schema/{blockPrefix}
```

Important:

- The identifier is the **Symfony block prefix**, not an alias.
- Unknown block prefix returns `404`.
- Schema generation errors return `500`.

Example:

```http
GET /pimcore-studio/api/coreshop-studio-form/schema/coreshop_country
```

### How Form Types Are Registered

The block prefix registry is filled from:

- services tagged with `form.type`
- services tagged with `coreshop.studio_form`
- additional form classes registered by compiler passes (see rule integrations below)

Typical entity form registration:

```yaml
services:
    App\Form\Type\BrandType:
        tags:
            - { name: form.type }
            - { name: coreshop.studio_form }
```

### Generated Schema Shape

Top-level response:

- `blockPrefix: string`
- `fields: FormSchemaField[]`
- `tabs: FormSchemaTab[]`
- `sections: FormSchemaSection[]`

`fields` can include:

- `blockPrefixes` (Symfony prefix chain, right-most is most specific)
- choice metadata (`choices`, `multiple`, `expanded`)
- `extra` vars from `buildView()`
- nested `children`
- collection `prototype` / `prototypes`
- `tab` / `section` assignment

### Schema Enrichers

Implement `FormSchemaEnricherInterface` to post-process generated schemas.

Common use cases:

- reorder fields
- assign tabs/sections
- inject extra block prefixes for custom widgets

Register with tag:

```yaml
services:
    App\Form\Schema\BrandSchemaEnricher:
        tags:
            - { name: coreshop_studio_form.enricher, priority: 100 }
```

Example (existing in CoreShop): `CarrierSchemaEnricher` removes `shippingRules` from the generated form so that field can be rendered in a dedicated custom tab.

### Rule Form Integrations

For condition/action systems, many bundles add:

```php
$container->addCompilerPass(new RegisterFormTypesFromTagsPass('your.rule.condition.tag'));
$container->addCompilerPass(new RegisterFormTypesFromTagsPass('your.rule.action.tag'));
```

This collects the `form-type` attribute from those tagged services and adds them to the block-prefix registry, so rule configuration forms are resolvable through the same schema endpoint.

## Frontend Package (`@coreshop/studio-form`)

Main exports:

```typescript
import { FormBuilder, DynamicForm } from '@coreshop/studio-form/src/form-builder'
import { SchemaForm, useFormSchema, preSeedSchemaCache } from '@coreshop/studio-form'
import { widgetRegistryServiceId, type WidgetRegistry } from '@coreshop/studio-form'
```

### `SchemaForm`

Fast path for schema-driven forms:

```tsx
<SchemaForm<MyEntity>
    blockPrefix="coreshop_country"
    data={data}
    onChange={onChange}
    currentLocale={currentLocale}
/>
```

### `useFormSchema`

If you need custom decorator control:

```typescript
import { useFormSchema } from '@coreshop/studio-form'
import { transformFieldDecorator } from '@coreshop/studio-form/src/form-builder'

const { builder, loading, error } = useFormSchema<MyEntity>('coreshop_country', [
    { name: 'readonly-code', decorator: transformFieldDecorator('isoCode', (f) => ({ ...f, disabled: true })) },
])
```

### Widget Registry

The bundle registers default resolvers on startup (for `text`, `textarea`, `integer`, `number`, `checkbox`, `choice`, `collection`, `hidden`, `email`, `url`, `password`, `date`, `datetime`, `time`, `color`, `range`).

To add custom rendering for a block prefix:

```typescript
import { container } from '@pimcore/studio-ui-bundle'
import { widgetRegistryServiceId, type WidgetRegistry } from '@coreshop/studio-form'
import { PimcoreRelationWidget } from './PimcoreRelationWidget'

const registry = container.get<WidgetRegistry>(widgetRegistryServiceId)

registry.register('coreshop_pimcore_relation', (field) => ({
    component: PimcoreRelationWidget,
    props: {
        relationClass: field.extra?.relation_class,
        multiple: field.extra?.multiple ?? false,
    },
}))
```

Resolution follows Symfony semantics: widget resolvers are checked from the most specific block prefix to the least specific one.

### Rule Config Performance Pattern

Rule `get-config` endpoints can embed schema payloads for all conditions/actions.

Frontend should preload them:

```typescript
const config = await api.getConfig()
if (config.schemas) {
    preSeedSchemaCache(config.schemas)
}
```

That avoids one HTTP request per condition/action form.

## FormBuilder Mode (Manual)

`FormBuilder` is still first-class for cases where backend schema alone is not enough.

Typical cases:

- highly custom UI composition
- specialized wrappers/layout behavior
- temporary migration steps

See [FormBuilder Pattern](03_FormBuilder.md) for detailed manual-builder usage.

## Migration Notes

If you are migrating older Studio code:

1. Replace legacy `@coreshop/resource` FormBuilder imports with `@coreshop/studio-form/src/form-builder`.
2. Replace alias-based schema lookups with block-prefix lookups.
3. Prefer `SchemaForm` for CRUD forms that already have a Symfony `FormType`.
4. Keep decorators for frontend-only concerns (visual grouping, minor UI overrides).

## Examples

For practical end-to-end examples (from simple entity forms to rule engine integrations), see [StudioFormBundle Examples](./05_StudioFormBundle_Examples.md).

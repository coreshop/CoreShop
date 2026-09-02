# StudioFormBundle Examples

Practical end-to-end examples showing how to use the StudioFormBundle — from simple entity forms to advanced rule engine integrations. Each example shows both the PHP backend and the frontend usage.

> **Live Demos:** Open the widget `coreshop-studio-form-demos` in Pimcore Studio to see interactive examples of all form types. The demos are fully functional with browser-only state — no persistence.

> **Prerequisites:** Read the [StudioFormBundle overview](./04_StudioFormBundle.md) first for architecture and API reference.

---

## Category 1: Simple Entity Forms

### Example 1 — Minimal Form (Name + Active)

The simplest possible entity form. Two fields, zero configuration beyond the FormType.

**PHP — FormType:**

```php
<?php

namespace App\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class BrandType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'app_brand_name',
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'app_active',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_brand';
    }
}
```

**Service Registration:**

```yaml
services:
    App\Form\Type\BrandType:
        tags:
            - { name: form.type }
            - { name: coreshop.studio_form }
```

Both tags are needed:
- `form.type` makes Symfony aware of the type
- `coreshop.studio_form` registers it in the block prefix registry so the schema endpoint can serve it

**Frontend — Render the form:**

```tsx
import React from 'react'
import { SchemaForm } from '@coreshop/studio-form'

interface Brand {
  name: string
  active: boolean
}

export const BrandForm: React.FC<{
  data: Brand
  onChange: (data: Partial<Brand>) => void
}> = ({ data, onChange }) => {
  return (
    <SchemaForm<Brand>
      blockPrefix="app_brand"
      data={data}
      onChange={onChange}
    />
  )
}
```

That's it. `SchemaForm` fetches the schema from `GET /pimcore-studio/api/coreshop-studio-form/schema/app_brand`, resolves widgets for `TextType` and `CheckboxType` automatically, and renders an Ant Design form.

**Real-world reference:** [ZoneType.php](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/AddressBundle/Form/Type/ZoneType.php)

---

### Example 2 — Choice / Select Fields

Symfony `ChoiceType` maps to different Ant Design widgets depending on `multiple` and `expanded`.

**PHP — FormType with static choices:**

```php
<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Single select → renders as <Select> (dropdown)
        $builder->add('status', ChoiceType::class, [
            'label' => 'app_status',
            'choices' => [
                'Draft' => 'draft',
                'Published' => 'published',
                'Archived' => 'archived',
            ],
        ]);

        // Multiple select → renders as <Select mode="multiple">
        $builder->add('tags', ChoiceType::class, [
            'label' => 'app_tags',
            'choices' => [
                'Featured' => 'featured',
                'Sale' => 'sale',
                'New' => 'new',
            ],
            'multiple' => true,
        ]);

        // Expanded single → renders as <Radio.Group>
        $builder->add('visibility', ChoiceType::class, [
            'label' => 'app_visibility',
            'choices' => [
                'Public' => 'public',
                'Private' => 'private',
            ],
            'expanded' => true,
        ]);

        // Expanded multiple → renders as <Checkbox.Group>
        $builder->add('channels', ChoiceType::class, [
            'label' => 'app_channels',
            'choices' => [
                'Web' => 'web',
                'Mobile' => 'mobile',
                'POS' => 'pos',
            ],
            'multiple' => true,
            'expanded' => true,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'app_product_settings';
    }
}
```

**Widget mapping summary:**

| `multiple` | `expanded` | Ant Design Widget |
|:---:|:---:|---|
| `false` | `false` | `<Select>` |
| `true` | `false` | `<Select mode="multiple">` |
| `false` | `true` | `<Radio.Group>` |
| `true` | `true` | `<Checkbox.Group>` |

The widget registry handles this mapping automatically — no frontend code needed beyond `<SchemaForm blockPrefix="app_product_settings" />`.

**Dynamic choices from the database** work the same way. CoreShop entity ChoiceTypes (like `CountryChoiceType`, `StoreChoiceType`, `CurrencyChoiceType`) resolve their options server-side and pass them through the schema as `choices`.

**Real-world reference:** [CountriesConfigurationType.php](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/CoreBundle/Form/Type/Rule/Condition/CountriesConfigurationType.php) — uses `CountryChoiceType` with `multiple => true`

---

### Example 3 — Localized Fields (Translations)

Entity forms with translatable fields use `ResourceTranslationsType`. The frontend renders a locale switcher automatically.

**PHP — FormType with translations:**

```php
<?php

namespace App\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class CategoryType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Translatable fields wrapped in ResourceTranslationsType
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => CategoryTranslationType::class,
            ])
            // Non-translatable fields at root level
            ->add('isoCode', TextType::class, [
                'label' => 'app_iso_code',
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'app_active',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_category';
    }
}
```

**PHP — Translation sub-type:**

```php
<?php

namespace App\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class CategoryTranslationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'app_name',
            ])
            ->add('description', TextType::class, [
                'label' => 'app_description',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_category_translation';
    }
}
```

**Frontend — Pass locale information:**

```tsx
import React, { useState } from 'react'
import { SchemaForm } from '@coreshop/studio-form'

interface CategoryDetail {
  isoCode: string
  active: boolean
  translations: Record<string, { name: string; description: string }>
}

export const CategoryForm: React.FC<{
  data: CategoryDetail
  onChange: (data: Partial<CategoryDetail>) => void
  locales: string[]
}> = ({ data, onChange, locales }) => {
  const [currentLocale, setCurrentLocale] = useState(locales[0] ?? 'en')

  return (
    <SchemaForm<CategoryDetail>
      blockPrefix="app_category"
      data={data}
      onChange={onChange}
      currentLocale={currentLocale}
      locales={locales}
    />
  )
}
```

`SchemaForm` detects the `translations` block prefix and renders:
- A locale switcher with globe icon
- Translation fields scoped to the selected locale
- Non-translatable fields rendered normally outside the locale scope

**How the data structure works:**

```json
{
  "isoCode": "US",
  "active": true,
  "translations": {
    "en": { "name": "Electronics", "description": "All electronics" },
    "de": { "name": "Elektronik", "description": "Alle Elektronik" }
  }
}
```

**Real-world reference:** [CountryType.php](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/AddressBundle/Form/Type/CountryType.php)

---

## Category 2: Special Field Types

### Example 4 — Pimcore Relations (PimcoreRelationType)

Use `PimcoreRelationType` to create fields that reference Pimcore Data Objects. The widget renders Pimcore's native ManyToOneRelation / ManyToManyRelation picker.

**PHP — FormType:**

```php
<?php

namespace App\Form\Type;

use CoreShop\Bundle\StudioFormBundle\Form\Type\PimcoreRelationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class FeaturedCategoriesConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Multiple Pimcore object relations
            ->add('categories', PimcoreRelationType::class, [
                'label' => 'app_condition_categories',
                'relation_class' => 'CoreShopCategory',  // Pimcore class name
                'multiple' => true,                       // Allow multiple selections
            ])
            ->add('recursive', CheckboxType::class, [
                'label' => 'app_condition_recursive',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_featured_categories';
    }
}
```

The `relation_class` option is exposed through the schema as `extra.relation_class`. The CoreBundle registers a custom widget for the `coreshop_pimcore_relation` block prefix that renders a `PimcoreRelationWidget`:

```typescript
// Already registered by CoreBundle — you don't need to do this yourself.
// Shown here for understanding:
const registry = container.get<WidgetRegistry>(widgetRegistryServiceId)

registry.register('coreshop_pimcore_relation', (field) => ({
  component: PimcoreRelationWidget,
  props: {
    relationClass: field.extra?.relation_class,
    multiple: field.extra?.multiple ?? false,
  },
}))
```

**No frontend code needed** — just use `<SchemaForm blockPrefix="app_featured_categories" />` and the relation widget appears automatically.

**Real-world reference:** [CategoriesConfigurationType.php](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/CoreBundle/Form/Type/Rule/Condition/CategoriesConfigurationType.php)

---

### Example 5 — Collection Fields (Dynamic Lists)

`CollectionType` renders as a list with add/remove buttons. Each entry is a sub-form.

**PHP — FormType:**

```php
<?php

namespace App\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class TagGroupType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'app_name',
            ])
            // Dynamic list of text entries
            ->add('tags', CollectionType::class, [
                'label' => 'app_tags',
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_tag_group';
    }
}
```

The schema serializes the `prototype` field (the template for new entries). The frontend `CollectionWidget` uses it to render:
- Existing entries as editable rows
- An "Add" button that creates new entries from the prototype
- A "Remove" button on each entry

For more complex entries, use a custom sub-type as `entry_type`:

```php
->add('rules', CollectionType::class, [
    'label' => 'app_rules',
    'entry_type' => RuleEntryType::class,  // A FormType with multiple fields
    'allow_add' => true,
    'allow_delete' => true,
])
```

**Real-world reference:** [CountryType.php](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/AddressBundle/Form/Type/CountryType.php) — uses `CollectionType` for `salutations`

---

### Example 6 — Numeric Fields

`NumberType` and `IntegerType` render as `InputNumber` in Ant Design.

**PHP — FormType:**

```php
<?php

namespace App\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ExchangeRateType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rate', NumberType::class, [
                'label' => 'app_exchange_rate',
                'required' => true,
                'scale' => 10,  // Decimal precision
            ])
            ->add('fromCurrency', CurrencyChoiceType::class, [
                'label' => 'app_from_currency',
                'required' => true,
            ])
            ->add('toCurrency', CurrencyChoiceType::class, [
                'label' => 'app_to_currency',
                'required' => true,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_exchange_rate';
    }
}
```

The `required` option translates to an Ant Design validation rule. The `scale` option is passed through to control decimal precision.

**Real-world reference:** [ExchangeRateType.php](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/CurrencyBundle/Form/Type/ExchangeRateType.php)

---

## Category 3: Layout and Structure

### Example 7 — Grouping Fields in Sections (Schema Enricher)

For forms with many fields, use a Schema Enricher to group fields into collapsible sections.

**PHP — FormType (the form itself stays simple):**

```php
<?php

namespace App\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ShopSettingsType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // General section
        $builder->add('shopName', TextType::class, ['label' => 'app_shop_name']);
        $builder->add('guestCheckout', CheckboxType::class, ['label' => 'app_guest_checkout']);

        // Order section
        $builder->add('orderPrefix', TextType::class, ['label' => 'app_order_prefix']);
        $builder->add('orderSuffix', TextType::class, ['label' => 'app_order_suffix']);

        // Invoice section
        $builder->add('invoicePrefix', TextType::class, ['label' => 'app_invoice_prefix']);
        $builder->add('invoiceSuffix', TextType::class, ['label' => 'app_invoice_suffix']);
    }

    public function getBlockPrefix(): string
    {
        return 'app_shop_settings';
    }
}
```

**PHP — Schema Enricher (assigns fields to sections):**

```php
<?php

namespace App\Form\Schema;

use App\Form\Type\ShopSettingsType;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchema;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaEnricherInterface;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\SectionSchema;

final class ShopSettingsSchemaEnricher implements FormSchemaEnricherInterface
{
    // Map each field to its section
    private const array FIELD_SECTIONS = [
        'shopName' => 'general',
        'guestCheckout' => 'general',
        'orderPrefix' => 'order',
        'orderSuffix' => 'order',
        'invoicePrefix' => 'invoice',
        'invoiceSuffix' => 'invoice',
    ];

    public function supports(string $formTypeClass): bool
    {
        return $formTypeClass === ShopSettingsType::class;
    }

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        // Define sections with label, sort order, collapsible, defaultCollapsed
        $schema->sections = [
            new SectionSchema('general', 'app_general', 100, true, false),
            new SectionSchema('order', 'app_order', 90, true, true),
            new SectionSchema('invoice', 'app_invoice', 80, true, true),
        ];

        // Assign fields to their sections
        foreach ($schema->fields as $field) {
            if (isset(self::FIELD_SECTIONS[$field->name])) {
                $field->section = self::FIELD_SECTIONS[$field->name];
            }
        }

        return $schema;
    }
}
```

**`SectionSchema` constructor parameters:**

| Parameter | Type | Description |
|---|---|---|
| `$key` | `string` | Unique section identifier |
| `$label` | `string` | Translation key for the section title |
| `$order` | `int` | Sort priority (higher = first) |
| `$collapsible` | `bool` | Whether the section can be collapsed |
| `$defaultCollapsed` | `bool` | Whether the section starts collapsed |

**Service registration:**

```yaml
services:
    App\Form\Schema\ShopSettingsSchemaEnricher:
        tags:
            - { name: coreshop_studio_form.enricher, priority: 100 }
```

The frontend renders collapsible `<Collapse>` panels for each section, with fields grouped accordingly.

**Real-world reference:** [StoreSettingsSchemaEnricher.php](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/CoreBundle/Form/Schema/StoreSettingsSchemaEnricher.php)

---

### Example 8 — Organizing Fields in Tabs (Schema Enricher)

For complex entities, you can organize fields into tabs instead of (or in addition to) sections.

**PHP — Schema Enricher with tabs:**

```php
<?php

namespace App\Form\Schema;

use App\Form\Type\ProductConfigType;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchema;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaEnricherInterface;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\TabSchema;

final class ProductConfigSchemaEnricher implements FormSchemaEnricherInterface
{
    private const array FIELD_TABS = [
        'name' => 'general',
        'sku' => 'general',
        'active' => 'general',
        'weight' => 'shipping',
        'width' => 'shipping',
        'height' => 'shipping',
        'metaTitle' => 'seo',
        'metaDescription' => 'seo',
    ];

    public function supports(string $formTypeClass): bool
    {
        return $formTypeClass === ProductConfigType::class;
    }

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        // Define tabs with key, label, and sort order
        $schema->tabs = [
            new TabSchema('general', 'app_general', 100),
            new TabSchema('shipping', 'app_shipping', 90),
            new TabSchema('seo', 'app_seo', 80),
        ];

        // Assign fields to their tabs
        foreach ($schema->fields as $field) {
            if (isset(self::FIELD_TABS[$field->name])) {
                $field->tab = self::FIELD_TABS[$field->name];
            }
        }

        return $schema;
    }
}
```

**`TabSchema` constructor parameters:**

| Parameter | Type | Description |
|---|---|---|
| `$key` | `string` | Unique tab identifier |
| `$label` | `string` | Translation key for the tab title |
| `$order` | `int` | Sort priority (higher = first) |
| `$widget` | `?string` | Optional custom widget for the tab content |

---

### Example 9 — Removing Fields from the Schema (Enricher)

Sometimes a field exists in the Symfony FormType but should not appear in the auto-generated schema form. Common reason: the field is rendered in a dedicated custom tab or panel.

**PHP — Schema Enricher that removes fields:**

```php
<?php

namespace App\Form\Schema;

use App\Form\Type\CarrierType;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchema;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\FormSchemaEnricherInterface;

final class CarrierSchemaEnricher implements FormSchemaEnricherInterface
{
    public function supports(string $formTypeClass): bool
    {
        return $formTypeClass === CarrierType::class;
    }

    public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
    {
        // Remove 'shippingRules' — it's rendered in a dedicated tab by CarrierForm.
        $schema->fields = array_values(
            array_filter(
                $schema->fields,
                static fn ($field) => $field->name !== 'shippingRules',
            ),
        );

        return $schema;
    }
}
```

**Why not just remove the field from the FormType?**

The field is still needed for form submission and data binding. The enricher only removes it from the _schema_ (the JSON that drives the auto-generated UI), not from the actual Symfony form.

**Removing multiple fields:**

```php
public function enrich(FormSchema $schema, string $formTypeClass): FormSchema
{
    $excluded = ['conditions', 'actions', 'type'];

    $schema->fields = array_values(
        array_filter(
            $schema->fields,
            static fn ($field) => !in_array($field->name, $excluded, true),
        ),
    );

    return $schema;
}
```

**Real-world references:**
- [CarrierSchemaEnricher.php](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/ShippingBundle/Form/Schema/CarrierSchemaEnricher.php) — removes `shippingRules`
- `NotificationRuleSchemaEnricher` — removes `conditions`, `actions`, `type`
- `FilterSchemaEnricher` — removes `preConditions`, `conditions`

---

## Category 4: Frontend Customizations

### Example 10 — Decorators: Make Fields Readonly

Use `useFormSchema` with decorators to customize the schema-driven form without touching the backend.

```typescript
import React from 'react'
import { useFormSchema, DynamicForm } from '@coreshop/studio-form'
import { transformFieldDecorator } from '@coreshop/studio-form/src/form-builder'

interface CountryDetail {
  isoCode: string
  name: string
  active: boolean
}

export const CountryForm: React.FC<{
  data: CountryDetail
  onChange: (data: Partial<CountryDetail>) => void
}> = ({ data, onChange }) => {
  // Fetch schema and apply decorators
  const { builder, loading, error } = useFormSchema<CountryDetail>('coreshop_country', [
    // Make isoCode readonly after creation
    {
      name: 'readonly-iso-code',
      decorator: transformFieldDecorator('isoCode', (field) => ({
        ...field,
        disabled: true,
      })),
    },
  ])

  if (loading) return <div>Loading...</div>
  if (error || !builder) return <div>Error loading form</div>

  // Build the config (decorators are applied in order)
  const config = builder.build(data)

  return <DynamicForm config={config} data={data} onChange={onChange} />
}
```

`transformFieldDecorator(fieldName, transform)` finds a field by name and applies the transform function. You can change any field property:

```typescript
// Change label
transformFieldDecorator('name', (f) => ({ ...f, label: 'Custom Label' }))

// Make required
transformFieldDecorator('email', (f) => ({ ...f, required: true }))
```

---

### Example 11 — Decorators: Add, Remove, and Reorder Fields

Decorators receive the full `FormBuilderConfig` and return a modified version. This lets you add, remove, or reorder fields on the client side.

```typescript
import React from 'react'
import { useFormSchema, DynamicForm, type FormDecorator } from '@coreshop/studio-form/src/form-builder'

// Decorator: remove a field
const removeField = (fieldName: string): FormDecorator<any> => {
  return {
    name: `remove-${fieldName}`,
    decorator: (config) => ({
      ...config,
      fields: config.fields.filter((f) => f.name !== fieldName),
    }),
  }
}

// Decorator: add a computed display-only field
const addInfoField: FormDecorator<any> = {
  name: 'add-info',
  decorator: (config) => ({
    ...config,
    fields: [
      ...config.fields,
      {
        name: '_info',
        label: 'Info',
        component: () => <div style={{ color: '#888' }}>This entity was auto-generated.</div>,
      },
    ],
  }),
}

export const CustomizedForm: React.FC<{ data: any; onChange: (d: any) => void }> = ({
  data,
  onChange,
}) => {
  const { builder, loading } = useFormSchema('app_my_entity', [
    removeField('internalNotes'),
    addInfoField,
  ])

  if (loading || !builder) return null

  return <DynamicForm config={builder.build(data)} data={data} onChange={onChange} />
}
```

---

### Example 12 — Register a Custom Widget

The widget registry maps Symfony block prefixes to React components. Register your own widget for a custom block prefix.

**PHP — FormType with custom block prefix:**

```php
<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class ColorPickerType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        // Pass extra data to the schema via form view vars
        $view->vars['extra']['swatches'] = ['#ff0000', '#00ff00', '#0000ff', '#000000', '#ffffff'];
    }

    public function getBlockPrefix(): string
    {
        return 'app_color_picker';
    }
}
```

**Frontend — Register the widget:**

```typescript
import React from 'react'
import { container } from '@pimcore/studio-ui-bundle'
import { widgetRegistryServiceId, type WidgetRegistry } from '@coreshop/studio-form'
import { ColorPicker } from 'antd'

// Register in your bundle's onInit or module onInit
const registry = container.get<WidgetRegistry>(widgetRegistryServiceId)

registry.register('app_color_picker', (field) => ({
  component: ({ value, onChange }) => (
    <ColorPicker
      value={value}
      onChange={(color) => onChange(color.toHexString())}
      presets={[{ label: 'Presets', colors: field.extra?.swatches ?? [] }]}
    />
  ),
}))
```

Now any FormType field using `ColorPickerType` renders as an Ant Design `ColorPicker` with preset swatches.

**Widget resolution order:** The registry checks block prefixes from most specific to least specific (following Symfony's prefix chain). So `app_color_picker` is checked before `text`.

---

## Category 5: Rule Engine Integration

### Example 13 — Rule Condition/Action as Schema Form

The most powerful pattern: add a new rule condition with **zero React code**. The PHP FormType + service tag is enough.

**PHP — Condition FormType:**

```php
<?php

namespace App\Form\Type\Rule\Condition;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

final class MinimumWeightConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minWeight', NumberType::class, [
                'label' => 'app_condition_min_weight',
                'required' => true,
                'scale' => 2,
            ])
            ->add('unit', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                'label' => 'app_weight_unit',
                'choices' => [
                    'Kilogram' => 'kg',
                    'Gram' => 'g',
                    'Pound' => 'lb',
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_shipping_rule_condition_minimum_weight';
    }
}
```

**PHP — Service registration with `form-type` attribute:**

```yaml
services:
    app.shipping_rule.condition.minimum_weight:
        class: App\Rule\Condition\MinimumWeightChecker
        tags:
            - name: coreshop.shipping_rule.condition
              type: minimumWeight
              form-type: App\Form\Type\Rule\Condition\MinimumWeightConfigurationType
```

The key attributes:
- `name`: The rule system tag (e.g., `coreshop.shipping_rule.condition`)
- `type`: The condition type identifier used in the rule data
- `form-type`: The FQCN of the configuration FormType

**How it works behind the scenes:**

1. The bundle's `build()` method registers a `RegisterFormTypesFromTagsPass`:
   ```php
   // In your bundle class
   $container->addCompilerPass(
       new RegisterFormTypesFromTagsPass('coreshop.shipping_rule.condition')
   );
   ```

2. The compiler pass reads the `form-type` attribute from all tagged services and adds them to the `BlockPrefixFormTypeRegistry`.

3. The `get-config` API endpoint returns a `conditionSchemaByType` map:
   ```json
   {
     "conditionSchemaByType": {
       "minimumWeight": "app_shipping_rule_condition_minimum_weight"
     },
     "schemas": {
       "app_shipping_rule_condition_minimum_weight": {
         "blockPrefix": "app_shipping_rule_condition_minimum_weight",
         "fields": [...]
       }
     }
   }
   ```

4. The frontend calls `registerSchemaComponentsFromConfig()` which auto-generates React components:
   ```typescript
   import { registerSchemaComponentsFromConfig } from '@coreshop/rule/src/rules/registry'

   const config = await shippingRuleApi.getConfig()
   registerSchemaComponentsFromConfig(conditionRegistry, actionRegistry, config)
   ```

**Result:** A fully functional condition form rendered from the PHP FormType — no React component needed.

**Real-world references:**
- [createSchemaRuleComponent.tsx](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/RuleBundle/Resources/assets/pimcore-studio/src/rules/components/createSchemaRuleComponent.tsx)
- [registerSchemaComponents.ts](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/RuleBundle/Resources/assets/pimcore-studio/src/rules/registry/registerSchemaComponents.ts)

---

### Example 14 — Cross-Bundle Extension (FormTypeExtension)

Symfony's `FormTypeExtension` lets one bundle add fields to another bundle's form. This is how CoreBundle (which depends on all bundles) extends forms from independent bundles.

**Scenario:** `ShippingBundle` defines `CarrierType`. `CoreBundle` (which depends on both `StoreBundle` and `TaxationBundle`) adds `stores` and `taxRule` fields.

**PHP — FormTypeExtension in CoreBundle:**

```php
<?php

namespace CoreShop\Bundle\CoreBundle\Form\Extension;

use CoreShop\Bundle\ShippingBundle\Form\Type\CarrierType;
use CoreShop\Bundle\StoreBundle\Form\Type\StoreChoiceType;
use CoreShop\Bundle\TaxationBundle\Form\Type\TaxRuleGroupChoiceType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class CarrierTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // CoreBundle knows about StoreBundle → can add StoreChoiceType
        $builder->add('stores', StoreChoiceType::class, [
            'multiple' => true,
            'priority' => 40,
        ]);

        // CoreBundle knows about TaxationBundle → can add TaxRuleGroupChoiceType
        $builder->add('taxRule', TaxRuleGroupChoiceType::class, [
            'priority' => 30,
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [CarrierType::class];
    }
}
```

**Why this pattern exists:**

```
ShippingBundle (defines CarrierType)
  ↕ has NO dependency on StoreBundle or TaxationBundle

CoreBundle (depends on ALL bundles)
  → adds 'stores' field (from StoreBundle) to CarrierType
  → adds 'taxRule' field (from TaxationBundle) to CarrierType
```

The `priority` option controls field ordering. Higher priority = rendered first.

**Frontend side:** No changes needed. The extended fields appear automatically in the schema because Symfony merges extensions into the form before the schema is generated.

**Service registration:**

```yaml
services:
    CoreShop\Bundle\CoreBundle\Form\Extension\CarrierTypeExtension:
        tags:
            - { name: form.type_extension }
```

**Real-world reference:** [CarrierTypeExtension.php](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/CoreBundle/Form/Extension/CarrierTypeExtension.php)

---

## Category 6: Performance

### Example 15 — Schema Caching with preSeedSchemaCache

Rule systems can have many condition/action types. Without caching, each type triggers a separate HTTP request to fetch its schema. Use `preSeedSchemaCache` to load all schemas in one request.

**Backend:** The `get-config` endpoint already returns all schemas embedded in the response:

```json
{
  "conditions": ["amount", "voucher", "countries", "categories"],
  "actions": ["discountPercent", "surchargePercent"],
  "conditionSchemaByType": {
    "amount": "coreshop_cart_price_rule_condition_amount",
    "voucher": "coreshop_cart_price_rule_condition_voucher",
    "countries": "coreshop_rule_condition_countries",
    "categories": "coreshop_rule_condition_categories"
  },
  "schemas": {
    "coreshop_cart_price_rule_condition_amount": { "blockPrefix": "...", "fields": [...] },
    "coreshop_cart_price_rule_condition_voucher": { "blockPrefix": "...", "fields": [...] },
    "coreshop_rule_condition_countries": { "blockPrefix": "...", "fields": [...] },
    "coreshop_rule_condition_categories": { "blockPrefix": "...", "fields": [...] }
  }
}
```

**Frontend — Pre-seed the cache:**

```typescript
import { preSeedSchemaCache } from '@coreshop/studio-form'
import { registerSchemaComponentsFromConfig } from '@coreshop/rule/src/rules/registry'

// Fetch config once
const config = await cartPriceRuleApi.getConfig()

// Pre-seed schema cache — subsequent SchemaForm renders use cached data
if (config.schemas) {
  preSeedSchemaCache(config.schemas)
}

// Register schema components (uses createSchemaCondition/createSchemaAction internally)
registerSchemaComponentsFromConfig(conditionRegistry, actionRegistry, config)
```

**What happens without `preSeedSchemaCache`:**

```
Rule editor opens
  → Condition 1 renders → GET /schema/coreshop_rule_condition_countries
  → Condition 2 renders → GET /schema/coreshop_rule_condition_categories
  → Condition 3 renders → GET /schema/coreshop_cart_price_rule_condition_amount
  → Action 1 renders    → GET /schema/coreshop_cart_price_rule_action_discount
  ... (N requests for N types)
```

**What happens with `preSeedSchemaCache`:**

```
Rule editor opens
  → GET /api/cart-price-rules/get-config  (1 request, all schemas included)
  → preSeedSchemaCache(config.schemas)
  → All SchemaForm renders → cache hit, zero additional requests
```

This is especially impactful when a rule has many conditions/actions expanded simultaneously.

---

## Quick Reference: What Goes Where

| I want to... | PHP | Frontend |
|---|---|---|
| Simple entity form | `AbstractResourceType` + `coreshop.studio_form` tag | `<SchemaForm blockPrefix="..." />` |
| Add choices | `ChoiceType` or entity `ChoiceType` | Automatic |
| Add translations | `ResourceTranslationsType` | Pass `currentLocale` + `locales` |
| Add Pimcore relations | `PimcoreRelationType` | Automatic (widget registered) |
| Add dynamic lists | `CollectionType` with `allow_add`/`allow_delete` | Automatic |
| Group fields in sections | `FormSchemaEnricherInterface` + `SectionSchema` | Automatic |
| Group fields in tabs | `FormSchemaEnricherInterface` + `TabSchema` | Automatic |
| Hide fields from schema | Enricher with `array_filter` | Automatic |
| Make fields readonly | — | `transformFieldDecorator` |
| Add/remove fields client-side | — | Custom decorator |
| Custom widget for block prefix | Custom `AbstractType` with `getBlockPrefix()` | `WidgetRegistry.register()` |
| New rule condition | `AbstractType` + service tag with `form-type` | `registerSchemaComponentsFromConfig()` |
| Extend another bundle's form | `AbstractTypeExtension` | Automatic |
| Avoid N+1 schema requests | Embed schemas in `get-config` response | `preSeedSchemaCache()` |

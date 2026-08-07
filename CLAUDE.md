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

## Pricing

CoreShop stores all monetary values as **integers** (cents). For example, `383.12 EUR` is stored as `38312`. The `decimal_factor` is configurable (default `100`).

**Frontend formatting:** Use `formatCurrency(amount, currencyCode)` from `@coreshop/pimcore/src/utils`. It divides by 100 and uses `Intl.NumberFormat` for localized output (e.g. `formatCurrency(38312, 'EUR')` → `"383,12 €"`).

**Frontend conversion hook:** `useCurrencyConfig()` from `CurrencyBundle` provides `toDisplayPrice(int)` and `toIntegerPrice(display)` for form inputs.

## Architecture

### CRITICAL: Documentation Requirements
**Every code change must update documentation!**
- When adding/modifying React components -> Update Studio docs in `docs/03_Development/14_Studio/`
- When creating new features -> Document architecture, API, and usage

### CRITICAL: Bundle Dependencies
**No bundle has a dependency to CoreBundle!**
- Individual bundles (ProductBundle, OrderBundle, etc.) MUST NOT import from CoreBundle
- Bundles are independent with cross-bundle dependencies (check composer.json)
- CoreBundle acts as a glue layer, not as a shared dependency layer

### Bundle-Component Pattern
- **Components** (`src/CoreShop/Component/`): Domain logic, business rules, interfaces
- **Bundles** (`src/CoreShop/Bundle/`): Symfony integration, DI configuration, controllers

### Core Architecture Layers

#### Components (Business Logic)
`Core/`, `Product/`, `Order/`, `Customer/`, `Payment/`, `Shipping/`, `Index/`, `Currency/`, `Address/`, `Store/`, `Taxation/`, `Rule/`

#### Bundles (Symfony Integration)
`CoreBundle/`, `FrontendBundle/`, `ResourceBundle/`, and corresponding bundles for each Component

### Key Design Patterns
- **Factory Pattern**, **Specification Pattern**, **Event-Driven Architecture**, **Repository Pattern**, **State Machine**

## Configuration

- `.env` / `.env.local`: Environment configuration
- `config/`: Symfony configuration
- `phpstan.neon`, `psalm.xml`, `ecs.php`, `behat.yml.dist`

## Localization

Translation files: `src/CoreShop/Bundle/{BundleName}/Resources/translations/studio.*.yaml`
Use `studio.en.yml` as the source of truth for English keys.

### CRITICAL: Always Add Translations
**Every UI-facing string must have a translation key!**
- When adding new form fields, labels, buttons, messages, or any user-visible text → add translation keys to `studio.en.yaml` in the corresponding bundle
- Never hardcode user-visible strings — always use translation keys (e.g., `t('coreshop_...')` in React, `'label' => 'coreshop_...'` in FormTypes)
- Check that all translation keys used in code actually exist in the YAML files

## Development Workflow

- PHP 8.3+, PSR-12 via ECS, PHPStan level 3
- Pimcore ^12.0, Symfony 6.3+ or 7.0+
- Branch strategy: `4.0`, `4.1`, `5.0`, `next` — PRs target `master`

### Before Committing
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

## Pimcore Studio v2 Architecture (React/TypeScript)

The admin UI uses Pimcore Studio v2 (React/TypeScript). All new UI work uses Studio v2.

### Bundle Registry Ownership (Rule Engine)

Each Rule Engine has **separate ConditionRegistry and ActionRegistry instances**. RuleBundle provides generic registry classes; each bundle creates its own instances and registers only what it knows about. CoreBundle acts as glue, retrieving registries from other bundles and registering shared conditions/actions.

**Pattern:**
```typescript
// Bundle creates own registries in main.ts
container.bind(serviceIds.conditionRegistry).to(ConditionRegistry).inSingletonScope()
container.bind(serviceIds.actionRegistry).to(ActionRegistry).inSingletonScope()

// CoreBundle extends all registries with shared components
const productRegistry = container.get<ConditionRegistry>(productServiceIds.conditionRegistry)
productRegistry.register('categories', CategoriesCondition)
```

When uncertain which bundle should register a component, check `composer.json` dependencies. If the target bundle doesn't depend on the required bundle, CoreBundle must register it.

### Studio v2 File Structure
```
BundleX/Resources/assets/pimcore-studio/src/
├── modules/
│   ├── rule-type/           # conditions/ and actions/
│   └── icon-library/
├── dynamic-types/           # Pimcore Data Object field types
└── main.ts                  # Registry creation + registration
```

### Select Components - Module-Level Caching

**All Select components MUST use module-level caching** to prevent duplicate API calls when multiple instances render. Key pattern:

```typescript
let cachedOptions: Array<{ value: number, label: string }> | null = null
let loadPromise: Promise<...> | null = null

const loadData = async () => {
  if (cachedOptions) return cachedOptions
  if (loadPromise) return loadPromise  // share in-flight request
  loadPromise = (async () => {
    const items = await api.list()
    cachedOptions = items.map(i => ({ value: i.id!, label: i.name ?? `#${i.id}` }))
    return cachedOptions
  })()
  return loadPromise
}

export const clearCache = () => { cachedOptions = null; loadPromise = null }
```

Use EntityApi `.list()` method, NOT raw `fetch()`. Initialize state from cache: `useState(cachedOptions || [])`.

## Extension System

7 extension types for customizing entities, all imported from `@coreshop/resource/src/entities`:

| Type | Service ID | Purpose |
|------|-----------|---------|
| Form Extensions | `entityFormExtensionsServiceId` | Add fields to entity forms |
| Table Column Extensions | `entityTableColumnExtensionsServiceId` | Add columns to nested tables |
| Save Decorators | `entitySaveDecoratorsServiceId` | Transform save payloads |
| Tab Extensions | `entityTabExtensionsServiceId` | Add tabs to entity detail views |
| Action Extensions | `entityActionExtensionsServiceId` | Add toolbar/context-menu/footer buttons |
| Validation Extensions | `entityValidationExtensionsServiceId` | Custom validation before save |
| Lifecycle Hooks | `entityLifecycleHooksServiceId` | beforeLoad/afterLoad/beforeSave/afterSave/beforeDelete/afterDelete |

**Slot naming:** `{bundle}.{resource}.{component}` (e.g., `coreshop.address.country.form`)

Register extensions in AbstractModule's `onInit()`, then register module in bundle's `main.ts` via `onStartup({ moduleSystem })`.

See `CoreBundle/Resources/assets/pimcore-studio/src/modules/extension/comprehensive-example/index.tsx` for a complete example.

### Extension Slot Reference

Forms: `coreshop.address.country.form`, `coreshop.address.state.form`, `coreshop.address.zone.form`, `coreshop.taxation.tax_rate.form`, `coreshop.taxation.tax_rule_group.form`, `coreshop.currency.currency.form`

Entity keys (save, validation, lifecycle, tabs, actions): `coreshop.address.country`, `coreshop.address.state`, `coreshop.address.zone`, `coreshop.taxation.tax_rate`, `coreshop.taxation.tax_rule_group`, `coreshop.currency.currency`

## Dynamic Types for Pimcore Data Objects

Custom field types registered in each bundle's `main.ts` `onInit()`. ID must match the PHP CoreExtension type name. Options come from the backend.

```typescript
export class DynamicTypeObjectDataCoreShopCountry extends DynamicTypeObjectDataAbstractSelect {
  readonly id = 'coreShopCountry'
  readonly dynamicTypeFieldFilterType = new DynamicTypeFieldFilterMultiselect()
}
```

### Dynamic Types List

| Type | Bundle | Type | Bundle |
|------|--------|------|--------|
| `coreShopCountry` | AddressBundle | `coreShopCurrency` | CurrencyBundle |
| `coreShopCountryMultiselect` | AddressBundle | `coreShopCurrencyMultiselect` | CurrencyBundle |
| `coreShopState` | AddressBundle | `coreShopStore` | StoreBundle |
| `coreShopAddressIdentifier` | AddressBundle | `coreShopStoreMultiselect` | StoreBundle |
| `coreShopCarrier` | ShippingBundle | `coreShopPaymentProvider` | PaymentBundle |
| `coreShopCarrierMultiselect` | ShippingBundle | `coreShopPaymentProviderMultiselect` | PaymentBundle |
| `coreShopTaxRate` | TaxationBundle | `coreShopCartPriceRule` | OrderBundle |
| `coreShopTaxRuleGroup` | TaxationBundle | `coreShopFilter` | IndexBundle |
| `coreShopProductUnit` | ProductBundle | `coreShopProductUnitDefinition` | ProductBundle |
| `coreShopProductUnitDefinitions` | ProductBundle | | |

Complex types: `coreShopMoney` (MoneyBundle), `coreShopMoneyCurrency` (CurrencyBundle), `coreShopStoreValues` (CoreBundle), `coreShopRelation`/`coreShopRelations` (ResourceBundle), `coreShopProductSpecificPriceRules` (ProductBundle), `coreShopProductQuantityPriceRules` (ProductQuantityPriceRulesBundle)

## StudioFormBundle - Schema-Driven Form System

**This is the primary way to build forms.** Generates React forms from Symfony FormTypes via JSON schema.

```
Symfony FormType -> FormSchemaGenerator -> JSON Schema -> FormSchemaAdapter -> FormBuilder -> DynamicForm (React)
```

### Backend

- `FormSchemaGenerator`: Converts FormView to JSON schema
- `FormSchemaEnricherInterface`: Extension point for adding tabs/sections/hiding fields (tag: `coreshop_studio_form.enricher`)
- `BlockPrefixFormTypeRegistry`: Maps block prefixes to form type classes (tag: `coreshop.studio_form`)
- API: `GET /pimcore-studio/api/coreshop-studio-form/schema/{blockPrefix}`

### Frontend

**Usage (preferred approach for all entity forms):**
```typescript
import { SchemaForm } from '@coreshop/studio-form'

// Simple: auto-generates form from Symfony FormType
<SchemaForm blockPrefix="coreshop_cart_creation" data={data} onChange={onChange} />

// With decorators:
const { builder, loading } = useFormSchema('coreshop_country', [
  { name: 'hide-field', decorator: removeFieldDecorator('internalCode') },
])
```

**Widget resolution:** Block prefixes resolved right-to-left (most specific first), matching Symfony's Twig block resolution.

**Default widgets:** `text`->Input, `textarea`->Input.TextArea, `integer`->InputNumber, `checkbox`->Switch, `choice`->Select/Radio/Checkbox, `date`->DatePicker, `collection`->CollectionWidget, `grid_collection`->GridCollectionWidget. Custom widgets via `WidgetRegistry.register(blockPrefix, resolver)`.

**Available decorators** (from `@coreshop/studio-form/src/form-builder/decorators/`):
`addFieldDecorator`, `removeFieldDecorator`, `transformFieldDecorator`, `addSectionDecorator`, `sectionSortingDecorator`, `sectionFilterDecorator`, `hiddenFieldsDecorator`, `readonlyDecorator`, `addValidationDecorator`, `requiredFieldDecorator`, `conditionalFieldsDecorator`, `groupFieldsDecorator`

### Bundle Integration

```yaml
services:
  MyFormType:
    tags: [{ name: form.type }, { name: coreshop.studio_form }]
  MySchemaEnricher:
    tags: [{ name: coreshop_studio_form.enricher, priority: 10 }]
```

## Knowledge Graph
Use the knowledge-graph-mcp before and after every task you do.
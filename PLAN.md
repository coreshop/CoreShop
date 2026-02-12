# Migrationsplan: Bestehende Formulare → StudioForm Schema-System

## Übersicht

14 FormBuilder-Implementierungen in 9 Bundles müssen migriert werden. Die Migration erfolgt in 4 Wellen nach Komplexität. Jede Welle baut auf der vorherigen auf.

---

## Voraussetzungen (Welle 0)

Bevor einzelne Formulare migriert werden, muss die Infrastruktur stehen:

### 0.1 Type Mapper für CoreShop ChoiceTypes

Jedes Bundle mit Custom ChoiceTypes registriert einen `FormTypeMapperInterface`:

| Bundle | ChoiceType | Widget-Output |
|--------|-----------|---------------|
| AddressBundle | `ZoneChoiceType` | `entitySelect` + `entityType: coreshop.zone` |
| AddressBundle | `CountryChoiceType` | `entitySelect` + `entityType: coreshop.country` |
| CurrencyBundle | `CurrencyChoiceType` | `entitySelect` + `entityType: coreshop.currency` |
| ShippingBundle | `ShippingTaxCalculationStrategyChoiceType` | `select` (Choices aus PHP) |
| NotificationBundle | `NotificationRuleTypeChoiceType` | `select` (Choices aus PHP) |

→ Je 1 PHP-Klasse pro Bundle, tagged mit `coreshop_studio_form.type_mapper`

### 0.2 Widget-Resolver im Frontend

Jedes Bundle registriert seine Entity-Select-Komponenten in `main.ts`:

```typescript
const widgetRegistry = container.get<WidgetRegistry>(widgetRegistryServiceId)
widgetRegistry.register('coreshop.zone', () => ({ component: ZoneSelect, props: { allowClear: true } }))
widgetRegistry.register('coreshop.country', () => ({ component: CountrySelectField }))
// etc.
```

### 0.3 Alias-Registrierung

Alle Form Type Aliases müssen in den jeweiligen Bundles registriert werden – entweder via `core_shop_studio_form.aliases` Config oder via DI-Tag.

### 0.4 ResourceTranslationsType Mapper

Ein spezieller Mapper im StudioFormBundle für `ResourceTranslationsType` → Widget `translations`, der die Child-Felder korrekt als `children`-Schema ausgibt. Das ist zentral, da 9 von 14 Forms lokalisierte Felder haben.

### 0.5 CollectionType Mapper

Der bestehende `BuiltinTypeMapper` erkennt `CollectionType` schon. Für das `salutations`-Feld (Tags-Input) und nested Entity-Collections (TaxRules, ShippingRules) braucht es aber spezielle Frontend-Widgets.

---

## Welle 1: Einfache Formulare (nur Basis-Felder)

Formulare die nur `Input`, `InputNumber`, `Switch`, `Select` nutzen – kein Entity-Select, keine Nested Collections.

### 1.1 Currency (CurrencyBundle) — SIMPLEST
- **PHP Type**: `CurrencyType` → name (Text), isoCode (Text), numericIsoCode (Integer), symbol (Text)
- **Aktuell**: 4 Felder, alle einfache Typen, keine Lokalisierung
- **Migration**:
  - Alias registrieren: `coreshop.currency` → `CurrencyType::class`
  - `CurrencyFormBuilder.ts` löschen
  - `form-builder-module.ts` löschen
  - `CurrencyForm.tsx` auf `useFormSchema('coreshop.currency')` umbauen
  - `main.ts` Module-Registrierung entfernen
- **Zu löschende Dateien**: `CurrencyFormBuilder.ts`, `form-builder-module.ts`
- **Zu ändernde Dateien**: `CurrencyForm.tsx`, `main.ts`

### 1.2 TaxRate (TaxationBundle) — SIMPLE + LOCALIZED
- **PHP Type**: `TaxRateType` → translations (ResourceTranslationsType), rate (Number), active (Checkbox)
- **Aktuell**: 3 Felder, `name` lokalisiert, `rate` mit InputNumber (min/max/step/addonAfter)
- **Migration**:
  - Alias registrieren: `coreshop.tax_rate` → `TaxRateType::class`
  - Schema liefert translations → Frontend-Adapter expandiert zu localized fields
  - UI-Decorator für `rate`-Feld: `addonAfter: '%'`, `min: 0`, `max: 100`, `step: 0.01`
  - `TaxRateFormBuilder.ts` löschen, `form-builder-module.ts` löschen
  - Es gibt kein TaxRateForm.tsx (wurde direkt in einem Manager genutzt)
- **Zu löschende Dateien**: `TaxRateFormBuilder.ts`, `form-builder-module.ts`
- **Decorator nötig**: rate-Feld componentProps (kann nicht aus PHP kommen)

### 1.3 ShippingRule (ShippingBundle) — SIMPLE RULE
- **PHP Type**: `ShippingRuleType` → name (Textarea), active (Checkbox) + conditions/actions Collections
- **Aktuell**: 2 Felder (name, active), conditions/actions werden vom RuleBundle separat gehandelt
- **Migration**:
  - Alias: `coreshop.shipping_rule` → `ShippingRuleType::class`
  - Schema Generator muss conditions/actions Collections ignorieren (die werden vom Rule-System gehandelt)
  - `ShippingRuleFormBuilder.ts` löschen, `form-builder-module.ts` löschen
  - `SettingsForm.tsx` auf `useFormSchema` umbauen
- **Zu löschende Dateien**: `ShippingRuleFormBuilder.ts`, `form-builder-module.ts`

---

## Welle 2: Formulare mit Entity-Selects

Benötigt die Widget-Resolver aus Welle 0.

### 2.1 Zone (AddressBundle) — ENTITY_SELECT
- **PHP Type**: `ZoneType` → name (Text), active (Checkbox)
- **Aktuell**: 3 Felder, `countries` ist ein CountryMultiSelectField (aber NICHT im PHP Type!)
- **Problem**: Das `countries`-Feld existiert nur im Frontend-FormBuilder, nicht im PHP Form Type. Es wird vermutlich über die Entity-Extension oder Save-Decorator gehandelt.
- **Migration**:
  - Alias: `coreshop.zone` → `ZoneType::class`
  - Schema aus PHP: name, active
  - `countries`-Feld via Frontend-Decorator hinzufügen (bleibt manuell)
  - `ZoneFormBuilder.ts` löschen, `form-builder-module.ts` löschen
  - `ZoneForm.tsx` auf `useFormSchema` + Decorator für countries

### 2.2 State (AddressBundle) — ENTITY_SELECT
- **PHP Type**: `StateType` → translations, isoCode (Text), country (CountryChoiceType), active (Checkbox)
- **Aktuell**: 3 Felder, `country` ist CountrySelectField
- **Migration**:
  - Alias: `coreshop.state` → `StateType::class`
  - CountryChoiceType Mapper → `entitySelect` + `coreshop.country`
  - Widget-Resolver: `coreshop.country` → `CountrySelectField`
  - `StateFormBuilder.ts` löschen, `form-builder-module.ts` löschen
  - `StateForm.tsx` auf `useFormSchema` umbauen

### 2.3 Country (AddressBundle) — ENTITY_SELECT + LOCALIZED
- **PHP Type**: `CountryType` → translations, isoCode, active, zone (ZoneChoiceType), addressFormat (Textarea), salutations (Collection)
- **Aktuell**: 6 Felder + CoreBundle Extension (currency)
- **Migration**:
  - Alias: `coreshop.country` → `CountryType::class`
  - ZoneChoiceType Mapper → `entitySelect` + `coreshop.zone`
  - Schema liefert alle Felder automatisch inkl. translations
  - CoreBundle-Extension: CurrencyChoiceType wird via `CountryTypeExtension` (PHP) im Schema erscheinen → kein manueller Decorator mehr nötig!
  - `salutations` CollectionType → Frontend-Widget `collection` (Tags-Input)
  - `CountryFormBuilder.ts` löschen, `form-builder-module.ts` löschen
  - `country-form-extension.ts` (CoreBundle) kann entfallen!
  - `CountryForm.tsx` auf `useFormSchema` umbauen

### 2.4 Store (StoreBundle) — ENTITY_SELECT + ASYNC_PROPS
- **PHP Type**: `StoreType` → name, template, siteId (Integer), currency (CurrencyChoiceType)
- **Aktuell**: 4 Felder + CoreBundle Extension (baseCountry, useGrossPrice, countries)
- **Problem**: `siteId` nutzt async Props mit Module-Level-Cache für Pimcore Sites
- **Migration**:
  - Alias: `coreshop.store` → `StoreType::class`
  - CurrencyChoiceType Mapper → `entitySelect` + `coreshop.currency`
  - `siteId`: Braucht Frontend-Decorator, da Sites-Loading nicht im PHP Schema steckt
  - CoreBundle Extensions (baseCountry, useGrossPrice, countries): Diese Felder kommen vermutlich aus PHP TypeExtensions → Schema liefert sie automatisch
  - `StoreFormBuilder.ts` löschen, `form-builder-module.ts` löschen
  - CoreBundle `store/index.tsx` Extension prüfen ob sie entfallen kann
  - `StoreForm.tsx` auf `useFormSchema` + Decorators

---

## Welle 3: Rule-Formulare (Settings-Teil)

Alle Rule-Formulare folgen dem gleichen Muster: name, active, evtl. priority, localized label. Conditions/Actions werden separat vom RuleBundle gehandelt.

### 3.1 PaymentProviderRule (PaymentBundle)
- **Felder**: label (localized), name, active
- **Migration**: Alias + `useFormSchema`, SettingsForm.tsx umbauen

### 3.2 ProductPriceRule (ProductBundle)
- **Felder**: label (localized), name, description, active, priority
- **PHP hat zusätzlich**: `stopPropagation` (fehlt im aktuellen TS!) → Schema bringt es automatisch mit
- **Migration**: Alias + `useFormSchema`, SettingsForm.tsx umbauen

### 3.3 ProductSpecificPriceRule (ProductBundle)
- **Felder**: label (localized), name, active, priority, inherit
- **Migration**: Alias + `useFormSchema`, SettingsForm.tsx umbauen

### 3.4 CartPriceRule (OrderBundle)
- **Felder**: label (localized), name, description, active, priority, isVoucherRule
- **Migration**: Alias + `useFormSchema`, SettingsForm.tsx umbauen

### 3.5 NotificationRule (NotificationBundle)
- **Felder**: name, active, label (localized)
- **Sonderfall**: `type`-Feld wird AUSSERHALB des FormBuilders gehandelt (eigener Select mit onTypeChange-Callback)
- **Migration**: Alias + `useFormSchema` für die normalen Felder. `type`-Feld bleibt manuell im SettingsForm.tsx
- **Enricher**: Schema-Enricher der das `type`-Feld aus dem Schema entfernt (da es separat gerendert wird)

---

## Welle 4: Komplexe Formulare

### 4.1 TaxRuleGroup (TaxationBundle) — NESTED_COLLECTION
- **Aktuell**: FormBuilder für name/active + custom JSX für nested TaxRules-Tabelle
- **Migration**:
  - Alias: `coreshop.tax_rule_group` → `TaxRuleGroupType::class`
  - Schema liefert name, active, taxRules (collection)
  - Die Nested-Tabelle (TaxRules) bleibt custom JSX — kann nicht sinnvoll aus Schema generiert werden
  - `useFormSchema` für den einfachen Teil, custom Rendering für die Tabelle
  - `TaxRuleGroupFormBuilder.ts` löschen, `form-builder-module.ts` löschen
  - `TaxRuleGroupForm.tsx` behält die Tabellen-Logik, nutzt aber `useFormSchema` für den Header

### 4.2 Carrier (ShippingBundle) — TABS + NESTED + ASYNC
- **Aktuell**: Factory-Pattern mit async Config, Tab-Layout, nested ShippingRules-Tabelle
- **Migration**:
  - Alias: `coreshop.carrier` → `CarrierType::class`
  - **Enricher**: `CarrierSchemaEnricher` fügt Settings-Tab + ShippingRules-Tab hinzu
  - Custom Type Mapper: `ShippingTaxCalculationStrategyChoiceType` → `select`
  - Custom Type Mapper: `PimcoreAssetChoiceType` → Custom Widget `assetSelect`
  - Settings-Tab: `useFormSchema` mit Schema-generierten Feldern
  - ShippingRules-Tab: Bleibt custom JSX (nested Tabelle mit Priority, StopPropagation)
  - `CarrierFormBuilder.ts` löschen, `form-builder-module.ts` vereinfachen (kein async Config mehr)
  - `CarrierForm.tsx` behält Tab-Struktur + Tabellen-Logik, Settings-Tab nutzt Schema

---

## Pro Form: Was wird gelöscht / geändert

| Form | Löschen | Ändern |
|------|---------|--------|
| Currency | FormBuilder.ts, form-builder-module.ts | CurrencyForm.tsx, main.ts |
| TaxRate | FormBuilder.ts, form-builder-module.ts | (Parent-Komponente), main.ts |
| ShippingRule | FormBuilder.ts, form-builder-module.ts | SettingsForm.tsx, main.ts |
| Zone | FormBuilder.ts, form-builder-module.ts | ZoneForm.tsx, main.ts |
| State | FormBuilder.ts, form-builder-module.ts | StateForm.tsx, main.ts |
| Country | FormBuilder.ts, form-builder-module.ts | CountryForm.tsx, main.ts |
| Store | FormBuilder.ts, form-builder-module.ts | StoreForm.tsx, main.ts |
| PaymentProviderRule | FormBuilder.ts, form-builder-module.ts | SettingsForm.tsx, main.ts |
| ProductPriceRule | FormBuilder.ts, form-builder-module.ts | SettingsForm.tsx, main.ts |
| ProductSpecificPriceRule | FormBuilder.ts, form-builder-module.ts | SettingsForm.tsx, main.ts |
| CartPriceRule | FormBuilder.ts, form-builder-module.ts | SettingsForm.tsx, main.ts |
| NotificationRule | FormBuilder.ts, form-builder-module.ts | SettingsForm.tsx, main.ts |
| TaxRuleGroup | FormBuilder.ts, form-builder-module.ts | TaxRuleGroupForm.tsx, main.ts |
| Carrier | FormBuilder.ts, form-builder-module.ts | CarrierForm.tsx, main.ts |

**CoreBundle:**
- `country-form-extension.ts` → löschen (Currency kommt aus PHP TypeExtension)
- `store/index.tsx` → prüfen ob PHP TypeExtensions die Felder liefern

---

## Zusammenfassung der nötigen Arbeiten

### PHP (pro Bundle)
1. Form Type Alias registrieren
2. Custom ChoiceType Mapper (tagged service)
3. Enricher wo nötig (Carrier Tabs, NotificationRule type-Feld ausblenden)

### Frontend (pro Bundle)
1. Widget-Resolver für Entity-Selects in `main.ts` registrieren
2. Form-Komponente auf `useFormSchema()` umbauen
3. FormBuilder.ts + form-builder-module.ts löschen
4. Module-Registrierung in `main.ts` bereinigen
5. UI-Decorators wo PHP-Schema nicht ausreicht (rate %, siteId async, countries Multi-Select)

### StudioFormBundle
1. `ResourceTranslationsType` Mapper (Voraussetzung für alle lokalisierten Forms)
2. Rule-Type-Filter: Schema Generator soll `conditions`/`actions` Collections in Rule-Forms nicht ausgeben (die werden vom RuleBundle gehandelt)

### Gesamt
- **28 Dateien löschen** (14 FormBuilder.ts + 14 form-builder-module.ts)
- **14 Form-Komponenten umbauen**
- **~10 main.ts bereinigen**
- **~5 PHP Type Mapper erstellen**
- **~3 PHP Enricher erstellen**
- **2 CoreBundle Extensions entfernen**

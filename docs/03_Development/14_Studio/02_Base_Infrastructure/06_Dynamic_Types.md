# Dynamic Types: CoreShop Field Types in Pimcore Studio

CoreShop ships 31 custom Pimcore DataObject field types (`coreShopStore`, `coreShopMoney`,
`coreShopProductUnitDefinitions`, ...). Each type is a PHP `CoreExtension` class registered in the bundle's
`Resources/config/pimcore/config.yml` under `pimcore.objects.class_definitions.data.map`.

Pimcore Studio needs **two separate frontend registrations** per field type:

| Registry | Service id | Purpose |
|---|---|---|
| `DynamicTypeObjectDataRegistry` | `DynamicTypes/ObjectDataRegistry` | Render and edit the field value in the **object editor** |
| `DynamicTypeFieldDefinitionRegistry` | `DynamicTypes/FieldDefinitionRegistry` | Configure the field in the **class definition editor** and offer it in the "add field" dropdown |

Without the second registration the class editor shows "Type not supported" for the field and the type is
missing from the dropdown.

## Object editor types (`DynamicTypeObjectData*`)

Every bundle ships one `DynamicTypeObjectDataCoreShop<Name>` class in
`Resources/assets/pimcore-studio/src/dynamic-types/`. The `id` must match the PHP `fieldtype`. Plain resource
selects extend `DynamicTypeObjectDataCoreShopSelect` / `DynamicTypeObjectDataCoreShopMultiSelect` from
ResourceBundle and only provide the option loader:

```typescript
export class DynamicTypeObjectDataCoreShopStore extends DynamicTypeObjectDataCoreShopSelect {
  readonly id = 'coreShopStore'
  loadOptions = loadStores
  getCachedOptions = getStoreCache
}
```

## Class editor types (`DynamicTypeFieldDefinition*`)

The class editor counterpart lives next to the object data type and is named
`DynamicTypeFieldDefinitionCoreShop<Name>`. It declares the type's group in the "add field" dropdown, the
default data of a new field and the type-specific settings form.

### Base classes

| Class | Bundle | Use for |
|---|---|---|
| `DynamicTypeFieldDefinitionCoreShopAbstract` | PimcoreBundle | Any CoreShop type. Puts the type into the "CoreShop" dropdown group, hides the "unique" switch. |
| `DynamicTypeFieldDefinitionCoreShopSelect` | ResourceBundle | Types extending `CoreShop\Bundle\ResourceBundle\CoreExtension\Select` (width, allowEmpty) |
| `DynamicTypeFieldDefinitionCoreShopMultiselect` | ResourceBundle | Types extending `CoreShop\Bundle\ResourceBundle\CoreExtension\Multiselect` (width, height, maxItems, renderType) |
| `DynamicTypeFieldDefinitionCoreShopRelation` / `...Relations` | ResourceBundle | `coreShopRelation` / `coreShopRelations`; extend Pimcore's many-to-one / many-to-many forms with the resource stack |

Shared form items (`WidthFormItem`, `HeightFormItem`, `MinMaxFormItems`) are exported from
`@coreshop/pimcore/src/dynamic-types/field-definitions`; `CoreShopSelectFormFields`,
`CoreShopMultiselectFormFields` and `StackSelectFormItem` from
`@coreshop/resource/src/dynamic-types/field-definitions`.

PimcoreBundle owns the abstract base because MoneyBundle, PimcoreBundle and ProductQuantityPriceRulesBundle do
not depend on ResourceBundle (see "Bundle Independence").

### A plain select type

```typescript
import { DynamicTypeFieldDefinitionCoreShopSelect } from '@coreshop/resource/src/dynamic-types/field-definitions'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'

export class DynamicTypeFieldDefinitionCoreShopStore extends DynamicTypeFieldDefinitionCoreShopSelect {
  id: string = 'coreShopStore'

  getIcon(): ElementIcon {
    return { type: 'name', value: 'coreshop_store' }
  }
}
```

### A type with its own settings

Override `getDefaultData()` for the initial values and `getSpecificFormFields()` for the settings panel. The
form field names must match the public properties of the PHP `CoreExtension` class, because the class editor
saves the raw field definition.

```typescript
export class DynamicTypeFieldDefinitionCoreShopMoney extends DynamicTypeFieldDefinitionCoreShopAbstract {
  id: string = 'coreShopMoney'

  getDefaultData(): FieldDefinitionData {
    return { ...super.getDefaultData(), nullable: false }
  }

  getSpecificFormFields(context: FieldDefinitionContext): React.JSX.Element {
    return (
      <>
        <WidthFormItem />
        <MinMaxFormItems precision={0} />
        <Form.Item name="nullable"><Switch labelRight={t('coreshop_field_definition_nullable')} /></Form.Item>
      </>
    )
  }
}
```

### Registration

Register both types in the bundle's `main.ts` `onInit()`, inside the same `try/catch` that guards the
object data registry (the registries are absent in the reduced document editor iframe):

```typescript
import { registerCoreShopFieldDefinitionTypes } from '@coreshop/resource/src/dynamic-types/field-definitions'

objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopStore())

registerCoreShopFieldDefinitionTypes([
  new DynamicTypeFieldDefinitionCoreShopStore()
])
```

`registerCoreShopFieldDefinitionTypes()` registers the shared "CoreShop" dropdown group on first use and skips
types that are already registered.

### Translations

Pimcore resolves the type label from `field-definition.<kebab-case id>` (`coreShopStore` becomes
`field-definition.core-shop-store`). Add these keys, plus the `.with-prefix.add` / `.with-prefix.convert`
variants Pimcore uses when a type is promoted to the dropdown root, to the bundle's `studio.en.yaml`:

```yaml
field-definition.core-shop-store: 'CoreShop Store'
field-definition.core-shop-store.with-prefix.add: "$t(tree.actions.prefix.add)$t(field-definition.core-shop-store)"
field-definition.core-shop-store.with-prefix.convert: "$t(tree.actions.prefix.convert)$t(field-definition.core-shop-store)"
```

Labels of type-specific settings use the usual `coreshop_field_definition_*` keys.

## Type overview

| Field type | Bundle | Class editor base | Specific settings |
|---|---|---|---|
| `coreShopCountry`, `coreShopState`, `coreShopAddressIdentifier` | AddressBundle | Select | width, allowEmpty |
| `coreShopCountryMultiselect` | AddressBundle | Multiselect | width, height, maxItems, renderType |
| `coreShopCurrency` | CurrencyBundle | Select | width, allowEmpty |
| `coreShopCurrencyMultiselect` | CurrencyBundle | Multiselect | width, height, maxItems, renderType |
| `coreShopMoneyCurrency` | CurrencyBundle | Abstract | width, minValue, maxValue |
| `coreShopStore` | StoreBundle | Select | width, allowEmpty |
| `coreShopStoreMultiselect` | StoreBundle | Multiselect | width, height, maxItems, renderType |
| `coreShopCarrier` / `coreShopCarrierMultiselect` | ShippingBundle | Select / Multiselect | as above |
| `coreShopPaymentProvider` / `coreShopPaymentProviderMultiselect` | PaymentBundle | Select / Multiselect | as above |
| `coreShopTaxRate`, `coreShopTaxRuleGroup` | TaxationBundle | Select | width, allowEmpty |
| `coreShopCartPriceRule` | OrderBundle | Select | width, allowEmpty |
| `coreShopFilter` | IndexBundle | Select | width, allowEmpty |
| `coreShopProductUnit`, `coreShopProductUnitDefinition` | ProductBundle | Select | width, allowEmpty |
| `coreShopProductUnitDefinitions` | ProductBundle | Abstract | width |
| `coreShopProductSpecificPriceRules` | ProductBundle | Abstract | height |
| `coreShopProductQuantityPriceRules` | ProductQuantityPriceRulesBundle | Abstract | height |
| `coreShopMoney` | MoneyBundle | Abstract | width, defaultValue, minValue, maxValue, nullable (integer cents) |
| `coreShopStoreValues` | CoreBundle | Abstract | width, minValue, maxValue |
| `coreShopSerializedData` | PimcoreBundle | Abstract | none |
| `coreShopDynamicDropdown` | PimcoreBundle | Abstract | width, folderName, className, methodName, sortBy, recursive, onlyPublished |
| `coreShopDynamicDropdownMultiple`, `coreShopItemSelector`, `coreShopSuperBoxSelect` | PimcoreBundle | Abstract | as DynamicDropdown plus height, maxItems |
| `coreShopRelation` | ResourceBundle | Pimcore ManyToOne | Pimcore relation settings plus stack, returnConcrete |
| `coreShopRelations` | ResourceBundle | Pimcore ManyToMany | Pimcore relation settings plus stack |

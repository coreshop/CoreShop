# ResourceBundle

The ResourceBundle provides the core infrastructure for CRUD (Create, Read, Update, Delete) operations in the CoreShop Studio. It includes the EntityApi base class, hooks, and manager components.

## EntityApi Base Class

The `EntityApi` class provides a standardized way to interact with backend REST APIs for entity management.

### Configuration

```typescript
import { EntityApi } from '@coreshop/resource/src/entities'
import type { CountryDetail } from './types'

// Standard pattern: direct instantiation (no subclass needed)
export const countryApi = new EntityApi<CountryDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/countries'
})
```

You can optionally override the default route paths:

```typescript
export const countryApi = new EntityApi<CountryDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/countries',
  routes: {
    list: '/list',      // Default: '/list'
    get: '/get',        // Default: '/get'
    add: '/add',        // Default: '/add'
    save: '/save',      // Default: '/save'
    delete: '/delete'   // Default: '/delete'
  }
})
```

Only extend `EntityApi` when you need to add custom methods (e.g., `RuleApi` adds `getConfig()`).

### Available Methods

#### `list()` - Get All Entities

```typescript
const countries = await countryApi.list()
// Returns: EntityListItem[] (array of { id, name, ... })
```

#### `get(id)` - Get Single Entity

```typescript
const country = await countryApi.get(1)
// Returns: EntityGetResponse<CountryDetail> — { data: CountryDetail, success: boolean }
```

#### `add(payload)` - Create Entity

```typescript
const newCountry = await countryApi.add({ name: 'Germany' })
// Returns: EntityGetResponse<CountryDetail>
```

#### `save(payload)` - Update Entity

```typescript
const updated = await countryApi.save({ id: 1, name: 'Germany (Updated)', isoCode: 'DE' })
// Returns: EntityGetResponse<CountryDetail>
```

#### `delete(id)` - Delete Entity

```typescript
const result = await countryApi.delete(1)
// Returns: { success: boolean }
```

## useEntitySelect Hook

The `useEntitySelect` hook simplifies entity selection in dropdowns. It automatically loads entities, handles missing selections, and provides proper formatting for Ant Design Select components.

### Basic Usage

```typescript
import { useEntitySelect } from '@coreshop/resource'
import { countryApi } from '@coreshop/address/src/modules/countries/api'

const MyComponent = ({ data, onChange }) => {
  const selectedIds = data.countries || []
  const [options, value, handleSelectChange, loading] = useEntitySelect(
    countryApi,
    selectedIds
  )

  const handleChange = (ids: number[]) => {
    handleSelectChange(ids)
    onChange({ ...data, countries: ids })
  }

  return (
    <Select
      mode="multiple"
      value={value}
      onChange={handleChange}
      options={options}
      loading={loading}
      showSearch
      optionFilterProp="label"
    />
  )
}
```

### Signature

```typescript
function useEntitySelect<T extends SelectableEntity>(
  api: EntityApi<T>,
  selectedIds: number[] | string[] | undefined,
  labelKey: string = 'name'  // Optional: which field to use as label
): [SelectOption[], number[], (ids: number[]) => void, boolean]
```

### Return Values

| Return Value | Type | Description |
|--------------|------|-------------|
| `options` | `SelectOption[]` | Array of `{ label, value }` for Select component |
| `value` | `number[]` | Currently selected IDs (normalized to numbers) |
| `handleSelectChange` | `(ids: number[]) => void` | Function to update selected IDs |
| `loading` | `boolean` | Loading state while fetching entities |

### Custom Label Key

By default, the hook uses `'name'` as the label. You can specify a different field:

```typescript
const [options, value, handleChange, loading] = useEntitySelect(
  productApi,
  selectedIds,
  'title'  // Use 'title' field instead of 'name'
)
```

### Features

The `useEntitySelect` hook automatically:

1. **Loads all entities** on component mount
2. **Normalizes IDs** (backend may send strings, converts to numbers)
3. **Formats options** for Ant Design Select (`{ label, value }`)
4. **Manages loading state**
5. **Syncs with prop changes** (updates when selectedIds change)

## EntityTabbedManager

The `EntityTabbedManager` component provides a complete CRUD interface with a tabbed layout: entity list (tree) on the left, detail form tabs on the right.

### Props

```typescript
interface EntityTabbedManagerProps<TDetail extends Record<string, any>> {
  api: EntityApi<TDetail>
  renderDetail: (
    data: TDetail | undefined,
    setData: (draft: Partial<TDetail>) => void,
    ctx?: { currentLocale?: string, locales?: string[] }
  ) => React.ReactNode
  getTitle?: (listItem?: EntityListItem, data?: TDetail) => string
  buildSavePayload?: (data: TDetail) => Record<string, any>
  onAdd?: () => Promise<number>
  leftExtras?: React.ReactNode
  localizable?: boolean
  buildDragInfo?: (item: EntityListItem) => DragAndDropInfo | null
  dragType?: string
  leftRootTitle?: string
  leafIcon?: string
}
```

| Prop | Required | Description |
|------|----------|-------------|
| `api` | Yes | EntityApi instance for CRUD operations |
| `renderDetail` | Yes | Render function for the detail form (receives data, setData, and optional locale context) |
| `getTitle` | No | Custom tab title generator |
| `buildSavePayload` | No | Transform data before saving to API |
| `onAdd` | No | Custom add handler — must return the new entity's ID |
| `localizable` | No | Enable locale tabs for translatable entities |
| `dragType` | No | Enable drag-and-drop with this type identifier |
| `leftRootTitle` | No | Title for the tree root node |
| `leafIcon` | No | Icon for tree leaf nodes |

### Basic Usage

```typescript
import { EntityTabbedManager } from '@coreshop/resource'
import { useFormModal } from '@coreshop/resource'
import { taxRateApi } from './api'
import { TaxRateForm } from './TaxRateForm'

export const TaxRateManager: React.FC = () => {
  const { t } = useTranslation()
  const modal = useFormModal()

  return (
    <EntityTabbedManager<TaxRateDetail>
      api={taxRateApi}
      dragType='coreshop:tax_rate'
      leftRootTitle={t('coreshop_tax_rate', { defaultValue: 'Tax Rates' })}
      localizable
      getTitle={(li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}`}
      buildSavePayload={(data) => data}
      onAdd={async () => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop_tax_rate', { defaultValue: 'Add Tax Rate' }),
          label: t('coreshop_name', { defaultValue: 'Name' }),
          onOk: async (value: string) => {
            const res = await taxRateApi.add({ name: value })
            resolve(res.data.id)
          }
        })
      })}
      renderDetail={(data, setData, ctx) => {
        if (!data) return <div>Select a tax rate...</div>

        return (
          <TaxRateForm
            data={data}
            currentLocale={ctx?.currentLocale ?? 'en'}
            locales={ctx?.locales}
            onChange={(draft) => setData(draft)}
          />
        )
      }}
    />
  )
}
```

### Features

- Tree-view list on the left with search, add, delete, reload
- Tabbed detail view on the right (multiple entities open simultaneously)
- Dirty tracking with unsaved-changes indicator on tabs
- Locale support when `localizable={true}` (locale tabs passed via `ctx`)
- Drag-and-drop when `dragType` is set
- Auto-reloads list after add/delete
- Save via footer toolbar (managed internally by EntityTabbedLayout)

## GroupedEntityTabbedManager

For entities that belong to groups (e.g., Countries grouped by Zone), use `GroupedEntityTabbedManager`. It adds a hierarchical tree view.

### Props (in addition to EntityTabbedManager props)

```typescript
interface GroupedEntityTabbedManagerProps<TDetail extends Record<string, any>> {
  // ... all EntityTabbedManager props, plus:
  loadGroups: () => Promise<GroupItem[]>
  resolveGroupId: (li: EntityListItem, groups: GroupItem[]) => number | null | undefined
  applyGroup?: (data: TDetail, groupId: number | null) => TDetail
  onAdd: (groupId?: number) => Promise<number>  // receives groupId
  renderDetail: (
    data: TDetail | undefined,
    setData: (draft: Partial<TDetail>) => void,
    groups: GroupItem[],  // groups passed as third param
    ctx?: { currentLocale?: string, locales?: string[] }
  ) => React.ReactNode
}
```

### Usage (Countries grouped by Zone)

```typescript
import { GroupedEntityTabbedManager } from '@coreshop/resource/src/entities/components/GroupedEntityTabbedManager'
import { countryApi } from './api'
import { zoneApi } from '../zones/api'

export const CountryManager: React.FC = () => {
  const { t } = useTranslation()
  const modal = useFormModal()

  return (
    <GroupedEntityTabbedManager<CountryDetail>
      api={countryApi}
      dragType='coreshop:country'
      localizable
      loadGroups={async () => await zoneApi.list() as any}
      resolveGroupId={(li, groups) => {
        const it: any = li
        if (typeof it.zone === 'number') return it.zone
        if (typeof it.zoneName === 'string')
          return groups.find(g => g.name === it.zoneName)?.id ?? null
        return null
      }}
      applyGroup={(data, groupId) =>
        ({ ...data, zone: groupId ?? undefined } as CountryDetail)
      }
      buildSavePayload={(data) => data}
      onAdd={async (groupId?: number) => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop_country_add'),
          label: t('coreshop_name'),
          rule: { required: true, message: t('coreshop_name_required') },
          onOk: async (value: string) => {
            const res = await countryApi.add({
              name: value,
              ...(groupId ? { zone: groupId } : {})
            })
            resolve(res.data.id)
          }
        })
      })}
      renderDetail={(data, setData, zones, ctx) => {
        if (!data) return <div>Select a country...</div>
        return (
          <CountryForm
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

### Additional Features

- Drag-and-drop between groups (items can be moved to a different group)
- Groups loaded asynchronously on mount
- `onAdd` receives the group ID when adding from within a group
- `applyGroup` callback to update entity data when moved between groups

## EntitySplitManager

A simpler alternative to `EntityTabbedManager` that uses a split layout (no tabs). Only one entity is visible at a time.

### Props

```typescript
interface EntitySplitManagerProps<TDetail extends Record<string, any>> {
  api: EntityApi<TDetail>
  renderDetail: (
    data: TDetail | undefined,
    loading: boolean,
    onSave: (data: TDetail) => Promise<void>,
    onChange: (data: TDetail) => void
  ) => React.ReactNode
  createEmpty: () => TDetail
  leftRootTitle?: string
  dragType?: string
  buildDragInfo?: (item: EntityListItem) => DragAndDropInfo | null
  leafIcon?: string
}
```

Used by `RuleManager` in the RuleBundle for a simpler list+detail pattern.

## Type Definitions

### EntityListItem

```typescript
interface EntityListItem {
  id: number
  name: string
  [key: string]: any
}
```

### EntityGetResponse

```typescript
interface EntityGetResponse<T> {
  data: T
  success: boolean
}
```

### EntityApiConfig

```typescript
interface EntityApiConfig {
  basePath: string       // e.g. '/pimcore-studio/api'
  resourcePath: string   // e.g. '/coreshop/countries'
  routes?: {
    list: string         // Default: '/list'
    get: string          // Default: '/get'
    add: string          // Default: '/add'
    save: string         // Default: '/save'
    delete: string       // Default: '/delete'
  }
}
```

## Best Practices

### API Client

1. **Use direct instantiation** for standard CRUD APIs
2. **Use TypeScript generics** for type safety
3. **Export a singleton instance** for reuse

```typescript
// Standard pattern
export const countryApi = new EntityApi<CountryDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/countries'
})

// Only extend for custom methods
export class CarrierApi extends EntityApi<CarrierDetail> {
  async getConfig(): Promise<CarrierConfig> { ... }
}
```

### Error Handling

Always wrap API calls in try-catch:

```typescript
try {
  const result = await api.save(data)
  message.success('Saved successfully')
} catch (error) {
  message.error(`Failed to save: ${error.message}`)
}
```

## Next Steps

- [RuleBundle](02_RuleBundle.md) - Learn about the Rule system infrastructure
- [FormBuilder](03_FormBuilder.md) - Decorator-based form builder pattern
- [StudioFormBundle](04_StudioFormBundle.md) - Schema-driven forms from PHP FormTypes

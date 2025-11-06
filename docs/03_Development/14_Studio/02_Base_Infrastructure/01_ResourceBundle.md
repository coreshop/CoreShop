# ResourceBundle

The ResourceBundle provides the core infrastructure for CRUD (Create, Read, Update, Delete) operations in the CoreShop Studio. It includes the EntityApi base class, hooks, and manager components.

## EntityApi Base Class

The `EntityApi` class provides a standardized way to interact with backend REST APIs for entity management.

### Configuration

```typescript
import { EntityApi } from '@coreshop/resource/src/entities'
import type { Country } from './types'

export class CountryApi extends EntityApi<Country> {}

export const countryApi = new CountryApi({
  basePath: '/pimcore-studio/api',        // API base path
  resourcePath: '/coreshop/countries',    // Resource endpoint
  routes: {                                // Optional: Override default routes
    list: '/list',                         // Default: '/list'
    get: '/get',                           // Default: '/get'
    add: '/add',                           // Default: '/add'
    save: '/save',                         // Default: '/save'
    delete: '/delete'                      // Default: '/delete'
  }
})
```

### Available Methods

#### `list()` - Get All Entities

Fetches a list of all entities.

```typescript
const countries = await countryApi.list()
// Returns: Country[] (array of entities)
```

**Response Format:**
```typescript
interface EntityListResponse<T extends EntityListItem> extends Array<T> {}

interface EntityListItem {
  id: number
  name: string
  // ... other fields
}
```

#### `get(id)` - Get Single Entity

Fetches a single entity by ID.

```typescript
const country = await countryApi.get(1)
// Returns: EntityGetResponse<Country>
```

**Response Format:**
```typescript
interface EntityGetResponse<T> {
  data: T        // The entity data
  success: boolean
}
```

#### `add(payload)` - Create Entity

Creates a new entity.

```typescript
const newCountry = await countryApi.add({
  name: 'Germany',
  isoCode: 'DE',
  active: true
})
// Returns: EntityGetResponse<Country>
```

**Error Handling:**
```typescript
try {
  const result = await countryApi.add(data)
  console.log('Created:', result.data)
} catch (error) {
  console.error('Failed to create:', error.message)
}
```

#### `save(payload)` - Update Entity

Updates an existing entity.

```typescript
const updated = await countryApi.save({
  id: 1,
  name: 'Germany (Updated)',
  isoCode: 'DE',
  active: true
})
// Returns: EntityGetResponse<Country>
```

#### `delete(id)` - Delete Entity

Deletes an entity by ID.

```typescript
const result = await countryApi.delete(1)
// Returns: { success: boolean }
```

### Complete Example

```typescript
// api.ts
import { EntityApi } from '@coreshop/resource/src/entities'
import type { Country } from './types'

export class CountryApi extends EntityApi<Country> {}

export const countryApi = new CountryApi({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/countries'
})

// Usage in component
import { countryApi } from './api'

const MyComponent = () => {
  const [countries, setCountries] = useState([])

  useEffect(() => {
    const loadCountries = async () => {
      try {
        const data = await countryApi.list()
        setCountries(data)
      } catch (error) {
        console.error('Failed to load countries:', error)
      }
    }

    loadCountries()
  }, [])

  const handleCreate = async () => {
    const result = await countryApi.add({
      name: 'Germany',
      isoCode: 'DE'
    })
    console.log('Created:', result.data)
  }

  const handleUpdate = async (id, data) => {
    const result = await countryApi.save({ id, ...data })
    console.log('Updated:', result.data)
  }

  const handleDelete = async (id) => {
    await countryApi.delete(id)
    console.log('Deleted')
  }

  return (
    // ... your UI
  )
}
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

### Return Values

```typescript
const [options, value, handleSelectChange, loading] = useEntitySelect(api, selectedIds, labelKey)
```

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
4. **Handles missing entities** (for saved selections not in the list)
5. **Manages loading state**
6. **Syncs with prop changes** (updates when selectedIds change)

### Complete Example

```typescript
import React from 'react'
import { Form, Select } from 'antd'
import { useEntitySelect } from '@coreshop/resource'
import { countryApi } from '@coreshop/address/src/modules/countries/api'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'

interface CountriesConditionData {
  countries?: number[]
}

export const CountriesCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const conditionData = data as CountriesConditionData
  const selectedIds = conditionData.countries || []

  const [options, value, handleSelectChange, loading] = useEntitySelect(
    countryApi,
    selectedIds
  )

  const handleChange = (ids: number[]) => {
    handleSelectChange(ids)
    onChange({ ...conditionData, countries: ids })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Countries">
        <Select
          mode="multiple"
          value={value}
          onChange={handleChange}
          placeholder="Select countries"
          style={{ width: '100%' }}
          loading={loading}
          showSearch
          optionFilterProp="label"
          options={options}
        />
      </Form.Item>
    </Form>
  )
}
```

## EntityTabbedManager Pattern

The `EntityTabbedManager` component provides a complete CRUD interface with a split view: entity list on the left, detail form on the right.

### Basic Usage

```typescript
import React from 'react'
import { EntityTabbedManager } from '@coreshop/resource'
import { countryApi } from './api'
import { CountryForm } from './CountryForm'

export const CountryManager: React.FC = () => {
  return (
    <EntityTabbedManager
      api={countryApi}
      detailComponent={CountryForm}
      listColumns={[
        { key: 'name', title: 'Name', dataIndex: 'name' },
        { key: 'isoCode', title: 'ISO Code', dataIndex: 'isoCode' },
        { key: 'active', title: 'Active', dataIndex: 'active' }
      ]}
    />
  )
}
```

### Props

```typescript
interface EntityTabbedManagerProps<T> {
  api: EntityApi<T>                    // API instance
  detailComponent: React.ComponentType // Form component for editing
  listColumns?: ColumnType[]           // Columns for list view
  defaultValues?: Partial<T>           // Default values for new entities
  validateBeforeSave?: (data: T) => boolean | Promise<boolean>
}
```

### List Columns

Define which columns to show in the list view:

```typescript
listColumns={[
  {
    key: 'name',              // Unique key
    title: 'Name',            // Column header
    dataIndex: 'name',        // Field in entity data
    width: 200,               // Optional: column width
    render: (text, record) => // Optional: custom render
      <strong>{text}</strong>
  },
  {
    key: 'active',
    title: 'Active',
    dataIndex: 'active',
    render: (active) => active ? '✓' : '✗'
  }
]}
```

### Detail Component

The detail component receives props for editing the selected entity:

```typescript
import type { EntityDetailComponentProps } from '@coreshop/resource'

interface CountryDetailProps extends EntityDetailComponentProps<Country> {
  // data: Country | null - Current entity data
  // onChange: (data: Country) => void - Update handler
  // onSave: () => void - Save handler
  // onDelete: () => void - Delete handler
}

export const CountryForm: React.FC<CountryDetailProps> = ({
  data,
  onChange,
  onSave,
  onDelete
}) => {
  if (!data) {
    return <div>Select an entity to edit</div>
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Name" required>
        <Input
          value={data.name || ''}
          onChange={(e) => onChange({ ...data, name: e.target.value })}
        />
      </Form.Item>

      <Form.Item label="ISO Code" required>
        <Input
          value={data.isoCode || ''}
          onChange={(e) => onChange({ ...data, isoCode: e.target.value })}
        />
      </Form.Item>

      <Form.Item label="Active">
        <Switch
          checked={data.active || false}
          onChange={(checked) => onChange({ ...data, active: checked })}
        />
      </Form.Item>

      <Space>
        <Button type="primary" onClick={onSave}>Save</Button>
        <Button danger onClick={onDelete}>Delete</Button>
      </Space>
    </Form>
  )
}
```

### Features

The EntityTabbedManager provides:

- ✅ **List View** with search and filtering
- ✅ **Create** new entities
- ✅ **Edit** existing entities
- ✅ **Delete** entities with confirmation
- ✅ **Auto-save** on form changes (optional)
- ✅ **Validation** before save
- ✅ **Error handling** and notifications
- ✅ **Loading states**

### Validation

Add validation before saving:

```typescript
<EntityTabbedManager
  api={countryApi}
  detailComponent={CountryForm}
  validateBeforeSave={(data) => {
    if (!data.name) {
      message.error('Name is required')
      return false
    }
    if (!data.isoCode) {
      message.error('ISO Code is required')
      return false
    }
    return true
  }}
/>
```

### Default Values

Set default values for new entities:

```typescript
<EntityTabbedManager
  api={countryApi}
  detailComponent={CountryForm}
  defaultValues={{
    active: true,
    currency: 'EUR'
  }}
/>
```

## Type Definitions

### EntityListItem

Base interface for list items:

```typescript
interface EntityListItem {
  id: number
  name: string
  [key: string]: any
}
```

### EntityGetResponse

Response format for get/add/save operations:

```typescript
interface EntityGetResponse<T> {
  data: T
  success: boolean
}
```

### EntityListResponse

Response format for list operations (extends Array):

```typescript
interface EntityListResponse<T extends EntityListItem = EntityListItem>
  extends Array<T> {}
```

### EntityApiConfig

Configuration for EntityApi:

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

## Real-World Examples

### Countries (AddressBundle)

```typescript
// api.ts
export class CountryApi extends EntityApi<Country> {}
export const countryApi = new CountryApi({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/countries'
})

// CountryManager.tsx
export const CountryManager = () => (
  <EntityTabbedManager
    api={countryApi}
    detailComponent={CountryForm}
    listColumns={[
      { key: 'name', title: 'Name', dataIndex: 'name' },
      { key: 'isoCode', title: 'ISO', dataIndex: 'isoCode' },
      { key: 'active', title: 'Active', dataIndex: 'active' }
    ]}
  />
)
```

### Tax Rates (TaxationBundle)

```typescript
// api.ts
export class TaxRateApi extends EntityApi<TaxRate> {}
export const taxRateApi = new TaxRateApi({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/tax-rates'
})

// TaxRateManager.tsx
export const TaxRateManager = () => (
  <EntityTabbedManager
    api={taxRateApi}
    detailComponent={TaxRateForm}
    defaultValues={{ rate: 0 }}
  />
)
```

### Zones (AddressBundle)

```typescript
// api.ts
export class ZoneApi extends EntityApi<Zone> {}
export const zoneApi = new ZoneApi({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/zones'
})

// ZoneManager.tsx
export const ZoneManager = () => (
  <EntityTabbedManager
    api={zoneApi}
    detailComponent={ZoneForm}
    listColumns={[
      { key: 'name', title: 'Name', dataIndex: 'name' },
      { key: 'active', title: 'Active', dataIndex: 'active' }
    ]}
  />
)
```

## Best Practices

### API Client

1. **Extend EntityApi** for all entity APIs
2. **Use TypeScript generics** for type safety
3. **Export a singleton instance** for reuse

```typescript
// ✅ GOOD
export class CountryApi extends EntityApi<Country> {}
export const countryApi = new CountryApi({ ... })

// ❌ BAD - creating instances everywhere
new EntityApi({ ... })
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

### Loading States

Show loading indicators during API calls:

```typescript
const [loading, setLoading] = useState(false)

const handleSave = async () => {
  setLoading(true)
  try {
    await api.save(data)
  } finally {
    setLoading(false)
  }
}

<Button loading={loading} onClick={handleSave}>Save</Button>
```

### Type Safety

Define proper TypeScript interfaces:

```typescript
interface Country {
  id: number
  name: string
  isoCode: string
  active: boolean
  currency?: string
  zone?: number
}

// Type-safe API
const countryApi: EntityApi<Country> = new CountryApi({ ... })
```

## Next Steps

- [RuleBundle](02_RuleBundle.md) - Learn about the Rule system infrastructure
- [Building CRUD Features](03_Entity_CRUD.md) - Step-by-step guide to building CRUD features
- [Components](../03_Components/01_EntityTabbedManager.md) - Deep dive into EntityTabbedManager

a# Countries

In CoreShop, managing countries through the API involves several operations including create, read, update, and delete.
Below are the guidelines for each of these operations.

## Backend (PHP)

### Create

To create a new country via API:

```php
$newCountry = $container->get('coreshop.factory.country')->createNew();
```

After creating a new Country instance, persist it using:

```php
$container->get('coreshop.manager.country')->persist($newCountry);
$container->get('coreshop.manager.country')->flush();
```

You now have a new persisted country.

### Read

To query for countries:

```php
$countryRepository = $container->get('coreshop.repository.country');
$queryBuilder = $countryRepository->createQueryBuilder('c');
// You can now create your query
// And get the result
$countries = $queryBuilder->getQuery()->getResult();
```

### Update

To update an existing country:

```php
// Fetch Country
$country = $countryRepository->findById(1);
$country->setName('Euro');
// And Persist it
$container->get('coreshop.manager.country')->persist($country);
$container->get('coreshop.manager.country')->flush();
```

### Delete

To delete an existing country:

```php
// Fetch Country
$country = $countryRepository->findById(1);
// And Remove it
$container->get('coreshop.manager.country')->remove($country);
$container->get('coreshop.manager.country')->flush();
```

## Studio (React)

The CoreShop Studio provides a complete UI for managing countries with support for localization, zones, and drag-and-drop.

### CountryManager Component

The `CountryManager` uses the `GroupedEntityTabbedManager` pattern, allowing countries to be grouped by zones.

```typescript
// src/CoreShop/Bundle/AddressBundle/Resources/assets/pimcore-studio/src/modules/countries/CountryManager.tsx

import React from 'react'
import { GroupedEntityTabbedManager } from '@coreshop/resource/src/entities/components/GroupedEntityTabbedManager'
import { countryApi } from './api'
import { zoneApi } from '../zones/api'
import { CountryForm } from './CountryForm'

export const CountryManager: React.FC = () => {
  return (
    <GroupedEntityTabbedManager
      api={countryApi}
      dragType='coreshop:country'
      localizable
      loadGroups={async () => await zoneApi.list()}
      resolveGroupId={(item, groups) => item.zone ?? null}
      applyGroup={(data, groupId) => ({ ...data, zone: groupId ?? undefined })}
      onAdd={async (groupId?: number) => {
        const name = prompt('Country name:')
        if (!name) return 0
        const res = await countryApi.add({ name, ...(groupId ? { zone: groupId } : {}) })
        return res.data.id
      }}
      renderDetail={(data, setData, zones, ctx) => (
        <CountryForm
          data={data}
          zones={zones}
          onChange={setData}
          currentLocale={ctx?.currentLocale ?? 'en'}
        />
      )}
    />
  )
}
```

### API Client

```typescript
// src/CoreShop/Bundle/AddressBundle/Resources/assets/pimcore-studio/src/modules/countries/api.ts

import { EntityApi } from '@coreshop/resource/src/entities'

export interface CountryDetail {
  id: number
  name: string
  active: boolean
  zone?: number
  isoCode?: string
  addressFormat?: string
  salutations?: string[]
  currency?: number
  translations?: Record<string, { locale: string, name: string }>
}

export const countryApi = new EntityApi<CountryDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/countries'
})
```

**Available Methods:**
- `countryApi.list()` - Get all countries
- `countryApi.get(id)` - Get single country with full details
- `countryApi.add(data)` - Create new country
- `countryApi.save(data)` - Update existing country
- `countryApi.delete(id)` - Delete country

### CountryForm Component

```typescript
// src/CoreShop/Bundle/AddressBundle/Resources/assets/pimcore-studio/src/modules/countries/CountryForm.tsx

import React from 'react'
import { Form, Input, Select, Switch } from 'antd'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'
import type { CountryDetail } from './api'

export const CountryForm: React.FC<{
  data?: CountryDetail
  zones: Array<{ id: number, name: string }>
  onChange: (draft: Partial<CountryDetail>) => void
  currentLocale: string
}> = ({ data, zones, onChange, currentLocale }) => {
  const [form] = Form.useForm()

  return (
    <Form
      form={form}
      layout="vertical"
      onValuesChange={(_, allValues) => onChange(allValues)}
    >
      <Form.Item
        label={`Name (${currentLocale.toUpperCase()})`}
        name={['translations', currentLocale, 'name']}
        rules={[{ required: true }]}
      >
        <Input placeholder="Country name" />
      </Form.Item>

      <Form.Item label="ISO Code" name="isoCode">
        <Input placeholder="ISO code (e.g., DE, US)" />
      </Form.Item>

      <Form.Item label="Zone">
        <DroppableEntity
          accept="coreshop:zone"
          onDrop={(info) => {
            const id = info?.data?.id
            if (typeof id === 'number') {
              form.setFieldsValue({ zone: id })
              onChange({ zone: id })
            }
          }}
        >
          <Form.Item name="zone" noStyle>
            <Select
              options={zones.map(z => ({ value: z.id, label: z.name }))}
              placeholder="Select or drop a zone"
            />
          </Form.Item>
        </DroppableEntity>
      </Form.Item>

      <Form.Item label="Address Format" name="addressFormat">
        <Input.TextArea
          autoSize={{ minRows: 6, maxRows: 16 }}
          placeholder="Address format template"
        />
      </Form.Item>

      <Form.Item label="Salutations" name="salutations">
        <Select
          mode="tags"
          placeholder="Add salutations (e.g., mr, mrs)"
        />
      </Form.Item>

      <Form.Item label="Active" name="active" valuePropName="checked">
        <Switch />
      </Form.Item>
    </Form>
  )
}
```

### Features

The Country Manager provides:

- ✅ **Grouped by Zones** - Countries organized by their assigned zone
- ✅ **Localization Support** - Multi-language country names via translations
- ✅ **Drag & Drop** - Drag zones into the zone field
- ✅ **CRUD Operations** - Create, read, update, delete countries
- ✅ **ISO Code Support** - Store ISO country codes (DE, US, etc.)
- ✅ **Address Formatting** - Custom address format templates
- ✅ **Salutations** - Manage available salutations per country
- ✅ **Currency Assignment** - Link country to default currency
- ✅ **Extension Slots** - Other bundles can add custom fields

### GroupedEntityTabbedManager

The `GroupedEntityTabbedManager` extends `EntityTabbedManager` with grouping support:

**Key Props:**
- `loadGroups` - Async function to load groups (zones)
- `resolveGroupId` - Extract group ID from list item
- `applyGroup` - Apply group ID to entity data
- `dragType` - Enable drag-and-drop (`coreshop:country`)
- `localizable` - Enable multi-language support

**Benefits:**
- Visual grouping in the list view
- Assign entities to groups
- Create entities directly in a group
- Drag-and-drop support between groups

### Usage in Menu

```typescript
// src/CoreShop/Bundle/AddressBundle/Resources/assets/pimcore-studio/src/main.ts

import { CountryManager } from './modules/countries/CountryManager'

const plugin: IAbstractPlugin = {
  name: 'coreshop-address',

  onStartup({ moduleSystem }) {
    const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)

    widgets.registerWidget({
      name: 'coreshop-countries',
      component: CountryManager
    })
  }
}
```

### See Also

- [ResourceBundle Documentation](../../14_Studio/02_Base_Infrastructure/01_ResourceBundle.md) - EntityApi and GroupedEntityTabbedManager
- [States](../03_States/01_CRUD.md) - Managing states/provinces
- [Zones](../04_Zones/01_CRUD.md) - Managing zones

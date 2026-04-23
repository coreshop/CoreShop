# States

Managing states in CoreShop involves various operations, including create, read, update, and delete. Below are the
guidelines for each of these operations.

## Backend (PHP)

### Create

To create a new state via the API:

```php
$newState = $container->get('coreshop.factory.state')->createNew();
```

After creating a new State instance, persist it using:

```php
$container->get('coreshop.manager.state')->persist($newState);
$container->get('coreshop.manager.state')->flush();
```

You now have a new persisted state.

### Read

To query for states:

```php
$stateRepository = $container->get('coreshop.repository.state');
$queryBuilder = $stateRepository->createQueryBuilder('c');
// You can now create your query
// And get the result
$states = $queryBuilder->getQuery()->getResult();
```

### Update

To update an existing state:

```php
// Fetch State
$state = $stateRepository->findById(1);
$state->setName('Euro');
// And Persist it
$container->get('coreshop.manager.state')->persist($state);
$container->get('coreshop.manager.state')->flush();
```

### Delete

To delete an existing state:

```php
// Fetch State
$state = $stateRepository->findById(1);
// And Remove it
$container->get('coreshop.manager.state')->remove($state);
$container->get('coreshop.manager.state')->flush();
```

## Studio (React)

The CoreShop Studio provides a complete UI for managing states (provinces, regions) with localization support.

### StateManager Component

The `StateManager` uses the `EntityTabbedManager` pattern with localization enabled.

```typescript
// src/CoreShop/Bundle/AddressBundle/Resources/assets/pimcore-studio/src/modules/states/StateManager.tsx

import React from 'react'
import { EntityTabbedManager } from '@coreshop/resource/src/entities'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { stateApi } from './api'
import { StateForm } from './StateForm'

export const StateManager: React.FC = () => {
  const modal = useFormModal()

  return (
    <EntityTabbedManager
      api={stateApi}
      dragType='coreshop:state'
      leftRootTitle='States'
      localizable
      onAdd={async () => {
        const name = prompt('State name:')
        if (!name) return 0
        const res = await stateApi.add({ name })
        return res.data.id
      }}
      renderDetail={(data, setData, ctx) => (
        <StateForm
          data={data}
          currentLocale={ctx?.currentLocale ?? 'en'}
          onChange={setData}
        />
      )}
    />
  )
}
```

### API Client

```typescript
// src/CoreShop/Bundle/AddressBundle/Resources/assets/pimcore-studio/src/modules/states/api.ts

import { EntityApi } from '@coreshop/resource/src/entities'

export interface StateDetail {
  id: number
  name: string
  active: boolean
  isoCode?: string
  country?: number
  translations?: Record<string, { locale: string, name: string }>
}

export const stateApi = new EntityApi<StateDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/states'
})
```

**Available Methods:**
- `stateApi.list()` - Get all states
- `stateApi.get(id)` - Get single state with full details
- `stateApi.add(data)` - Create new state
- `stateApi.save(data)` - Update existing state
- `stateApi.delete(id)` - Delete state

### StateForm Component

```typescript
// src/CoreShop/Bundle/AddressBundle/Resources/assets/pimcore-studio/src/modules/states/StateForm.tsx

import React from 'react'
import { Form, Input, Switch } from 'antd'
import { CountrySelect } from '../../components/CountrySelect'
import type { StateDetail } from './api'

export const StateForm: React.FC<{
  data?: StateDetail
  onChange: (draft: Partial<StateDetail>) => void
  currentLocale: string
}> = ({ data, onChange, currentLocale }) => {
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
        <Input placeholder="State name" />
      </Form.Item>

      <Form.Item label="ISO Code" name="isoCode">
        <Input placeholder="ISO code (optional)" />
      </Form.Item>

      <Form.Item label="Country" name="country">
        <CountrySelect />
      </Form.Item>

      <Form.Item label="Active" name="active" valuePropName="checked">
        <Switch />
      </Form.Item>
    </Form>
  )
}
```

### CountrySelect Component

The `CountrySelect` is a reusable component that uses `useEntitySelect` to provide country selection:

```typescript
// src/CoreShop/Bundle/AddressBundle/Resources/assets/pimcore-studio/src/components/CountrySelect.tsx

import React from 'react'
import { Select } from 'antd'
import { useEntitySelect } from '@coreshop/resource'
import { countryApi } from '../modules/countries/api'

export const CountrySelect: React.FC<{
  value?: number
  onChange?: (value: number) => void
}> = ({ value, onChange }) => {
  const [options, , , loading] = useEntitySelect(
    countryApi,
    value ? [value] : []
  )

  return (
    <Select
      value={value}
      onChange={onChange}
      options={options}
      loading={loading}
      placeholder="Select a country"
      showSearch
      optionFilterProp="label"
    />
  )
}
```

### Features

The State Manager provides:

- ✅ **Localization Support** - Multi-language state names via translations
- ✅ **Country Assignment** - Link state to a country
- ✅ **CRUD Operations** - Create, read, update, delete states
- ✅ **ISO Code Support** - Store ISO state codes (e.g., CA, NY)
- ✅ **Drag & Drop** - Drag-and-drop support (`coreshop:state`)
- ✅ **Extension Slots** - Other bundles can add custom fields
- ✅ **Active Toggle** - Enable/disable states

### EntityTabbedManager with Localization

When `localizable` is set to `true`, the EntityTabbedManager provides:

- Language selector in the toolbar
- Automatic translation management
- Current locale passed to detail component
- Multi-language support for entity names

### Usage in Menu

```typescript
// src/CoreShop/Bundle/AddressBundle/Resources/assets/pimcore-studio/src/main.ts

import { StateManager } from './modules/states/StateManager'

const plugin: IAbstractPlugin = {
  name: 'coreshop-address',

  onStartup({ moduleSystem }) {
    const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)

    widgets.registerWidget({
      name: 'coreshop-states',
      component: StateManager
    })
  }
}
```

### See Also

- [ResourceBundle Documentation](../../14_Studio/02_Base_Infrastructure/01_ResourceBundle.md) - EntityApi and EntityTabbedManager
- [Countries](../02_Countries/01_CRUD.md) - Managing countries
- [Zones](../04_Zones/01_CRUD.md) - Managing zones

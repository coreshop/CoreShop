# Zones

Managing zones in CoreShop involves various operations, including create, read, update, and delete. Below are the
guidelines for each of these operations.

## Backend (PHP)

### Create

To create a new zone via the API:

```php
$newZone = $container->get('coreshop.factory.zone')->createNew();
```

After creating a new Zone instance, persist it using:

```php
$container->get('coreshop.manager.zone')->persist($newZone);
$container->get('coreshop.manager.zone')->flush();
```

You now have a new persisted zone.

### Read

To query for zones:

```php
$zoneRepository = $container->get('coreshop.repository.zone');
$queryBuilder = $zoneRepository->createQueryBuilder('c');
// You can now create your query
// And get the result
$zones = $queryBuilder->getQuery()->getResult();
```

### Update

To update an existing zone:

```php
// Fetch Zone
$zone = $zoneRepository->findById(1);
$zone->setName('Euro');
// And Persist it
$container->get('coreshop.manager.zone')->persist($zone);
$container->get('coreshop.manager.zone')->flush();
```

### Delete

To delete an existing zone:

```php
// Fetch Zone
$zone = $zoneRepository->findById(1);
// And Remove it
$container->get('coreshop.manager.zone')->remove($zone);
$container->get('coreshop.manager.zone')->flush();
```

## Studio (React)

The CoreShop Studio provides a simple UI for managing zones (geographic regions).

### ZoneManager Component

The `ZoneManager` uses the basic `EntityTabbedManager` pattern.

```typescript
// src/CoreShop/Bundle/AddressBundle/Resources/assets/pimcore-studio/src/modules/zones/ZoneManager.tsx

import React from 'react'
import { EntityTabbedManager } from '@coreshop/resource/src/entities'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { zoneApi } from './api'
import { ZoneForm } from './ZoneForm'

export const ZoneManager: React.FC = () => {
  const modal = useFormModal()

  return (
    <EntityTabbedManager
      api={zoneApi}
      dragType='coreshop:zone'
      leftRootTitle='Zones'
      buildSavePayload={(data) => ({
        id: data.id,
        name: data.name,
        active: data.active
      })}
      onAdd={async () => {
        const name = prompt('Zone name:')
        if (!name) return 0
        const res = await zoneApi.add({ name })
        return res.data.id
      }}
      renderDetail={(data, setData) => (
        <ZoneForm data={data} onChange={setData} />
      )}
    />
  )
}
```

### API Client

```typescript
// src/CoreShop/Bundle/AddressBundle/Resources/assets/pimcore-studio/src/modules/zones/api.ts

import { EntityApi } from '@coreshop/resource/src/entities'

export interface ZoneDetail {
  id: number
  name: string
  active: boolean
}

export const zoneApi = new EntityApi<ZoneDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/zones'
})
```

**Available Methods:**
- `zoneApi.list()` - Get all zones
- `zoneApi.get(id)` - Get single zone
- `zoneApi.add(data)` - Create new zone
- `zoneApi.save(data)` - Update existing zone
- `zoneApi.delete(id)` - Delete zone

### ZoneForm Component

```typescript
// src/CoreShop/Bundle/AddressBundle/Resources/assets/pimcore-studio/src/modules/zones/ZoneForm.tsx

import React from 'react'
import { Form, Input, Switch } from 'antd'
import type { ZoneDetail } from './api'

export const ZoneForm: React.FC<{
  data?: ZoneDetail
  onChange: (draft: Partial<ZoneDetail>) => void
}> = ({ data, onChange }) => {
  const [form] = Form.useForm()

  return (
    <Form
      form={form}
      layout="vertical"
      onValuesChange={(_, allValues) => onChange(allValues)}
    >
      <Form.Item label="Name" name="name" rules={[{ required: true }]}>
        <Input placeholder="Zone name" />
      </Form.Item>

      <Form.Item label="Active" name="active" valuePropName="checked">
        <Switch />
      </Form.Item>
    </Form>
  )
}
```

### Features

The Zone Manager provides:

- ✅ **Simple CRUD** - Create, read, update, delete zones
- ✅ **Drag & Drop** - Drag-and-drop support (`coreshop:zone`)
- ✅ **Active Toggle** - Enable/disable zones
- ✅ **Minimal Configuration** - Only name and active status required
- ✅ **Country Grouping** - Countries can be assigned to zones

### Zones as Groups

Zones are used throughout CoreShop as grouping entities:

- **Countries** - Grouped by zones (EU, North America, etc.)
- **Shipping Rules** - Can target specific zones
- **Tax Rules** - Can apply to zones
- **Price Rules** - Can be restricted by zone

### Drag-and-Drop Type

The `dragType='coreshop:zone'` enables zones to be:

- Dragged from the zone list
- Dropped into country forms
- Used in other zone-aware components

### Usage in Menu

```typescript
// src/CoreShop/Bundle/AddressBundle/Resources/assets/pimcore-studio/src/main.ts

import { ZoneManager } from './modules/zones/ZoneManager'

const plugin: IAbstractPlugin = {
  name: 'coreshop-address',

  onStartup({ moduleSystem }) {
    const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)

    widgets.registerWidget({
      name: 'coreshop-zones',
      component: ZoneManager
    })
  }
}
```

### See Also

- [ResourceBundle Documentation](../../14_Studio/02_Base_Infrastructure/01_ResourceBundle.md) - EntityApi and EntityTabbedManager
- [Countries](../02_Countries/01_CRUD.md) - Managing countries (uses zones for grouping)
- [States](../03_States/01_CRUD.md) - Managing states

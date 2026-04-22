# Tax Rates

Managing tax rates in CoreShop involves various operations, including create, read, update, and delete. Below are the
guidelines for each of these operations.

## Backend (PHP)

### Create

To create a new tax rate via the API:

```php
$newTaxRate = $container->get('coreshop.factory.tax_rate')->createNew();
```

After creating a new Tax Rate instance, persist it using:

```php
$container->get('coreshop.manager.tax_rate')->persist($newTaxRate);
$container->get('coreshop.manager.tax_rate')->flush();
```

You now have a new persisted tax rate.

### Read

To query for tax rates:

```php
$rateRepository = $container->get('coreshop.repository.tax_rate');
$queryBuilder = $rateRepository->createQueryBuilder('c');
// You can now create your query
// And get the result
$rates = $queryBuilder->getQuery()->getResult();
```

### Update

To update an existing tax rate:

```php
// Fetch Tax Rate
$rate = $rateRepository->findById(1);
$rate->setName('Euro');
// And Persist it
$container->get('coreshop.manager.tax_rate')->persist($rate);
$container->get('coreshop.manager.tax_rate')->flush();
```

### Delete

To delete an existing tax rate:

```php
// Fetch Tax Rate
$rate = $rateRepository->findById(1);
// And Remove it
$container->get('coreshop.manager.tax_rate')->remove($rate);
$container->get('coreshop.manager.tax_rate')->flush();
```

## Studio (React)

The CoreShop Studio provides a UI for managing tax rates with localization and percentage configuration.

### TaxRateManager Component

The `TaxRateManager` uses the `EntityTabbedManager` pattern with localization enabled.

```typescript
// src/CoreShop/Bundle/TaxationBundle/Resources/assets/pimcore-studio/src/modules/tax-rates/TaxRateManager.tsx

import React from 'react'
import { EntityTabbedManager } from '@coreshop/resource/src/entities'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { taxRateApi } from './api'
import { TaxRateForm } from './TaxRateForm'

export const TaxRateManager: React.FC = () => {
  const modal = useFormModal()

  return (
    <EntityTabbedManager
      api={taxRateApi}
      dragType='coreshop:tax_rate'
      leftRootTitle='Tax Rates'
      localizable
      buildSavePayload={(data) => ({
        id: data.id,
        name: data.name,
        rate: data.rate,
        active: data.active,
        translations: data.translations
      })}
      onAdd={async () => {
        const name = prompt('Tax rate name:')
        if (!name) return 0
        const res = await taxRateApi.add({ name })
        return res.data.id
      }}
      renderDetail={(data, setData, ctx) => (
        <TaxRateForm
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
// src/CoreShop/Bundle/TaxationBundle/Resources/assets/pimcore-studio/src/modules/tax-rates/api.ts

import { EntityApi } from '@coreshop/resource/src/entities'

export interface TaxRateTranslation {
  locale: string
  name: string
}

export interface TaxRateDetail {
  id: number
  name: string
  rate: number
  active: boolean
  translations: Record<string, TaxRateTranslation>
}

export const taxRateApi = new EntityApi<TaxRateDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/tax_rates'
})
```

**Available Methods:**
- `taxRateApi.list()` - Get all tax rates
- `taxRateApi.get(id)` - Get single tax rate
- `taxRateApi.add(data)` - Create new tax rate
- `taxRateApi.save(data)` - Update existing tax rate
- `taxRateApi.delete(id)` - Delete tax rate

### TaxRateForm Component

```typescript
// src/CoreShop/Bundle/TaxationBundle/Resources/assets/pimcore-studio/src/modules/tax-rates/TaxRateForm.tsx

import React from 'react'
import { Form, Input, InputNumber, Switch } from 'antd'
import type { TaxRateDetail } from './api'

export const TaxRateForm: React.FC<{
  data?: TaxRateDetail
  onChange: (draft: Partial<TaxRateDetail>) => void
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
        <Input placeholder="Tax rate name" />
      </Form.Item>

      <Form.Item
        label="Rate (%)"
        name="rate"
        rules={[{ required: true }]}
      >
        <InputNumber
          min={0}
          max={100}
          step={0.01}
          placeholder="Tax rate percentage"
          style={{ width: '100%' }}
          addonAfter="%"
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

The Tax Rate Manager provides:

- ✅ **Localization Support** - Multi-language tax rate names
- ✅ **Percentage Input** - Rate input with % suffix (0-100 with 2 decimal places)
- ✅ **CRUD Operations** - Create, read, update, delete tax rates
- ✅ **Drag & Drop** - Drag-and-drop support (`coreshop:tax_rate`)
- ✅ **Active Toggle** - Enable/disable tax rates
- ✅ **Extension Slots** - Other bundles can add custom fields

### Tax Rate Percentage

The tax rate is stored as a percentage value:

- **Minimum:** 0%
- **Maximum:** 100%
- **Precision:** 2 decimal places (e.g., 19.50%)
- **Display:** InputNumber with % suffix

**Common Tax Rates:**
- Standard VAT (EU): 19%, 20%, 21%, 23%
- Reduced VAT (EU): 5%, 7%, 10%
- US Sales Tax: 0% - 10% (varies by state)
- Canada GST/HST: 5% - 15%

### Usage in Menu

```typescript
// src/CoreShop/Bundle/TaxationBundle/Resources/assets/pimcore-studio/src/main.ts

import { TaxRateManager } from './modules/tax-rates/TaxRateManager'

const plugin: IAbstractPlugin = {
  name: 'coreshop-taxation',

  onStartup({ moduleSystem }) {
    const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)

    widgets.registerWidget({
      name: 'coreshop-tax-rates',
      component: TaxRateManager
    })
  }
}
```

### See Also

- [ResourceBundle Documentation](../../../../14_Studio/02_Base_Infrastructure/01_ResourceBundle.md) - EntityApi and EntityTabbedManager
- [Tax Rule Groups](../02_Tax_Rule/01_CRUD.md) - Managing tax rule groups (combines tax rates)

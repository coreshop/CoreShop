# Currencies

## Backend (PHP)

### Create

If you want to create a Currency via API, you can do following:

```php
$newCurrency = $container->get('coreshop.factory.currency')->createNew();
```

Now you have a new Currency, if you want to persist it, you need to do following:

```php
$container->get('coreshop.manager.currency')->persist($newCurrency);
$container->get('coreshop.manager.currency')->flush();
```

You now have a new persisted Currency.

### Read

If you want to query for Currencies, you can do following:

```php
$currencyRepository = $container->get('coreshop.repository.currency');

$queryBuilder = $currencyRepository->createQueryBuilder('c');

// You can now create your query

// And get the result

$currencies = $queryBuilder->getQuery()->getResult();

```

### Update

If you want to update an existing Currency, you need to do following:

```php
// Fetch Currency

$currency = $currencyRepository->findById(1);
$currency->setName('Euro');

// And Persist it
$container->get('coreshop.manager.currency')->persist($currency);
$container->get('coreshop.manager.currency')->flush();
```

### Delete

If you want to delete an existing Currency, you need to do following:

```php
// Fetch Currency

$currency = $currencyRepository->findById(1);

// And remove it
$container->get('coreshop.manager.currency')->remove($currency);
$container->get('coreshop.manager.currency')->flush();
```

## Studio (React)

The CoreShop Studio provides a UI for managing currencies with support for ISO codes and symbols.

### CurrencyManager Component

The `CurrencyManager` uses the `EntityTabbedManager` pattern.

```typescript
// src/CoreShop/Bundle/CurrencyBundle/Resources/assets/pimcore-studio/src/modules/currencies/CurrencyManager.tsx

import React from 'react'
import { EntityTabbedManager } from '@coreshop/resource/src/entities'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { currencyApi } from './api'
import { CurrencyForm } from './CurrencyForm'

export const CurrencyManager: React.FC = () => {
  const modal = useFormModal()

  return (
    <EntityTabbedManager
      api={currencyApi}
      dragType='coreshop:currency'
      leftRootTitle='Currencies'
      buildSavePayload={(data) => ({
        id: data.id,
        name: data.name,
        isoCode: data.isoCode,
        numericIsoCode: data.numericIsoCode,
        symbol: data.symbol
      })}
      onAdd={async () => {
        const name = prompt('Currency name:')
        if (!name) return 0
        const res = await currencyApi.add({ name })
        return res.data.id
      }}
      renderDetail={(data, setData) => (
        <CurrencyForm data={data} onChange={setData} />
      )}
    />
  )
}
```

### API Client

```typescript
// src/CoreShop/Bundle/CurrencyBundle/Resources/assets/pimcore-studio/src/modules/currencies/api.ts

import { EntityApi } from '@coreshop/resource/src/entities'

export interface CurrencyDetail {
  id: number
  name: string
  isoCode?: string
  numericIsoCode?: number
  symbol?: string
}

export const currencyApi = new EntityApi<CurrencyDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/currencies'
})
```

**Available Methods:**
- `currencyApi.list()` - Get all currencies
- `currencyApi.get(id)` - Get single currency
- `currencyApi.add(data)` - Create new currency
- `currencyApi.save(data)` - Update existing currency
- `currencyApi.delete(id)` - Delete currency

### CurrencyForm Component

```typescript
// src/CoreShop/Bundle/CurrencyBundle/Resources/assets/pimcore-studio/src/modules/currencies/CurrencyForm.tsx

import React from 'react'
import { Form, Input, InputNumber } from 'antd'
import type { CurrencyDetail } from './api'

export const CurrencyForm: React.FC<{
  data?: CurrencyDetail
  onChange: (draft: Partial<CurrencyDetail>) => void
}> = ({ data, onChange }) => {
  const [form] = Form.useForm()

  return (
    <Form
      form={form}
      layout="vertical"
      onValuesChange={(_, allValues) => onChange(allValues)}
    >
      <Form.Item label="Name" name="name" rules={[{ required: true }]}>
        <Input placeholder="Currency name" />
      </Form.Item>

      <Form.Item label="ISO Code" name="isoCode">
        <Input placeholder="ISO 4217 code (e.g., EUR)" />
      </Form.Item>

      <Form.Item label="Numeric ISO Code" name="numericIsoCode">
        <InputNumber
          style={{ width: '100%' }}
          placeholder="Numeric ISO (e.g., 978)"
        />
      </Form.Item>

      <Form.Item label="Symbol" name="symbol">
        <Input placeholder="Symbol (e.g., €)" />
      </Form.Item>
    </Form>
  )
}
```

### Features

The Currency Manager provides:

- ✅ **ISO 4217 Support** - Store ISO currency codes (EUR, USD, etc.)
- ✅ **Numeric ISO Codes** - ISO 4217 numeric codes
- ✅ **Currency Symbols** - Store and display currency symbols (€, $, £)
- ✅ **CRUD Operations** - Create, read, update, delete currencies
- ✅ **Drag & Drop** - Drag-and-drop support (`coreshop:currency`)

### ISO 4217 Standards

The currency form follows ISO 4217 standards:

- **ISO Code** - Three-letter alphabetic code (EUR, USD, GBP)
- **Numeric ISO** - Three-digit numeric code (978 for EUR, 840 for USD)
- **Symbol** - Currency symbol for display purposes (€, $, £)

**Examples:**
- EUR - Euro - 978 - €
- USD - United States Dollar - 840 - $
- GBP - British Pound - 826 - £
- JPY - Japanese Yen - 392 - ¥

### Usage in Menu

```typescript
// src/CoreShop/Bundle/CurrencyBundle/Resources/assets/pimcore-studio/src/main.ts

import { CurrencyManager } from './modules/currencies/CurrencyManager'

const plugin: IAbstractPlugin = {
  name: 'coreshop-currency',

  onStartup({ moduleSystem }) {
    const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)

    widgets.registerWidget({
      name: 'coreshop-currencies',
      component: CurrencyManager
    })
  }
}
```

### See Also

- [ResourceBundle Documentation](../../14_Studio/02_Base_Infrastructure/01_ResourceBundle.md) - EntityApi and EntityTabbedManager
- [Exchange Rates](../01_Currencies/02_ExchangeRates.md) - Managing currency exchange rates
- [Countries](../02_Countries/01_CRUD.md) - Countries can have default currencies

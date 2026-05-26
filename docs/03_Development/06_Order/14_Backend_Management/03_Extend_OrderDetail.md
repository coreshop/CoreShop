# Extending Order Detail

CoreShop's Order Detail view in Pimcore Studio is composed of React blocks that can be extended or replaced via the
Studio Extension System.

## UI Overview

The Studio Order Detail page is assembled from registered tab, action, and form extensions around the core sale data.
Extensions are attached through the entity key `coreshop.order.sale` (and related order-bundle keys). See the
[Extension System overview](../../14_Studio/index.md#3-extension-system) for the full slot/extension-type
table.

## Adding a Custom Tab to the Order Detail

Register a `TabExtension` targeting the sale entity to add a new React tab next to the default ones (Items, Payments,
Invoices, Shipments, Correspondence, Details):

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/order-tabs/CustomTab.tsx

import React from 'react'
import { Card } from 'antd'
import type { EntityTabComponentProps } from '@coreshop/resource/src/entities'

export const CustomOrderTab: React.FC<EntityTabComponentProps> = ({ entity }) => {
  return (
    <Card title="Custom Information">
      <div>Purchase Order Number: {entity.purchaseOrderNumber}</div>
      <div>Delivery Phone: {entity.deliveryPhone}</div>
    </Card>
  )
}
```

Register the tab in your module's `onInit()`:

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/order-tabs/index.ts

import { AbstractModule, moduleSystem } from '@pimcore/studio-ui-bundle'
import { serviceIds as resourceServiceIds } from '@coreshop/resource/src/entities'
import { CustomOrderTab } from './CustomTab'

export class CustomOrderTabsModule extends AbstractModule {
  onInit(): void {
    const tabExtensions = this.serviceContainer.get(
      resourceServiceIds.entityTabExtensionsServiceId
    )

    tabExtensions.register({
      entityKey: 'coreshop.order.sale',
      tab: {
        id: 'custom-info',
        label: 'Custom Info',
        component: CustomOrderTab,
        priority: 50,
      },
    })
  }
}
```

Activate the module from the bundle's `main.ts` via `onStartup({ moduleSystem })`.

## Adding Toolbar / Context-Menu Actions

Use an `ActionExtension` registered against the `coreshop.order.sale` entity to add buttons to the toolbar, context
menu, or footer. A complete example of all seven extension types (form, column, save decorator, tab, action,
validation, lifecycle) is shipped with the `CoreBundle`:

`src/CoreShop/Bundle/CoreBundle/Resources/assets/pimcore-studio/src/modules/extension/comprehensive-example/index.tsx`

## Customizing Order Serialization

CoreShop uses JMS Serializer for order serialization. You can extend its serialization logic either through JMS
Configuration or by registering an event handler for the `CoreShop\Bundle\OrderBundle\Events::SALE_DETAIL_PREPARE`
event.

If that isn't enough, expose a custom Studio API controller (one action per controller) and call it from your React
component with `fetch('/pimcore-studio/api/...')` or an RTK Query hook.

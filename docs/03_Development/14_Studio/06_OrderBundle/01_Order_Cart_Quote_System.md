# Order/Cart/Quote System in Pimcore Studio v2

This guide explains the Sales (Order/Cart/Quote) management system in CoreShop Studio v2, including how to extend tabs, modal fields, and the order creation wizard.

## Overview

CoreShop provides a unified system for managing Orders, Carts, and Quotes (collectively called "Sales"). The system consists of:

- **Sale Lists**: Searchable/filterable listings for Orders, Carts, Quotes
- **Sale Detail Views**: Tabbed interface showing all sale information
- **Tab Registry**: Extensible system for adding custom tabs/panels
- **Modal Field Extensions**: Add fields to action modals (Create Payment, Create Shipment, etc.)
- **Order Creation Wizard**: Multi-step wizard for creating orders from the backend

## Architecture

### Sale Types

```typescript
export type SaleType = 'order' | 'cart' | 'quote'
```

| Type | Description | Features |
|------|-------------|----------|
| `order` | Completed/processing orders | Payments, Shipments, Invoices, State management |
| `cart` | Shopping carts | Can be converted to orders |
| `quote` | Price quotes | Can be converted to orders |

### Service IDs

```typescript
// src/CoreShop/Bundle/OrderBundle/.../service-ids.ts
export const serviceIds = {
  saleTabRegistry: Symbol.for('coreshop.order.sale_tab_registry'),
  orderApi: Symbol.for('coreshop.order.order_api'),
  cartApi: Symbol.for('coreshop.order.cart_api'),
  quoteApi: Symbol.for('coreshop.order.quote_api'),
}

// Extension service IDs
export const extensionServiceIds = {
  modalFieldExtensionRegistry: Symbol.for('coreshop.order.modal_field_extension_registry')
}

// Order creation service IDs
export const orderCreationServiceIds = {
  stepRegistry: Symbol.for('coreshop.order.order_creation_step_registry')
}
```

## Sale Tab Registry

The Sale Tab Registry allows bundles to register custom tabs that display in the sale detail view.

### Tab Structure

```typescript
export interface SaleTab {
  key: string                           // Unique identifier
  label: string                         // Display label
  icon?: string                         // Optional icon
  priority: number                      // Lower = displayed first
  position: BlockPosition               // 'top' | 'left' | 'right' | 'bottom'
  types: SaleType[]                     // Which sale types show this tab
  component: React.ComponentType<SaleTabProps>
  toolbarButtons?: React.ComponentType[]  // Optional toolbar buttons
}
```

### Layout Positions

The detail view uses a grid layout:

```
+------------------------------------------+
|               TOP (header)               |
+------------------------------------------+
|           |                   |          |
|   LEFT    |      CENTER       |  RIGHT   |
| (payment, |     (content)     |(customer,|
| shipment, |                   | comments)|
| invoice)  |                   |          |
|           |                   |          |
+------------------------------------------+
|              BOTTOM (detail)             |
+------------------------------------------+
```

### Built-in Tabs

| Key | Position | Types | Priority | Description |
|-----|----------|-------|----------|-------------|
| `header` | top | all | 10 | Order header with states and summary |
| `info` | left | all | 10 | Carrier and payment provider info |
| `payment` | left | order | 20 | Payment management |
| `shipment` | left | order | 30 | Shipment management |
| `invoice` | left | order | 40 | Invoice management |
| `customer` | right | all | 10 | Customer information |
| `comments` | right | order, quote | 20 | Order comments |
| `correspondence` | right | order | 30 | Email correspondence |
| `detail` | bottom | all | 10 | Products/items table |

### Adding a Custom Tab

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/sales/tabs/CustomTab.tsx

import React from 'react'
import { Card, Table } from 'antd'
import { useTranslation } from 'react-i18next'
import type { SaleTabProps } from '@coreshop/order/src/modules/sales/registry'
import { useSaleContext } from '@coreshop/order/src/modules/sales/context/SaleActionsContext'

export const CustomTab: React.FC<SaleTabProps> = () => {
  const { t } = useTranslation()
  const { sale, onReload } = useSaleContext()

  if (!sale) return null

  return (
    <Card title={t('coreshop_custom_tab', { defaultValue: 'Custom Data' })}>
      {/* Your custom content */}
      <Table
        dataSource={(sale as any).customData || []}
        columns={[
          { title: 'Field', dataIndex: 'field' },
          { title: 'Value', dataIndex: 'value' }
        ]}
        pagination={false}
        size="small"
      />
    </Card>
  )
}
```

### Registering the Tab

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/main.ts

import { IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import { SaleTabRegistry } from '@coreshop/order/src/modules/sales/registry'
import { serviceIds } from '@coreshop/order/src/modules/sales/service-ids'
import { CustomTab } from './modules/sales/tabs/CustomTab'

const plugin: IAbstractPlugin = {
    name: 'your-bundle',

    onInit() {
        const tabRegistry = container.get<SaleTabRegistry>(serviceIds.saleTabRegistry)

        tabRegistry.register('custom', {
            key: 'custom',
            label: 'Custom Data',
            priority: 50,           // After invoice tab
            position: 'left',       // Left column
            types: ['order'],       // Only for orders
            component: CustomTab
        })
    }
}

export default plugin
```

### Using the Sale Context

Tabs should use the `useSaleContext()` hook to access sale data:

```typescript
import { useSaleContext } from '@coreshop/order/src/modules/sales/context/SaleActionsContext'

const {
  sale,           // Current sale data
  onReload,       // Function to reload sale data
  isActionOpen,   // Check if an action modal is open
  openAction,     // Open an action modal
  closeAction,    // Close an action modal
  buttonRegistry  // Register toolbar buttons
} = useSaleContext()
```

### Adding Toolbar Buttons

Tabs can dynamically register toolbar buttons:

```typescript
export const CustomTab: React.FC<SaleTabProps> = () => {
  const { sale, buttonRegistry, openAction } = useSaleContext()

  React.useEffect(() => {
    // Register button when tab mounts
    buttonRegistry.add('customAction', CustomActionButton, 50)

    // Cleanup on unmount
    return () => buttonRegistry.remove('customAction')
  }, [buttonRegistry])

  // ... tab content
}
```

## Modal Field Extensions

Modal Field Extensions allow bundles to inject additional fields into action modals.

### Architecture

```typescript
export interface ModalFieldExtension {
  (props: any): React.ReactNode
}

export class ModalFieldExtensionRegistry {
  register(modalKey: string, extension: ModalFieldExtension): void
  getFields(modalKey: string, props: any): React.ReactNode[]
  hasExtensions(modalKey: string): boolean
}
```

### Available Modal Keys

| Key | Modal | Description |
|-----|-------|-------------|
| `create-shipment` | CreateShipmentModal | Create new shipment |
| `create-payment` | CreatePaymentModal | Create new payment |
| `create-invoice` | CreateInvoiceModal | Create new invoice |

### Adding Modal Fields

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/sales/extensions/CustomExtension.tsx

import React from 'react'
import { Form, Input } from 'antd'
import { container } from '@pimcore/studio-ui-bundle'
import { ModalFieldExtensionRegistry } from '@coreshop/order/src/modules/sales/extensions'
import { extensionServiceIds } from '@coreshop/order/src/modules/sales/extensions/service-ids'

// Extension component - receives form instance and onChange
const TrackingNumberExtension: React.FC<{
  form: any
  onChange: (values: any) => void
}> = ({ form, onChange }) => {
  return (
    <Form.Item
      label="Tracking Number"
      name="trackingNumber"
    >
      <Input
        placeholder="Enter tracking number"
        onChange={(e) => onChange({ trackingNumber: e.target.value })}
      />
    </Form.Item>
  )
}

// Registration
const plugin: IAbstractPlugin = {
    name: 'your-bundle',

    onInit() {
        const registry = container.get<ModalFieldExtensionRegistry>(
            extensionServiceIds.modalFieldExtensionRegistry
        )

        // Add field to create-shipment modal
        registry.register('create-shipment', (props) => (
            <TrackingNumberExtension {...props} />
        ))
    }
}
```

## Order Creation Wizard

The Order Creation Wizard provides a multi-step interface for creating orders from the backend.

### Step Structure

```typescript
export interface OrderCreationStepConfig {
  key: string
  title: string
  icon?: React.ReactNode
  priority: number  // Lower = earlier in wizard
  component: React.ComponentType<OrderCreationStepProps>
  validate?: (data: OrderCreationData) => Promise<boolean> | boolean
}

export interface OrderCreationStepProps {
  data: OrderCreationData
  onChange: (updates: Partial<OrderCreationData>) => void
  onNext: () => void
  onPrev: () => void
}
```

### Built-in Steps

| Key | Priority | Description |
|-----|----------|-------------|
| `base` | 10 | Customer selection, store, currency, language |
| `products` | 20 | Product selection and quantity |
| `totals` | 30 | Order summary and totals |

### Adding a Custom Step

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/modules/order-creation/steps/ShippingStep.tsx

import React from 'react'
import { Card, Form, Select } from 'antd'
import type { OrderCreationStepProps } from '@coreshop/order/src/modules/order-creation/types'

export const ShippingStep: React.FC<OrderCreationStepProps> = ({
  data,
  onChange
}) => {
  return (
    <Card title="Shipping Options">
      <Form layout="vertical">
        <Form.Item label="Shipping Method">
          <Select
            value={data.shippingMethod}
            onChange={(value) => onChange({ shippingMethod: value })}
            options={[
              { value: 'standard', label: 'Standard Shipping' },
              { value: 'express', label: 'Express Shipping' }
            ]}
          />
        </Form.Item>
      </Form>
    </Card>
  )
}

export const ShippingStepConfig: OrderCreationStepConfig = {
  key: 'shipping',
  title: 'Shipping',
  priority: 25,  // Between products and totals
  component: ShippingStep,
  validate: (data) => !!data.shippingMethod
}
```

### Registering the Step

```typescript
import { OrderCreationStepRegistry } from '@coreshop/order/src/modules/order-creation/registry'
import { orderCreationServiceIds } from '@coreshop/order/src/modules/order-creation/service-ids'
import { ShippingStepConfig } from './modules/order-creation/steps/ShippingStep'

const plugin: IAbstractPlugin = {
    name: 'your-bundle',

    onInit() {
        const stepRegistry = container.get<OrderCreationStepRegistry>(
            orderCreationServiceIds.stepRegistry
        )

        stepRegistry.register('shipping', ShippingStepConfig)
    }
}
```

## Cart Price Rules Integration

The OrderBundle also includes Cart Price Rules with a full condition/action system.

**Most cart price rule conditions and actions are schema-driven** — their forms are rendered automatically from PHP FormTypes via the StudioFormBundle. No custom React components are needed for standard conditions/actions.

### Service IDs

```typescript
export const coreshopOrderServiceIds = {
  cartPriceRuleConditionRegistry: Symbol.for('coreshop.order.cart_price_rule.condition_registry'),
  cartPriceRuleActionRegistry: Symbol.for('coreshop.order.cart_price_rule.action_registry'),
  cartItemConditionRegistry: Symbol.for('coreshop.order.cart_item.condition_registry'),
  cartItemActionRegistry: Symbol.for('coreshop.order.cart_item.action_registry')
}
```

### Built-in Conditions (All schema-driven from PHP FormTypes)

| Key | Source Bundle | Description |
|-----|-------------|-------------|
| `amount` | OrderBundle | Cart amount range |
| `voucher` | OrderBundle | Voucher code required |
| `not_combinable` | OrderBundle | Cannot combine with other rules |
| `categories` | CoreBundle | Product categories |
| `products` | CoreBundle | Specific products |
| `customers` | CoreBundle | Specific customers |
| `customerGroups` | CoreBundle | Customer groups |
| `guest` | CoreBundle | Guest checkout |
| `countries` | CoreBundle | Customer countries |
| `zones` | CoreBundle | Geographic zones |
| `stores` | CoreBundle | Store selection |
| `currencies` | CoreBundle | Currency selection |
| `carriers` | CoreBundle | Shipping carriers |

### Conditions (Hand-written — require custom JS)

| Key | Source Bundle | Reason |
|-----|-------------|--------|
| `nested` | CoreBundle | Recursively renders sub-conditions with AND/OR logic |
| `timespan` | CoreBundle | Uses custom date/time picker composition |

### Built-in Actions (Schema-driven)

| Key | Source Bundle | Description |
|-----|-------------|-------------|
| `surchargePercent` | OrderBundle | Add percentage surcharge |
| `surchargeAmount` | OrderBundle | Add fixed surcharge |
| `discountAmount` | CoreBundle | Fixed amount discount |
| `discountPercent` | CoreBundle | Percentage discount |
| `giftProduct` | CoreBundle | Free gift product |
| `freeShipping` | CoreBundle | Free shipping |

### Actions (Hand-written — require custom JS)

| Key | Source Bundle | Reason |
|-----|-------------|--------|
| `cartItemAction` | OrderBundle | Renders nested cart-item condition/action panels |

### Adding Custom Conditions/Actions

New cart price rule conditions/actions only require a PHP FormType and service tag — no React code needed. See [StudioFormBundle Examples — Example 13](../02_Base_Infrastructure/05_StudioFormBundle_Examples.md#example-13--rule-conditionaction-as-schema-form).

## File Structure

```
OrderBundle/Resources/assets/pimcore-studio/src/
├── main.ts                              # Plugin entry point
├── modules/
│   ├── icon-library/
│   │   └── index.ts
│   ├── cart-price-rules/
│   │   ├── service-ids.ts               # Registry service IDs
│   │   ├── types.ts
│   │   ├── api.ts
│   │   ├── CartPriceRuleManager.tsx
│   │   ├── CartPriceRuleFormBuilder.ts
│   │   ├── form-builder-module.ts
│   │   ├── components/
│   │   │   ├── SettingsForm.tsx
│   │   │   └── VoucherCodesPanel.tsx
│   │   ├── conditions/
│   │   │   └── index.ts                 # All conditions are schema-generated
│   │   ├── actions/
│   │   │   ├── CartItemAction.tsx       # Hand-written (nested panels)
│   │   │   └── index.ts
│   │   └── cart-item/                   # Nested cart item rules
│   │       ├── CartItemConditionItem.tsx
│   │       ├── CartItemActionItem.tsx
│   │       ├── CartItemConditionsPanel.tsx
│   │       └── CartItemActionsPanel.tsx
│   ├── sales/
│   │   ├── index.ts
│   │   ├── service-ids.ts
│   │   ├── types.ts
│   │   ├── OrderList.tsx
│   │   ├── CartList.tsx
│   │   ├── QuoteList.tsx
│   │   ├── OrderDetailWidget.tsx
│   │   ├── CartDetailWidget.tsx
│   │   ├── QuoteDetailWidget.tsx
│   │   ├── SaleDetail.tsx               # Main detail component
│   │   ├── SaleEditorTabs.tsx
│   │   ├── SaleWidgetRestorer.ts
│   │   ├── registry/
│   │   │   ├── SaleTabRegistry.ts
│   │   │   └── ComponentRegistry.ts
│   │   ├── extensions/
│   │   │   ├── ModalFieldExtensionRegistry.ts
│   │   │   ├── service-ids.ts
│   │   │   └── index.ts
│   │   ├── context/
│   │   │   ├── SaleActionsContext.tsx
│   │   │   └── ButtonRegistry.tsx
│   │   ├── hooks/
│   │   │   ├── useSaleHelper.ts
│   │   │   └── useDetailEditMode.ts
│   │   ├── tabs/
│   │   │   ├── HeaderTab.tsx
│   │   │   ├── InfoTab.tsx
│   │   │   ├── PaymentTab.tsx
│   │   │   ├── ShipmentTab.tsx
│   │   │   ├── InvoiceTab.tsx
│   │   │   ├── CustomerTab.tsx
│   │   │   ├── CommentsTab.tsx
│   │   │   ├── CorrespondenceTab.tsx
│   │   │   └── DetailTab.tsx
│   │   ├── components/
│   │   │   ├── SaleToolbar.tsx
│   │   │   ├── CreatePaymentButton.tsx
│   │   │   ├── CreatePaymentModal.tsx
│   │   │   ├── CreateShipmentButton.tsx
│   │   │   ├── CreateShipmentModal.tsx
│   │   │   ├── CreateInvoiceButton.tsx
│   │   │   ├── CreateInvoiceModal.tsx
│   │   │   ├── StateChangeModal.tsx
│   │   │   ├── InvoiceDetailModal.tsx
│   │   │   └── ShipmentDetailModal.tsx
│   │   ├── events/
│   │   │   ├── DetailEvents.ts
│   │   │   ├── PaymentEvents.ts
│   │   │   ├── ShipmentEvents.ts
│   │   │   └── InvoiceEvents.ts
│   │   └── listing-builders/
│   │       └── index.ts
│   └── order-creation/
│       ├── index.ts
│       ├── service-ids.ts
│       ├── types.ts
│       ├── api.ts
│       ├── registry/
│       │   └── OrderCreationStepRegistry.ts
│       ├── context/
│       │   └── OrderCreationContext.tsx
│       ├── components/
│       │   ├── OrderCreationPanel.tsx
│       │   ├── CustomerSelector.tsx
│       │   └── index.ts
│       └── steps/
│           ├── BaseStep.tsx
│           ├── ProductsStep.tsx
│           ├── TotalsStep.tsx
│           └── index.ts
└── dynamic-types/
    └── DynamicTypeObjectDataCoreShopCartPriceRule.tsx
```

## Best Practices

1. **Use SaleContext**: Always use `useSaleContext()` hook in tabs instead of props
2. **Type filtering**: Register tabs for specific sale types only when needed
3. **Priority ordering**: Use appropriate priority values to control tab order
4. **Cleanup on unmount**: Remove toolbar buttons when tab unmounts
5. **Handle loading states**: Show loading indicators during API calls
6. **Validate step data**: Implement `validate` function for order creation steps
7. **Use translations**: Use `useTranslation()` for all labels
8. **Follow naming conventions**: Use consistent key naming (kebab-case for modal keys)

## Testing

1. **Test tab visibility**: Verify tabs appear for correct sale types
2. **Test position rendering**: Verify tabs appear in correct layout position
3. **Test modal extensions**: Verify additional fields appear in modals
4. **Test order creation**: Walk through entire wizard with custom steps
5. **Test data persistence**: Verify custom data saves and loads correctly

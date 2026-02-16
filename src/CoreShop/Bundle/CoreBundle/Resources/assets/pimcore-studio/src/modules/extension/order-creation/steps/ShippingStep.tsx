/**
 * CoreShop CoreBundle - Shipping Step Component
 *
 * Schema-driven carrier selection step.
 * Uses coreshop_carrier_choice widget to read from OrderCreation preview data.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Card, Typography, Spin } from 'antd'
import { useTranslation } from 'react-i18next'
import { useFormSchema, DynamicForm, sectionFilterDecorator } from '@coreshop/studio-form'
import type {
  OrderCreationStepConfig,
  OrderCreationState,
  OrderCreationStepProps,
  CarrierInfo,
} from '@coreshop/order/src/modules/order-creation/types'

const formatCurrency = (value: number | undefined | null, isoCode?: string): string => {
  if (value === undefined || value === null) return '-'
  const amount = value / 100
  if (isoCode) {
    try {
      return new Intl.NumberFormat(undefined, {
        style: 'currency',
        currency: isoCode,
      }).format(amount)
    } catch {
      // Fallback
    }
  }
  return amount.toFixed(2)
}

const ShippingStepComponent: React.FC<OrderCreationStepProps> = ({ state, dispatch, triggerPreview }) => {
  const { t } = useTranslation()

  const { builder, loading } = useFormSchema('coreshop_cart_creation', [
    { name: 'section-filter', decorator: sectionFilterDecorator('shipping') },
  ])

  const carriers = state.preview?.carriers ?? []
  const currencyCode = state.preview?.baseCurrency?.isoCode
  const selectedCarrier = carriers.find((c: CarrierInfo) => c.id === state.formData.carrier)

  if (loading || !builder) {
    return (
      <Card title={t('coreshop_order_creation_shipping', { defaultValue: 'Shipping' })} size="small">
        <div style={{ display: 'flex', justifyContent: 'center', padding: 24 }}>
          <Spin />
        </div>
      </Card>
    )
  }

  const config = builder.build()

  // Inject carriers into widget componentProps (widgets can't use React Context
  // across module federation boundaries, so we pass data via props instead)
  config.fields = config.fields.map(f => ({
    ...f,
    componentProps: { ...f.componentProps, carriers, currencyCode },
  }))

  return (
    <Card
      title={t('coreshop_order_creation_shipping', { defaultValue: 'Shipping' })}
      size="small"
    >
      <DynamicForm
        config={config}
        data={state.formData}
        onChange={(changedValues) => {
          dispatch({ type: 'UPDATE_FORM_DATA', payload: changedValues })
          triggerPreview()
        }}
      />

      {carriers.length === 0 && (
        <Typography.Text type="secondary">
          {t('coreshop_no_carriers_available', {
            defaultValue: 'No carriers available. Please select shipping and invoice addresses first.',
          })}
        </Typography.Text>
      )}

      {selectedCarrier && (
        <Typography.Text type="secondary">
          {t('coreshop_shipping_price', { defaultValue: 'Shipping Price' })}:{' '}
          <Typography.Text strong>
            {formatCurrency(selectedCarrier.price, currencyCode)}
          </Typography.Text>
        </Typography.Text>
      )}
    </Card>
  )
}

export const ShippingStepConfig: OrderCreationStepConfig = {
  key: 'shipping',
  label: 'coreshop_order_creation_shipping',
  icon: 'coreshop_icon_shipping',
  priority: 50,
  component: ShippingStepComponent,

  isValid: () => true,

  getValues: (state: OrderCreationState) => ({
    carrier: state.formData.carrier,
  }),

  isVisible: (state: OrderCreationState) => {
    return Boolean(
      state.formData.items.length > 0 &&
        state.formData.shippingAddress &&
        state.formData.invoiceAddress
    )
  },
}

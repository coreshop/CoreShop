/**
 * CoreShop CoreBundle - Shipping Step Component
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
import { Card, Form, Select, Typography, Space } from 'antd'
import { useTranslation } from 'react-i18next'
import type {
  OrderCreationStepConfig,
  OrderCreationState,
  OrderCreationStepProps,
  CarrierInfo
} from '@coreshop/order/src/modules/order-creation/types'

/**
 * Format currency value (cents to display)
 */
const formatCurrency = (value: number | undefined | null, isoCode?: string): string => {
  if (value === undefined || value === null) {
    return '-'
  }
  const amount = value / 100
  if (isoCode) {
    try {
      return new Intl.NumberFormat(undefined, {
        style: 'currency',
        currency: isoCode
      }).format(amount)
    } catch {
      // Fallback if currency code is invalid
    }
  }
  return amount.toFixed(2)
}

const ShippingStepComponent: React.FC<OrderCreationStepProps> = ({ state, dispatch, triggerPreview }) => {
  const { t } = useTranslation()

  // Get carriers from preview (populated by CoreBundle backend)
  const carriers = state.preview?.carriers || []
  const currencyCode = state.preview?.baseCurrency?.isoCode

  const carrierOptions = carriers.map((carrier: CarrierInfo) => ({
    value: carrier.id,
    label: `${carrier.name} - ${formatCurrency(carrier.price, currencyCode)}`
  }))

  const handleChange = (value: number | null): void => {
    dispatch({ type: 'UPDATE_FORM_DATA', payload: { carrier: value } })
    triggerPreview()
  }

  // Get selected carrier details
  const selectedCarrier = carriers.find((c: CarrierInfo) => c.id === state.formData.carrier)

  return (
    <Card
      title={t('coreshop_order_creation_shipping', { defaultValue: 'Shipping' })}
      size="small"
    >
      <Form.Item label={t('coreshop_carrier', { defaultValue: 'Carrier' })}>
        <Select
          value={state.formData.carrier ?? undefined}
          onChange={handleChange}
          options={carrierOptions}
          placeholder={t('coreshop_select_carrier', { defaultValue: 'Select Carrier' })}
          allowClear
          style={{ width: '100%' }}
          disabled={carriers.length === 0}
        />
      </Form.Item>

      {carriers.length === 0 && (
        <Typography.Text type="secondary">
          {t('coreshop_no_carriers_available', {
            defaultValue:
              'No carriers available. Please select shipping and invoice addresses first.'
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

  // Carrier is optional
  isValid: () => true,

  getValues: (state: OrderCreationState) => ({
    carrier: state.formData.carrier
  }),

  // Only show if addresses are selected and items exist
  isVisible: (state: OrderCreationState) => {
    return Boolean(
      state.formData.items.length > 0 &&
        state.formData.shippingAddress &&
        state.formData.invoiceAddress
    )
  }
}

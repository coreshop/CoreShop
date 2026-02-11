/**
 * CoreShop CoreBundle - Payment Step Component
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
import { Card, Form } from 'antd'
import { useTranslation } from 'react-i18next'
import { PaymentProviderSelect } from '@coreshop/payment/src/components'
import type {
  OrderCreationStepConfig,
  OrderCreationState,
  OrderCreationStepProps
} from '@coreshop/order/src/modules/order-creation/types'

const PaymentStepComponent: React.FC<OrderCreationStepProps> = ({ state, dispatch, triggerPreview }) => {
  const { t } = useTranslation()

  const handleChange = (value: number | null): void => {
    dispatch({ type: 'UPDATE_FORM_DATA', payload: { paymentProvider: value } })
    triggerPreview()
  }

  return (
    <Card
      title={t('coreshop_order_creation_payment', { defaultValue: 'Payment' })}
      size="small"
    >
      <Form.Item
        label={t('coreshop_payment_provider', { defaultValue: 'Payment Provider' })}
        required
      >
        <PaymentProviderSelect
          value={(state.formData as Record<string, unknown>).paymentProvider as number ?? undefined}
          onChange={(value) => handleChange(value as number)}
          placeholder={t('coreshop_select_payment_provider', { defaultValue: 'Select Payment Provider' })}
          allowClear
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Card>
  )
}

export const PaymentStepConfig: OrderCreationStepConfig = {
  key: 'payment',
  label: 'coreshop_order_creation_payment',
  icon: 'coreshop_icon_payment_provider',
  priority: 60,
  component: PaymentStepComponent,

  isValid: (state: OrderCreationState) => {
    return Boolean((state.formData as Record<string, unknown>).paymentProvider)
  },

  getValues: (state: OrderCreationState) => ({
    paymentProvider: (state.formData as Record<string, unknown>).paymentProvider
  }),

  // Only show if items exist
  isVisible: (state: OrderCreationState) => state.formData.items.length > 0
}

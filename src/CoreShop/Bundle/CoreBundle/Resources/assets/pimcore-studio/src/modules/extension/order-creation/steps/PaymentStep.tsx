/**
 * CoreShop CoreBundle - Payment Step Component
 *
 * Schema-driven payment provider selection step.
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
import { Card, Spin } from 'antd'
import { useTranslation } from 'react-i18next'
import { useFormSchema, DynamicForm, sectionFilterDecorator } from '@coreshop/studio-form'
import type {
  OrderCreationStepConfig,
  OrderCreationState,
  OrderCreationStepProps
} from '@coreshop/order/src/modules/order-creation/types'

const PaymentStepComponent: React.FC<OrderCreationStepProps> = ({ state, dispatch, triggerPreview }) => {
  const { t } = useTranslation()

  const { builder, loading } = useFormSchema('coreshop_cart_creation', [
    { name: 'section-filter', decorator: sectionFilterDecorator('payment') },
  ])

  if (loading || !builder) {
    return (
      <Card title={t('coreshop_order_creation_payment', { defaultValue: 'Payment' })} size="small">
        <div style={{ display: 'flex', justifyContent: 'center', padding: 24 }}>
          <Spin />
        </div>
      </Card>
    )
  }

  const config = builder.build()

  return (
    <Card
      title={t('coreshop_order_creation_payment', { defaultValue: 'Payment' })}
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

  isVisible: (state: OrderCreationState) => state.formData.items.length > 0
}

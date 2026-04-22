/**
 * CoreShop OrderBundle - Base Step Component
 *
 * Schema-driven base settings step (store, currency, locale).
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
import type { FormDecorator } from '@coreshop/studio-form'
import type { OrderCreationStepConfig, OrderCreationState, OrderCreationStepProps } from '../types'

const columnsDecorator: FormDecorator = (config) => ({
  ...config,
  columns: 3,
  fields: config.fields.map((f) => ({ ...f, span: 8 })),
})

const hideSectionTitleDecorator: FormDecorator = (config) => ({
  ...config,
  sections: config.sections?.map((s) => ({ ...s, title: '', description: undefined })),
})

const BaseStepComponent: React.FC<OrderCreationStepProps> = ({ state, dispatch, triggerPreview }) => {
  const { t } = useTranslation()

  const { builder, loading } = useFormSchema('coreshop_cart_creation', [
    { name: 'section-filter', decorator: sectionFilterDecorator('base') },
    { name: 'hide-section-title', decorator: hideSectionTitleDecorator },
    { name: 'columns', decorator: columnsDecorator },
  ])

  if (loading || !builder) {
    return (
      <Card title={t('coreshop_order_creation_base', { defaultValue: 'Base Settings' })}>
        <div style={{ display: 'flex', justifyContent: 'center', padding: 24 }}>
          <Spin />
        </div>
      </Card>
    )
  }

  const config = builder.build()

  return (
    <Card
      title={t('coreshop_order_creation_base', { defaultValue: 'Base Settings' })}
    >
      <DynamicForm
        config={config}
        data={state.formData}
        onChange={(changedValues) => {
          dispatch({ type: 'UPDATE_FORM_DATA', payload: changedValues })
          setTimeout(triggerPreview, 0)
        }}
      />
    </Card>
  )
}

// Step configuration
export const BaseStepConfig: OrderCreationStepConfig = {
  key: 'base',
  label: 'coreshop_order_creation_base',
  icon: 'coreshop_icon_localization',
  priority: 20,
  component: BaseStepComponent,

  isValid: (state: OrderCreationState) => {
    return Boolean(
      state.formData.store && state.formData.currency && state.formData.localeCode
    )
  },

  getValues: (state: OrderCreationState) => ({
    store: state.formData.store,
    currency: state.formData.currency,
    localeCode: state.formData.localeCode
  })
}

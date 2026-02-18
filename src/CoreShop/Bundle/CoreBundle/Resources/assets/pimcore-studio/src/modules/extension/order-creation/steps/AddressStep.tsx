/**
 * CoreShop CoreBundle - Address Step Component
 *
 * Schema-driven address selection step.
 * Uses coreshop_customer_address_choice widget to read from OrderCreation context.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useState, useCallback } from 'react'
import { Card, Button, Spin, Space, Typography } from 'antd'
import { PlusOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'
import { useFormSchema, DynamicForm, sectionFilterDecorator } from '@coreshop/studio-form'
import type { FormDecorator } from '@coreshop/studio-form'
import { orderCreationApi } from '@coreshop/order/src/modules/order-creation/api'
import type {
  OrderCreationStepConfig,
  OrderCreationState,
  OrderCreationStepProps,
} from '@coreshop/order/src/modules/order-creation/types'
import { AddressCreationModal } from './AddressCreationModal'

const hideSectionTitleDecorator: FormDecorator = (config) => ({
  ...config,
  sections: config.sections?.map((s) => ({ ...s, title: '', description: undefined })),
})

const twoColumnDecorator: FormDecorator = (config) => ({
  ...config,
  columns: 2,
  fields: config.fields.map((f) => ({ ...f, span: 12 })),
})

const AddressStepComponent: React.FC<OrderCreationStepProps> = ({ state, dispatch, triggerPreview }) => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const [createModalOpen, setCreateModalOpen] = useState(false)

  const { builder, loading } = useFormSchema('coreshop_cart_creation', [
    { name: 'section-filter', decorator: sectionFilterDecorator('address') },
    { name: 'hide-section-title', decorator: hideSectionTitleDecorator },
    { name: 'two-columns', decorator: twoColumnDecorator },
  ])

  const handleAddressCreated = useCallback(async (addressId: number) => {
    setCreateModalOpen(false)

    if (state.customerId) {
      try {
        const details = await orderCreationApi.getCustomerDetails(state.customerId)
        dispatch({ type: 'SET_CUSTOMER', payload: { id: state.customerId, details } })
      } catch (err) {
        void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to reload customer')))
      }
    }

    void messageApi.success(
      t('coreshop_address_created_success', { defaultValue: 'Address created successfully' })
    )
  }, [state.customerId, dispatch, messageApi, t])

  const handleCopyShippingToInvoice = (): void => {
    if (state.formData.shippingAddress) {
      dispatch({
        type: 'UPDATE_FORM_DATA',
        payload: { invoiceAddress: state.formData.shippingAddress },
      })
      triggerPreview()
    }
  }

  if (loading || !builder) {
    return (
      <Card title={t('coreshop_order_creation_address', { defaultValue: 'Addresses' })}>
        <div style={{ display: 'flex', justifyContent: 'center', padding: 24 }}>
          <Spin />
        </div>
      </Card>
    )
  }

  const addresses = state.customerDetails?.addresses ?? []
  const config = builder.build()

  // Inject addresses into widget componentProps (widgets can't use React Context
  // across module federation boundaries, so we pass data via props instead)
  config.fields = config.fields.map(f => ({
    ...f,
    componentProps: { ...f.componentProps, addresses },
  }))

  return (
    <Card
      title={t('coreshop_order_creation_address', { defaultValue: 'Addresses' })}
      extra={
        <Space>
          {state.formData.shippingAddress && (
            <Button
              type="link"
              size="small"
              onClick={handleCopyShippingToInvoice}
            >
              {t('coreshop_copy_from_shipping', { defaultValue: 'Copy shipping to invoice' })}
            </Button>
          )}
          {state.customerId && (
            <Button
              type="primary"
              icon={<PlusOutlined />}
              size="small"
              onClick={() => setCreateModalOpen(true)}
            >
              {t('coreshop_address_create', { defaultValue: 'Create Address' })}
            </Button>
          )}
        </Space>
      }
    >
      <DynamicForm
        config={config}
        data={state.formData}
        onChange={(changedValues) => {
          dispatch({ type: 'UPDATE_FORM_DATA', payload: changedValues })
          triggerPreview()
        }}
      />

      {addresses.length === 0 && (
        <Typography.Text type="secondary">
          {t('coreshop_no_addresses_available', {
            defaultValue: 'No addresses available for this customer.',
          })}
        </Typography.Text>
      )}

      {state.customerId && (
        <AddressCreationModal
          open={createModalOpen}
          customerId={state.customerId}
          onClose={() => setCreateModalOpen(false)}
          onCreated={(addressId) => void handleAddressCreated(addressId)}
        />
      )}
    </Card>
  )
}

export const AddressStepConfig: OrderCreationStepConfig = {
  key: 'address',
  label: 'coreshop_order_creation_address',
  icon: 'coreshop_icon_address',
  priority: 40,
  component: AddressStepComponent,

  isValid: () => true,

  getValues: (state: OrderCreationState) => ({
    shippingAddress: state.formData.shippingAddress,
    invoiceAddress: state.formData.invoiceAddress,
  }),

  isVisible: (state: OrderCreationState) => state.formData.items.length > 0,
}

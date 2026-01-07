/**
 * CoreShop CoreBundle - Address Step Component
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
import { Card, Form, Row, Col, Select, Typography, Space, Button } from 'antd'
import { useTranslation } from 'react-i18next'
import type {
  OrderCreationStepConfig,
  OrderCreationState,
  OrderCreationStepProps,
  AddressInfo
} from '@coreshop/order/src/modules/order-creation/types'

/**
 * Format address for display in select option
 */
const formatAddress = (addr: AddressInfo): string => {
  const parts: string[] = []

  if (addr.firstname || addr.lastname) {
    parts.push([addr.firstname, addr.lastname].filter(Boolean).join(' '))
  }
  if (addr.company) {
    parts.push(addr.company)
  }
  if (addr.street) {
    parts.push([addr.street, addr.number].filter(Boolean).join(' '))
  }
  if (addr.postcode || addr.city) {
    parts.push([addr.postcode, addr.city].filter(Boolean).join(' '))
  }
  if (addr.countryName) {
    parts.push(addr.countryName)
  }

  return parts.join(', ') || `Address #${addr.id}`
}

const AddressStepComponent: React.FC<OrderCreationStepProps> = ({ state, dispatch, triggerPreview }) => {
  const { t } = useTranslation()

  // Get addresses from customer details
  const addresses = state.customerDetails?.addresses || []

  const addressOptions = addresses.map((addr) => ({
    value: addr.id,
    label: formatAddress(addr)
  }))

  const handleChange = (field: string, value: number | null): void => {
    dispatch({ type: 'UPDATE_FORM_DATA', payload: { [field]: value } })
    triggerPreview()
  }

  const handleCopyShippingToInvoice = (): void => {
    if (state.formData.shippingAddress) {
      dispatch({
        type: 'UPDATE_FORM_DATA',
        payload: { invoiceAddress: state.formData.shippingAddress }
      })
      triggerPreview()
    }
  }

  return (
    <Card
      title={t('coreshop_order_creation_address', { defaultValue: 'Addresses' })}
      size="small"
    >
      <Row gutter={24}>
        <Col span={12}>
          <Form.Item
            label={t('coreshop_address_shipping', { defaultValue: 'Shipping Address' })}
          >
            <Select
              value={state.formData.shippingAddress ?? undefined}
              onChange={(value) => handleChange('shippingAddress', value)}
              options={addressOptions}
              placeholder={t('coreshop_select_address', { defaultValue: 'Select Address' })}
              allowClear
              style={{ width: '100%' }}
              showSearch
              filterOption={(input, option) =>
                (option?.label ?? '').toLowerCase().includes(input.toLowerCase())
              }
            />
          </Form.Item>
          {state.preview?.address_shipping_formatted && (
            <Typography.Paragraph
              type="secondary"
              style={{ fontSize: 12, marginTop: -8 }}
            >
              <div
                dangerouslySetInnerHTML={{
                  __html: state.preview.address_shipping_formatted
                }}
              />
            </Typography.Paragraph>
          )}
        </Col>
        <Col span={12}>
          <Form.Item
            label={
              <Space>
                {t('coreshop_address_invoice', { defaultValue: 'Invoice Address' })}
                {state.formData.shippingAddress && (
                  <Button
                    type="link"
                    size="small"
                    onClick={handleCopyShippingToInvoice}
                    style={{ padding: 0 }}
                  >
                    {t('coreshop_copy_from_shipping', { defaultValue: 'Copy from shipping' })}
                  </Button>
                )}
              </Space>
            }
          >
            <Select
              value={state.formData.invoiceAddress ?? undefined}
              onChange={(value) => handleChange('invoiceAddress', value)}
              options={addressOptions}
              placeholder={t('coreshop_select_address', { defaultValue: 'Select Address' })}
              allowClear
              style={{ width: '100%' }}
              showSearch
              filterOption={(input, option) =>
                (option?.label ?? '').toLowerCase().includes(input.toLowerCase())
              }
            />
          </Form.Item>
          {state.preview?.address_billing_formatted && (
            <Typography.Paragraph
              type="secondary"
              style={{ fontSize: 12, marginTop: -8 }}
            >
              <div
                dangerouslySetInnerHTML={{
                  __html: state.preview.address_billing_formatted
                }}
              />
            </Typography.Paragraph>
          )}
        </Col>
      </Row>

      {addresses.length === 0 && (
        <Typography.Text type="secondary">
          {t('coreshop_no_addresses_available', {
            defaultValue: 'No addresses available for this customer.'
          })}
        </Typography.Text>
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

  // Address step is always valid (addresses are optional for preview)
  isValid: () => true,

  getValues: (state: OrderCreationState) => ({
    shippingAddress: state.formData.shippingAddress,
    invoiceAddress: state.formData.invoiceAddress
  }),

  // Only show if products exist
  isVisible: (state: OrderCreationState) => state.formData.items.length > 0
}

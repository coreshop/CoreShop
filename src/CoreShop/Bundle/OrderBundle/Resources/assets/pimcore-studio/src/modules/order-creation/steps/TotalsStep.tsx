/**
 * CoreShop OrderBundle - Totals Step Component
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
import { Card, Table, Typography, Space } from 'antd'
import { useTranslation } from 'react-i18next'
import type { OrderCreationStepConfig, OrderCreationState, OrderCreationStepProps, SummaryItem } from '../types'

/**
 * Format currency value (cents to display)
 */
const formatCurrency = (value: number, isoCode?: string): string => {
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

/**
 * Get translation for summary item key
 */
const summaryLabelKeys: Record<string, { key: string; defaultValue: string }> = {
  subtotal: { key: 'coreshop_subtotal', defaultValue: 'Subtotal' },
  subtotal_without_tax: { key: 'coreshop_subtotal_without_tax', defaultValue: 'Subtotal (excl. Tax)' },
  subtotal_tax: { key: 'coreshop_subtotal_tax', defaultValue: 'Subtotal Tax' },
  discount: { key: 'coreshop_discount', defaultValue: 'Discount' },
  discount_without_tax: { key: 'coreshop_discount_without_tax', defaultValue: 'Discount (excl. Tax)' },
  discount_tax: { key: 'coreshop_discount_tax', defaultValue: 'Discount Tax' },
  shipping: { key: 'coreshop_shipping', defaultValue: 'Shipping' },
  shipping_without_tax: { key: 'coreshop_shipping_without_tax', defaultValue: 'Shipping (excl. Tax)' },
  shipping_tax: { key: 'coreshop_shipping_tax', defaultValue: 'Shipping Tax' },
  payment_provider_fee: { key: 'coreshop_payment_provider_fee', defaultValue: 'Payment Provider Fee' },
  total: { key: 'coreshop_total', defaultValue: 'Total' },
  total_without_tax: { key: 'coreshop_total_without_tax', defaultValue: 'Total (excl. Tax)' },
  total_tax: { key: 'coreshop_total_tax', defaultValue: 'Total Tax' }
}

const TotalsStepComponent: React.FC<OrderCreationStepProps> = ({ state }) => {
  const { t } = useTranslation()

  const summary = state.preview?.summary || []
  const currencyCode = state.preview?.baseCurrency?.isoCode
  const convertedCurrencyCode = state.preview?.currency?.isoCode

  // Filter out zero values and tax-related items for cleaner display
  const displayItems = summary.filter((item) => {
    // Always show total
    if (item.key === 'total') return true
    // Show subtotal
    if (item.key === 'subtotal') return true
    // Show other items only if they have a value
    return item.value !== 0
  })

  const columns = [
    {
      title: t('coreshop_description', { defaultValue: 'Description' }),
      dataIndex: 'key',
      render: (key: string) => {
        const isTotal = key === 'total'
        const labelConfig = summaryLabelKeys[key]
        const label = labelConfig
          ? t(labelConfig.key, { defaultValue: labelConfig.defaultValue })
          : key
        return (
          <Typography.Text strong={isTotal}>{label}</Typography.Text>
        )
      }
    },
    {
      title: t('coreshop_value', { defaultValue: 'Value' }),
      dataIndex: 'value',
      width: 150,
      align: 'right' as const,
      render: (value: number, record: SummaryItem) => {
        const isTotal = record.key === 'total'
        return (
          <Typography.Text strong={isTotal}>
            {formatCurrency(value, currencyCode)}
          </Typography.Text>
        )
      }
    }
  ]

  // Add converted value column if currencies differ
  if (currencyCode !== convertedCurrencyCode) {
    columns.push({
      title: t('coreshop_converted_value', { defaultValue: 'Converted' }),
      dataIndex: 'convertedValue',
      width: 150,
      align: 'right' as const,
      render: (value: number, record: SummaryItem) => {
        const isTotal = record.key === 'total'
        return (
          <Typography.Text strong={isTotal} type="secondary">
            {formatCurrency(value, convertedCurrencyCode)}
          </Typography.Text>
        )
      }
    })
  }

  if (!state.preview || displayItems.length === 0) {
    return (
      <Card
        title={t('coreshop_order_creation_totals', { defaultValue: 'Order Summary' })}
        size="small"
      >
        <Space direction="vertical" style={{ width: '100%', textAlign: 'center', padding: 24 }}>
          <Typography.Text type="secondary">
            {t('coreshop_no_preview_available', {
              defaultValue: 'Fill in the required fields to see the order summary.'
            })}
          </Typography.Text>
        </Space>
      </Card>
    )
  }

  return (
    <Card
      title={t('coreshop_order_creation_totals', { defaultValue: 'Order Summary' })}
      size="small"
    >
      <Table
        dataSource={displayItems}
        columns={columns}
        rowKey="key"
        pagination={false}
        size="small"
        showHeader={false}
        rowClassName={(record) => (record.key === 'total' ? 'totals-row-highlight' : '')}
      />
    </Card>
  )
}

export const TotalsStepConfig: OrderCreationStepConfig = {
  key: 'totals',
  label: 'coreshop_order_creation_totals',
  icon: 'coreshop_icon_order',
  priority: 100,
  component: TotalsStepComponent,

  // Totals step is always valid (it's just display)
  isValid: () => true,

  // No values to contribute to the request
  getValues: () => ({})
}

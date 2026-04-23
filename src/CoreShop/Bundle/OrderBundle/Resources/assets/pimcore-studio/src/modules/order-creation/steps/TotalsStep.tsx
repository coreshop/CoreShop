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
import { Card, Typography, Space, theme } from 'antd'
import { useTranslation } from 'react-i18next'
import { createStyles } from 'antd-style'
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

const useTotalsStyles = createStyles(({ css, token }) => ({
  summaryWrapper: css`
    display: flex;
    flex-direction: column;
    gap: 0;
  `,
  summaryRow: css`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid ${token.colorBorderSecondary};
  `,
  summaryRowTotal: css`
    border-bottom: none;
    border-top: 2px solid ${token.colorBorder};
    padding-top: 10px;
    margin-top: 4px;
  `,
  summaryLabel: css`
    font-size: 13px;
    color: ${token.colorTextSecondary};
  `,
  summaryValues: css`
    display: flex;
    gap: 16px;
  `,
  summaryValue: css`
    text-align: right;
    font-size: 13px;
    font-weight: 500;
    color: ${token.colorText};
    min-width: 90px;
    font-variant-numeric: tabular-nums;
  `
}))

const TotalsStepComponent: React.FC<OrderCreationStepProps> = ({ state }) => {
  const { t } = useTranslation()
  const { styles } = useTotalsStyles()
  const { token } = theme.useToken()

  const summary = state.preview?.summary || []
  const currencyCode = state.preview?.baseCurrency?.isoCode
  const convertedCurrencyCode = state.preview?.currency?.isoCode
  const showConverted = currencyCode !== convertedCurrencyCode

  // Filter out zero values and tax-related items for cleaner display
  const displayItems = summary.filter((item) => {
    if (item.key === 'total') return true
    if (item.key === 'subtotal') return true
    return item.value !== 0
  })

  const isTotalRow = (key: string) => key === 'total'

  // Sort: regular items first, then total last
  const sortedItems = React.useMemo(() => {
    const regular = displayItems.filter(item => !isTotalRow(item.key))
    const totals = displayItems.filter(item => isTotalRow(item.key))
    return [...regular, ...totals]
  }, [displayItems])

  if (!state.preview || displayItems.length === 0) {
    return (
      <Card
        title={t('coreshop_order_creation_totals', { defaultValue: 'Order Summary' })}
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
    >
      <div className={styles.summaryWrapper}>
        {sortedItems.map((item) => {
          const isTotal = isTotalRow(item.key)
          const labelConfig = summaryLabelKeys[item.key]
          const label = labelConfig
            ? t(labelConfig.key, { defaultValue: labelConfig.defaultValue })
            : item.key
          return (
            <div key={item.key} className={`${styles.summaryRow} ${isTotal ? styles.summaryRowTotal : ''}`}>
              <span className={styles.summaryLabel} style={isTotal ? { fontWeight: 600, color: token.colorText } : undefined}>
                {label}
              </span>
              <div className={styles.summaryValues}>
                <span className={styles.summaryValue} style={isTotal ? { fontWeight: 700, fontSize: 15 } : undefined}>
                  {formatCurrency(item.value, currencyCode)}
                </span>
                {showConverted && (
                  <span className={styles.summaryValue} style={isTotal ? { fontWeight: 700, fontSize: 15 } : undefined}>
                    {formatCurrency(item.convertedValue ?? item.value, convertedCurrencyCode)}
                  </span>
                )}
              </div>
            </div>
          )
        })}
      </div>
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

/**
 * CoreShop CoreBundle - Preview Carrier Select Widget
 *
 * Schema-driven widget for carrier selection in order creation.
 * Reads carrier options from OrderCreation preview data instead of schema choices
 * (since carriers depend on the current cart context).
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
import { Select } from 'antd'
import type { CarrierInfo } from '@coreshop/order/src/modules/order-creation/types'

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

interface PreviewCarrierSelectWidgetProps {
  value?: number | null
  onChange?: (value: number | null) => void
  disabled?: boolean
  carriers?: CarrierInfo[]
  currencyCode?: string
}

export const PreviewCarrierSelectWidget: React.FC<PreviewCarrierSelectWidgetProps> = ({
  value,
  onChange,
  disabled,
  carriers = [],
  currencyCode,
}) => {
  const options = carriers.map((carrier: CarrierInfo) => ({
    value: carrier.id,
    label: `${carrier.name} - ${formatCurrency(carrier.price, currencyCode)}`,
  }))

  return (
    <Select
      value={value ?? undefined}
      onChange={(v) => onChange?.(v ?? null)}
      options={options}
      allowClear
      disabled={disabled || carriers.length === 0}
    />
  )
}

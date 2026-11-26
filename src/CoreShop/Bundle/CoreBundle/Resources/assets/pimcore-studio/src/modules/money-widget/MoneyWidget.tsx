/**
 * CoreShop CoreBundle - Money Widget
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
import { InputNumber } from 'antd'
import { useCurrencyConfig } from '@coreshop/currency/src/modules/currency-config'

interface MoneyWidgetProps {
  value?: number | string | null
  onChange?: (value: number | null) => void
  disabled?: boolean
}

/**
 * Input for MoneyType fields ("coreshop_money") in schema forms.
 *
 * The form value stays the stored integer (minor units, e.g. 772); the input shows and edits the decimal value
 * (7.72). Rule configurations are loaded and saved with their stored integers, and
 * StudioRuleMoneyTypeExtension reads integers submitted from Studio as minor units, so untouched values are
 * saved unchanged.
 */
export const MoneyWidget: React.FC<MoneyWidgetProps> = ({ value, onChange, disabled }) => {
  const { toDisplayPrice, toIntegerPrice, decimalPrecision } = useCurrencyConfig()

  const numericValue = value === null || value === undefined || value === '' ? null : Number(value)
  const displayValue = numericValue === null || Number.isNaN(numericValue) ? null : toDisplayPrice(numericValue)

  return (
    <InputNumber
      disabled={disabled}
      onChange={(next) => {
        onChange?.(next === null || next === undefined ? null : toIntegerPrice(Number(next)))
      }}
      precision={decimalPrecision}
      step={1 / 10 ** decimalPrecision}
      style={{ width: '100%' }}
      value={displayValue}
    />
  )
}

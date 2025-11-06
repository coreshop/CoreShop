/**
 * CoreShop ProductBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useState, useEffect } from 'react'
import { Form, InputNumber, Select } from 'antd'
import type { ActionComponentProps } from '@coreshop/rule/src/rules'
import { currencyApi } from '@coreshop/currency/src/modules/currencies/api'

export const DiscountAmountAction: React.FC<ActionComponentProps> = ({
  data,
  onChange
}) => {
  const amount = data.amount || 0
  const currency = data.currency || null
  const [currencies, setCurrencies] = useState<Array<{ id: number, name: string }>>([])

  useEffect(() => {
    currencyApi.list()
      .then((response) => {
        setCurrencies(Array.isArray(response) ? response : [])
      })
      .catch(() => {
        setCurrencies([])
      })
  }, [])

  const handleAmountChange = (value: number | null) => {
    onChange({ ...data, amount: value || 0 })
  }

  const handleCurrencyChange = (value: number) => {
    onChange({ ...data, currency: value })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Discount Amount">
        <InputNumber
          value={amount}
          onChange={handleAmountChange}
          min={0}
          precision={2}
          style={{ width: '100%' }}
        />
      </Form.Item>
      <Form.Item label="Currency">
        <Select
          value={currency}
          onChange={handleCurrencyChange}
          options={currencies.map(c => ({ label: c.name, value: c.id }))}
          style={{ width: '100%' }}
          placeholder="Select Currency"
        />
      </Form.Item>
    </Form>
  )
}

/**
 * CoreShop PaymentBundle - Addition Amount Action
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
import { Form, InputNumber, Select } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ActionComponentProps } from '@coreshop/rule/src/rules/types'
import { currencyApi } from '@coreshop/currency/src/modules/currencies/api'

export const AdditionAmountAction: React.FC<ActionComponentProps> = ({ data, onChange }) => {
  const { t } = useTranslation()
  const [form] = Form.useForm()
  const [currencies, setCurrencies] = React.useState<Array<{ value: number, label: string }>>([])

  React.useEffect(() => {
    form.setFieldsValue(data ?? {})
  }, [data])

  React.useEffect(() => {
    void loadCurrencies()
  }, [])

  const loadCurrencies = async () => {
    try {
      const list = await currencyApi.list()
      setCurrencies(list.map(c => ({
        value: c.id!,
        label: c.name ?? `#${c.id}`
      })))
    } catch (err) {
      console.error('Failed to load currencies:', err)
    }
  }

  return (
    <Form
      form={form}
      layout="vertical"
      onValuesChange={(_, allValues) => {
        onChange(allValues)
      }}
    >
      <Form.Item
        label={t('coreshop_action_additionAmount_amount', { defaultValue: 'Amount' })}
        name="amount"
        help={t('coreshop_action_additionAmount_help', { defaultValue: 'Amount to add (in smallest currency unit, e.g., cents)' })}
        rules={[{ required: true, message: t('coreshop_amount_required', { defaultValue: 'Amount is required' }) }]}
      >
        <InputNumber
          min={0}
          step={1}
          precision={0}
          style={{ width: '100%' }}
          placeholder={t('coreshop_amount', { defaultValue: 'Amount' })}
        />
      </Form.Item>

      <Form.Item
        label={t('coreshop_currency', { defaultValue: 'Currency' })}
        name="currency"
        help={t('coreshop_action_additionAmount_currency_help', { defaultValue: 'Currency for the amount' })}
        rules={[{ required: true, message: t('coreshop_currency_required', { defaultValue: 'Currency is required' }) }]}
      >
        <Select
          placeholder={t('coreshop_select_currency', { defaultValue: 'Select currency' })}
          options={currencies}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}

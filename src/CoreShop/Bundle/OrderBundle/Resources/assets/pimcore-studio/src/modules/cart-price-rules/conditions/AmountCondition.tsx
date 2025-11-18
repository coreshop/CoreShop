/**
 * CoreShop OrderBundle Studio Plugin
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
import { Form, InputNumber } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'

export const AmountCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const minAmount = data.minAmount || 0
  const maxAmount = data.maxAmount || 0

  const handleChange = (field: string, value: number | null) => {
    onChange({ ...data, [field]: value || 0 })
  }

  return (
    <Form layout="vertical">
      <Form.Item label={t('coreshop_condition_amount_minAmount', { defaultValue: 'Min Amount' })}>
        <InputNumber
          value={minAmount}
          onChange={(value) => handleChange('minAmount', value)}
          min={0}
          precision={2}
          style={{ width: '100%' }}
        />
      </Form.Item>

      <Form.Item label={t('coreshop_condition_amount_maxAmount', { defaultValue: 'Max Amount' })}>
        <InputNumber
          value={maxAmount}
          onChange={(value) => handleChange('maxAmount', value)}
          min={0}
          precision={2}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}

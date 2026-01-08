/**
 * CoreShop PaymentBundle Studio Plugin
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
import { Form, InputNumber, Checkbox } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'

interface AmountConditionConfig {
  minAmount?: number
  maxAmount?: number
  gross?: boolean
  useTotal?: boolean
}

export const AmountCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const minAmount = data?.minAmount || 0
  const maxAmount = data?.maxAmount || 0
  const gross = data?.gross ?? true
  const useTotal = data?.useTotal ?? false

  const handleChange = (field: string, value: any) => {
    onChange({ ...data, [field]: value })
  }

  return (
    <>
      <Form.Item
        label={t('coreshop_condition_amount_minAmount', { defaultValue: 'Minimum Amount' })}
      >
        <InputNumber
          value={minAmount}
          onChange={(value) => handleChange('minAmount', value || 0)}
          min={0}
          precision={2}
          style={{ width: '100%' }}
        />
      </Form.Item>

      <Form.Item
        label={t('coreshop_condition_amount_maxAmount', { defaultValue: 'Maximum Amount' })}
      >
        <InputNumber
          value={maxAmount}
          onChange={(value) => handleChange('maxAmount', value || 0)}
          min={0}
          precision={2}
          style={{ width: '100%' }}
        />
      </Form.Item>

      <Form.Item>
        <Checkbox
          checked={gross}
          onChange={(e) => handleChange('gross', e.target.checked)}
        >
          {t('coreshop_condition_amount_gross', { defaultValue: 'Use Gross Amount' })}
        </Checkbox>
      </Form.Item>

      <Form.Item>
        <Checkbox
          checked={useTotal}
          onChange={(e) => handleChange('useTotal', e.target.checked)}
        >
          {t('coreshop_condition_amount_use_total', { defaultValue: 'Use Total Amount' })}
        </Checkbox>
      </Form.Item>
    </>
  )
}

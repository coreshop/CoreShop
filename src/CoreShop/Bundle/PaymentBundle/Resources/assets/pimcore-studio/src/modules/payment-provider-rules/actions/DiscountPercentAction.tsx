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
import { Form, InputNumber } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ActionComponentProps } from '@coreshop/rule/src/rules/types'

interface PercentActionConfig {
  percent?: number
}

export const DiscountPercentAction: React.FC<ActionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const percent = data?.percent || 0

  const handleChange = (value: number | null) => {
    onChange({ ...data, percent: value || 0 })
  }

  return (
    <Form.Item
      label={t('coreshop_action_discountPercent_percent', { defaultValue: 'Discount Percent (%)' })}
    >
      <InputNumber
        value={percent}
        onChange={handleChange}
        min={0}
        max={100}
        precision={2}
        style={{ width: '100%' }}
        addonAfter="%"
      />
    </Form.Item>
  )
}

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

import React from 'react'
import { Form, InputNumber } from 'antd'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'

export const WeightCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const minWeight = data.minWeight || 0
  const maxWeight = data.maxWeight || 0

  const handleMinChange = (value: number | null) => {
    onChange({ ...data, minWeight: value || 0 })
  }

  const handleMaxChange = (value: number | null) => {
    onChange({ ...data, maxWeight: value || 0 })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Minimum Weight">
        <InputNumber
          value={minWeight}
          onChange={handleMinChange}
          min={0}
          precision={2}
          style={{ width: '100%' }}
        />
      </Form.Item>
      <Form.Item label="Maximum Weight">
        <InputNumber
          value={maxWeight}
          onChange={handleMaxChange}
          min={0}
          precision={2}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}

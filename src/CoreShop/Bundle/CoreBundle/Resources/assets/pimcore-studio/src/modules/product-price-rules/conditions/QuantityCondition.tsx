/**
 * CoreShop CoreBundle Studio Plugin
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

interface QuantityConditionData {
  minQuantity?: number
  maxQuantity?: number
}

export const QuantityCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const conditionData = data as QuantityConditionData
  const minQuantity = conditionData.minQuantity || 0
  const maxQuantity = conditionData.maxQuantity || 0

  const handleMinChange = (value: number | null) => {
    onChange({
      ...conditionData,
      minQuantity: value || 0
    })
  }

  const handleMaxChange = (value: number | null) => {
    onChange({
      ...conditionData,
      maxQuantity: value || 0
    })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Minimum Quantity">
        <InputNumber
          value={minQuantity}
          onChange={handleMinChange}
          min={0}
          step={1}
          precision={0}
          style={{ width: '100%' }}
        />
      </Form.Item>

      <Form.Item label="Maximum Quantity">
        <InputNumber
          value={maxQuantity}
          onChange={handleMaxChange}
          min={0}
          step={1}
          precision={0}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}

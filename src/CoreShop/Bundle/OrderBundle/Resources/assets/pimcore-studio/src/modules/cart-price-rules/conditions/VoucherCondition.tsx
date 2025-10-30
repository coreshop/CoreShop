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
import { Form, InputNumber, Checkbox } from 'antd'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'

export const VoucherCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const maxUsagePerCode = data.maxUsagePerCode || 0
  const maxUsagePerUser = data.maxUsagePerUser || 0
  const onlyOnePerCart = data.onlyOnePerCart || false

  const handleChange = (field: string, value: any) => {
    onChange({ ...data, [field]: value })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Max Usage Per Code">
        <InputNumber
          value={maxUsagePerCode}
          onChange={(value) => handleChange('maxUsagePerCode', value || 0)}
          min={0}
          style={{ width: '100%' }}
        />
      </Form.Item>

      <Form.Item label="Max Usage Per User">
        <InputNumber
          value={maxUsagePerUser}
          onChange={(value) => handleChange('maxUsagePerUser', value || 0)}
          min={0}
          style={{ width: '100%' }}
        />
      </Form.Item>

      <Form.Item>
        <Checkbox
          checked={onlyOnePerCart}
          onChange={(e) => handleChange('onlyOnePerCart', e.target.checked)}
        >
          Only One Per Cart
        </Checkbox>
      </Form.Item>
    </Form>
  )
}

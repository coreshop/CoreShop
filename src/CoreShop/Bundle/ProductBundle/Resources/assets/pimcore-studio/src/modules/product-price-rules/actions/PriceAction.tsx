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
import type { ActionComponentProps } from '@coreshop/rule/src/rules'

export const PriceAction: React.FC<ActionComponentProps> = ({
  data,
  onChange
}) => {
  const price = data.price || 0

  const handleChange = (value: number | null) => {
    onChange({ ...data, price: value || 0 })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="New Price">
        <InputNumber
          value={price}
          onChange={handleChange}
          min={0}
          precision={2}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}

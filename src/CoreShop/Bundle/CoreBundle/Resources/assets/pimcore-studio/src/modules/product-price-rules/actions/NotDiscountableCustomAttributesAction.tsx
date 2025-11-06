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
import { Form, Input } from 'antd'
import type { ActionComponentProps } from '@coreshop/rule/src/rules'

export const NotDiscountableCustomAttributesAction: React.FC<ActionComponentProps> = ({
  data,
  onChange
}) => {
  const attributes = data.attributes || ''

  const handleChange = (value: string) => {
    onChange({ ...data, attributes: value })
  }

  return (
    <Form layout="vertical">
      <Form.Item
        label="Custom Attributes"
        extra="Enter comma-separated attribute names that should not be discounted"
      >
        <Input.TextArea
          value={attributes}
          onChange={(e) => handleChange(e.target.value)}
          placeholder="e.g. shipping,tax,fee"
          rows={3}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}

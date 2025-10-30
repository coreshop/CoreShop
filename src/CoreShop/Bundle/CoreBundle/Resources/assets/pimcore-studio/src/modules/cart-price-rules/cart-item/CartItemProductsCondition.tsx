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
import { Form, Select, Checkbox } from 'antd'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'

export const CartItemProductsCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const products = data.products || []
  const includeVariants = data.includeVariants || false

  const handleChange = (field: string, value: any) => {
    onChange({ ...data, [field]: value })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Products">
        <Select
          mode="multiple"
          value={products}
          onChange={(value) => handleChange('products', value)}
          placeholder="Select products"
          style={{ width: '100%' }}
        >
          {/* Products will be loaded from API */}
        </Select>
      </Form.Item>

      <Form.Item>
        <Checkbox
          checked={includeVariants}
          onChange={(e) => handleChange('includeVariants', e.target.checked)}
        >
          Include Variants
        </Checkbox>
      </Form.Item>
    </Form>
  )
}

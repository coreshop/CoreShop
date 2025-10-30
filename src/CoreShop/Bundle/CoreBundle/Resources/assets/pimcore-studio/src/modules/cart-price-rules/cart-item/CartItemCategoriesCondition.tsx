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

export const CartItemCategoriesCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const categories = data.categories || []
  const recursive = data.recursive || false

  const handleChange = (field: string, value: any) => {
    onChange({ ...data, [field]: value })
  }

  return (
    <Form layout="vertical">
      <Form.Item label="Categories">
        <Select
          mode="multiple"
          value={categories}
          onChange={(value) => handleChange('categories', value)}
          placeholder="Select categories"
          style={{ width: '100%' }}
        >
          {/* Categories will be loaded from API */}
        </Select>
      </Form.Item>

      <Form.Item>
        <Checkbox
          checked={recursive}
          onChange={(e) => handleChange('recursive', e.target.checked)}
        >
          Include Subcategories
        </Checkbox>
      </Form.Item>
    </Form>
  )
}

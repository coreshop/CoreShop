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
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'

export const CartItemCategoriesCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const categories = data.categories || []
  const recursive = data.recursive || false

  const handleChange = (field: string, value: any) => {
    onChange({ ...data, [field]: value })
  }

  return (
    <Form layout="vertical">
      <Form.Item label={t('coreshop_condition_categories', { defaultValue: 'Categories' })}>
        <Select
          mode="multiple"
          value={categories}
          onChange={(value) => handleChange('categories', value)}
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
          {t('coreshop_condition_recursive', { defaultValue: 'Include all Subcategories' })}
        </Checkbox>
      </Form.Item>
    </Form>
  )
}

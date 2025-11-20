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

import React, { useEffect } from 'react'
import { Form, Checkbox } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'
import { CategoryMultiSelect } from '@coreshop/product/src/components/CategoryMultiSelect'

export const CategoriesCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const [form] = Form.useForm()

  useEffect(() => {
    form.setFieldsValue({
      categories: data.categories,
      recursive: data.recursive
    })
  }, [data.categories, data.recursive, form])

  return (
    <Form
      form={form}
      layout="vertical"
      onValuesChange={(_, allValues) => {
        onChange({ ...data, ...allValues })
      }}
    >
      <CategoryMultiSelect
        name="categories"
        label={t('coreshop_condition_categories', { defaultValue: 'Categories' })}
        value={data.categories}
        onChange={(ids) => onChange({ ...data, categories: ids })}
      />

      <Form.Item
        name="recursive"
        valuePropName="checked"
      >
        <Checkbox>
          {t('coreshop_condition_recursive', { defaultValue: 'Include all Subcategories' })}
        </Checkbox>
      </Form.Item>
    </Form>
  )
}

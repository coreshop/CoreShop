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
import { ProductMultiSelect } from '@coreshop/product/src/components/ProductMultiSelect'

export const ProductsCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const [form] = Form.useForm()

  // Debug logging
  console.log('[ProductsCondition] Render with data:', { products: data.products, productsType: typeof data.products, isArray: Array.isArray(data.products), firstItem: Array.isArray(data.products) && data.products.length > 0 ? data.products[0] : null })

  useEffect(() => {
    form.setFieldsValue({
      products: data.products,
      includeVariants: data.includeVariants
    })
  }, [data.products, data.includeVariants, form])

  return (
    <Form
      form={form}
      layout="vertical"
      onValuesChange={(_, allValues) => {
        onChange({ ...data, ...allValues })
      }}
    >
      <ProductMultiSelect
        name="products"
        label={t('coreshop_report_products', { defaultValue: 'Products' })}
        value={data.products}
        onChange={(ids) => onChange({ ...data, products: ids })}
      />

      <Form.Item
        name="includeVariants"
        valuePropName="checked"
      >
        <Checkbox>
          {t('coreshop_condition_include_variants', { defaultValue: 'Include Variants' })}
        </Checkbox>
      </Form.Item>
    </Form>
  )
}

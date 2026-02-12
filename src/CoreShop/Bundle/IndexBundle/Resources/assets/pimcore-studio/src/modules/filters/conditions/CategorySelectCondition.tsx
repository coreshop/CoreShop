/**
 * CoreShop IndexBundle Category Select Filter Condition
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
import { Form, Checkbox, Input } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionProps } from '../types'
import { QuantityUnitSelect } from '../../shared/QuantityUnitSelect'

export const CategorySelectCondition: React.FC<ConditionProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()

  const handleCategoryChange = (id: number | null) => {
    onChange({
      configuration: { ...data.configuration, preSelect: id }
    })
  }

  return (
    <Form layout="vertical">
      <Form.Item label={t('coreshop_label', { defaultValue: 'Label' })}>
        <Input
          value={data.label}
          onChange={(e) => onChange({ label: e.target.value })}
        />
      </Form.Item>

      <Form.Item label={t('coreshop_filters_quantityUnit', { defaultValue: 'Quantity Value' })}>
        <QuantityUnitSelect
          value={data.quantityUnit ?? "0"}
          onChange={(value) => onChange({ quantityUnit: Number(value) })}
        />
      </Form.Item>

      <Form.Item label={t('coreshop_filters_category_name', { defaultValue: 'Category' })}>
        <Input
          type="number"
          value={data.configuration?.preSelect}
          onChange={(e) => handleCategoryChange(e.target.value ? Number(e.target.value) : null)}
          placeholder={t('coreshop_filters_category_id', { defaultValue: 'Category Object ID' })}
        />
      </Form.Item>

      <Form.Item>
        <Checkbox
          checked={data.configuration?.includeSubCategories ?? false}
          onChange={(e) => onChange({
            configuration: { ...data.configuration, includeSubCategories: e.target.checked }
          })}
        >
          {t('coreshop_filters_include_subcategories', { defaultValue: 'Include Subcategories' })}
        </Checkbox>
      </Form.Item>
    </Form>
  )
}

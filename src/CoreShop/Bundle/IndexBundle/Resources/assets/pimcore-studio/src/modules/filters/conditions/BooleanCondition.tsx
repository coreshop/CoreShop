/**
 * CoreShop IndexBundle Boolean Filter Condition
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
import { Form, Input, Select } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionProps } from '../types'
import { filterApi } from '../api'
import { QuantityUnitSelect } from '../../shared/QuantityUnitSelect'

export const BooleanCondition: React.FC<ConditionProps> = ({
  data,
  onChange,
  indexId
}) => {
  const { t } = useTranslation()
  const [fieldOptions, setFieldOptions] = React.useState<Array<{ label: string, value: string }>>([])
  const [loading, setLoading] = React.useState(false)

  React.useEffect(() => {
    if (!indexId) return

    setLoading(true)
    filterApi.getFieldsForIndex(indexId)
      .then(fields => {
        setFieldOptions(fields.map(f => ({ label: f.name, value: f.name })))
      })
      .catch(err => console.error('Failed to load fields:', err))
      .finally(() => setLoading(false))
  }, [indexId])

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
          onChange={(value) => onChange({ quantityUnit: value })}
        />
      </Form.Item>

      <Form.Item label={t('coreshop_filters_field', { defaultValue: 'Field' })} required>
        <Select
          value={data.configuration?.field}
          onChange={(value) => onChange({
            configuration: { ...data.configuration, field: value }
          })}
          options={fieldOptions}
          loading={loading}
          showSearch
        />
      </Form.Item>

      <Form.Item label={t('coreshop_filters_boolean', { defaultValue: 'Boolean' })}>
        <Select
          value={data.configuration?.preSelect}
          onChange={(value) => onChange({
            configuration: { ...data.configuration, preSelect: value }
          })}
          options={[
            { label: t('yes', { defaultValue: 'Yes' }), value: '1' },
            { label: t('no', { defaultValue: 'No' }), value: '0' }
          ]}
          allowClear
        />
      </Form.Item>
    </Form>
  )
}

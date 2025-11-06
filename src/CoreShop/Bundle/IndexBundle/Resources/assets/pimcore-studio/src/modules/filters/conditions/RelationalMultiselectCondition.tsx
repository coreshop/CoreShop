/**
 * CoreShop IndexBundle Relational Multiselect Filter Condition
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
import type { ConditionProps } from '../types'
import { filterApi } from '../api'
import type { FieldValue } from '../types'
import { QuantityUnitSelect } from '../../shared/QuantityUnitSelect'

/**
 * Relational Multiselect Condition - Multiple-select dropdown for relational data
 *
 * Similar to MultiselectCondition but optimized for relational fields
 *
 * Form fields:
 * - field: Index field to filter
 * - preSelects: Array of default selected values
 */
export const RelationalMultiselectCondition: React.FC<ConditionProps> = ({
  data,
  onChange,
  indexId
}) => {
  const [fieldOptions, setFieldOptions] = React.useState<Array<{ label: string, value: string }>>([])
  const [valueOptions, setValueOptions] = React.useState<FieldValue[]>([])
  const [loading, setLoading] = React.useState(false)

  // Load available fields when indexId changes
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

  // Load field values when field changes
  React.useEffect(() => {
    if (!indexId || !data.configuration?.field) return

    filterApi.getValuesForFilterField(indexId, data.configuration.field)
      .then(setValueOptions)
      .catch(err => console.error('Failed to load field values:', err))
  }, [indexId, data.configuration?.field])

  return (
    <Form layout="vertical">
      <Form.Item label="Label" help="Display label for the filter">
        <Input
          value={data.label}
          onChange={(e) => onChange({ label: e.target.value })}
          placeholder="Filter label"
        />
      </Form.Item>

      <Form.Item label="Quantity Unit" help="Unit for quantity values">
        <QuantityUnitSelect
          value={data.quantityUnit ?? "0"}
          onChange={(value) => onChange({ quantityUnit: value })}
        />
      </Form.Item>

      <Form.Item label="Field" required help="Index field to filter">
        <Select
          value={data.configuration?.field}
          onChange={(value) => onChange({
            configuration: { ...data.configuration, field: value }
          })}
          options={fieldOptions}
          loading={loading}
          placeholder="Select field"
          showSearch
        />
      </Form.Item>

      <Form.Item label="Pre-Select Values" help="Default selected values">
        <Select
          mode="multiple"
          value={data.configuration?.preSelects ?? []}
          onChange={(values) => onChange({
            configuration: { ...data.configuration, preSelects: values }
          })}
          options={valueOptions.map(v => ({ label: v.value, value: v.key }))}
          placeholder="Select default values"
          allowClear
        />
      </Form.Item>
    </Form>
  )
}

/**
 * CoreShop IndexBundle Range Filter Condition
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
import { Form, Input, InputNumber, Select } from 'antd'
import type { ConditionProps } from '../types'
import { filterApi } from '../api'
import type { FieldValue } from '../types'
import { QuantityUnitSelect } from '../../shared/QuantityUnitSelect'

/**
 * Range Condition - Filter by value range (e.g., price from-to)
 *
 * Form fields (from FilterConditionRangeType):
 * - field: Index field to filter
 * - preSelectMin: Default minimum value
 * - preSelectMax: Default maximum value
 * - stepCount: Number of slider steps
 */
export const RangeCondition: React.FC<ConditionProps> = ({
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

      <Form.Item label="Step Count" help="Number of slider steps">
        <InputNumber
          value={data.configuration?.stepCount}
          onChange={(value) => onChange({
            configuration: { ...data.configuration, stepCount: value }
          })}
          min={0}
          step={0.01}
          style={{ width: '100%' }}
        />
      </Form.Item>

      <Form.Item label="Pre-Select Min" help="Default minimum value">
        <Select
          value={data.configuration?.preSelectMin}
          onChange={(value) => onChange({
            configuration: { ...data.configuration, preSelectMin: value }
          })}
          options={valueOptions.map(v => ({ label: v.value, value: v.key }))}
          placeholder="Select min value"
          allowClear
        />
      </Form.Item>

      <Form.Item label="Pre-Select Max" help="Default maximum value">
        <Select
          value={data.configuration?.preSelectMax}
          onChange={(value) => onChange({
            configuration: { ...data.configuration, preSelectMax: value }
          })}
          options={valueOptions.map(v => ({ label: v.value, value: v.key }))}
          placeholder="Select max value"
          allowClear
        />
      </Form.Item>
    </Form>
  )
}

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
import type { ConditionProps } from '../types'
import { filterApi } from '../api'
import { QuantityUnitSelect } from '../../shared/QuantityUnitSelect'

/**
 * Boolean Condition - Yes/No filter
 *
 * Form fields (from FilterConditionBooleanType):
 * - field: Index field to filter
 * - preSelect: Default value (yes/no)
 */
export const BooleanCondition: React.FC<ConditionProps> = ({
  data,
  onChange,
  indexId
}) => {
  const [fieldOptions, setFieldOptions] = React.useState<Array<{ label: string, value: string }>>([])
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

      <Form.Item label="Pre-Select" help="Default value">
        <Select
          value={data.configuration?.preSelect}
          onChange={(value) => onChange({
            configuration: { ...data.configuration, preSelect: value }
          })}
          options={[
            { label: 'Yes', value: '1' },
            { label: 'No', value: '0' }
          ]}
          placeholder="Select default value"
          allowClear
        />
      </Form.Item>
    </Form>
  )
}

/**
 * CoreShop IndexBundle Search Filter Condition
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
 * Search Condition - Full-text search filter
 *
 * Form fields (from FilterConditionSearchType):
 * - name: Input name attribute
 * - fields: Array of fields to search
 * - searchTerm: Search term field
 * - concatenator: Field concatenation method
 * - pattern: Search pattern
 */
export const SearchCondition: React.FC<ConditionProps> = ({
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
          placeholder="Search label"
        />
      </Form.Item>

      <Form.Item label="Quantity Unit" help="Unit for quantity values">
        <QuantityUnitSelect
          value={data.quantityUnit ?? "0"}
          onChange={(value) => onChange({ quantityUnit: value })}
        />
      </Form.Item>

      <Form.Item label="Name" help="Input name attribute">
        <Input
          value={data.configuration?.name}
          onChange={(e) => onChange({
            configuration: { ...data.configuration, name: e.target.value }
          })}
          placeholder="search_query"
        />
      </Form.Item>

      <Form.Item label="Fields" required help="Index fields to search">
        <Select
          mode="multiple"
          value={data.configuration?.fields ?? []}
          onChange={(values) => onChange({
            configuration: { ...data.configuration, fields: values }
          })}
          options={fieldOptions}
          loading={loading}
          placeholder="Select fields to search"
        />
      </Form.Item>

      <Form.Item label="Search Term" help="Search term field name">
        <Input
          value={data.configuration?.searchTerm}
          onChange={(e) => onChange({
            configuration: { ...data.configuration, searchTerm: e.target.value }
          })}
          placeholder="Search term"
        />
      </Form.Item>

      <Form.Item label="Concatenator" help="Field concatenation method">
        <Select
          value={data.configuration?.concatenator ?? 'OR'}
          onChange={(value) => onChange({
            configuration: { ...data.configuration, concatenator: value }
          })}
          options={[
            { label: 'OR', value: 'OR' },
            { label: 'AND', value: 'AND' }
          ]}
        />
      </Form.Item>

      <Form.Item label="Pattern" help="Search pattern - how to match the search term">
        <Select
          value={data.configuration?.pattern ?? 'both'}
          onChange={(value) => onChange({
            configuration: { ...data.configuration, pattern: value }
          })}
          options={[
            { label: 'Contains', value: 'both' },
            { label: 'Begins with', value: 'left' },
            { label: 'Ends with', value: 'right' }
          ]}
        />
      </Form.Item>
    </Form>
  )
}

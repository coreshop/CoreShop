/**
 * CoreShop IndexBundle - Filter Value Multi-Select Widget
 *
 * Schema widget for selecting multiple values for a filter field.
 * Uses FilterIndexContext for indexId and Form.useWatch for the current field name.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Select, Form } from 'antd'
import { useFilterIndex } from '../FilterIndexContext'
import { filterApi } from '../api'
import type { FieldValue } from '../types'

interface FilterValueMultiSelectProps {
  value?: Array<string | number>
  onChange?: (value: Array<string | number>) => void
}

export const FilterValueMultiSelect: React.FC<FilterValueMultiSelectProps> = ({ value, onChange }) => {
  const { indexId } = useFilterIndex()
  const fieldName = Form.useWatch('field')
  const [options, setOptions] = React.useState<FieldValue[]>([])
  const [loading, setLoading] = React.useState(false)

  React.useEffect(() => {
    if (!indexId || !fieldName) {
      setOptions([])
      return
    }

    setLoading(true)
    filterApi.getValuesForFilterField(indexId, fieldName)
      .then(setOptions)
      .catch(err => {
        console.error('Failed to load field values:', err)
      })
      .finally(() => setLoading(false))
  }, [indexId, fieldName])

  return (
    <Select
      mode="multiple"
      value={value ?? []}
      onChange={onChange}
      options={options.map(v => ({ label: v.value, value: v.key }))}
      loading={loading}
      allowClear
      showSearch
      filterOption={(input, option) =>
        String(option?.label ?? '').toLowerCase().includes(input.toLowerCase())
      }
    />
  )
}

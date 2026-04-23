/**
 * CoreShop IndexBundle - Filter Fields Multi-Select Widget
 *
 * Schema widget for selecting multiple index fields (e.g., search condition).
 * Uses FilterIndexContext to get the current indexId.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Select } from 'antd'
import { useFilterIndex } from '../FilterIndexContext'
import { filterApi } from '../api'

interface FilterFieldsMultiSelectProps {
  value?: string[]
  onChange?: (value: string[]) => void
}

export const FilterFieldsMultiSelect: React.FC<FilterFieldsMultiSelectProps> = ({ value, onChange }) => {
  const { indexId } = useFilterIndex()
  const [options, setOptions] = React.useState<Array<{ label: string, value: string }>>([])
  const [loading, setLoading] = React.useState(false)

  React.useEffect(() => {
    if (!indexId) {
      setOptions([])
      return
    }

    setLoading(true)
    filterApi.getFieldsForIndex(indexId)
      .then(fields => {
        setOptions(fields.map(f => ({ label: f.name, value: f.name })))
      })
      .catch(err => {
        console.error('Failed to load index fields:', err)
      })
      .finally(() => setLoading(false))
  }, [indexId])

  return (
    <Select
      mode="multiple"
      value={value ?? []}
      onChange={onChange}
      options={options}
      loading={loading}
      showSearch
      filterOption={(input, option) =>
        (option?.label ?? '').toLowerCase().includes(input.toLowerCase())
      }
    />
  )
}

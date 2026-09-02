/**
 * CoreShop PimcoreBundle Grid Filter Dropdown
 *
 * Dropdown component for selecting grid filters.
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
import { Select, Space, Typography } from 'antd'
import { FilterOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import { useGridFilters } from '../hooks'

const { Text } = Typography

interface GridFilterDropdownProps {
  listType: string
  value: string | null
  onChange: (filterId: string | null) => void
  style?: React.CSSProperties
}

/**
 * Dropdown for selecting a grid filter
 *
 * @example
 * <GridFilterDropdown
 *   listType="coreshop_order"
 *   value={selectedFilter}
 *   onChange={setSelectedFilter}
 * />
 */
export const GridFilterDropdown: React.FC<GridFilterDropdownProps> = ({
  listType,
  value,
  onChange,
  style
}) => {
  const { t } = useTranslation()
  const { filters, loading } = useGridFilters(listType)

  const options = [
    {
      value: '',
      label: t('coreshop_grid_filter_empty', { defaultValue: 'No Filter' })
    },
    ...filters.map(filter => ({
      value: filter.id,
      label: filter.name
    }))
  ]

  // Only show if loading or if there are filters available
  const hasFilters = loading || filters.length > 0

  if (!hasFilters) {
    return null
  }

  return (
    <Space style={style}>
      <FilterOutlined />
      <Text>{t('coreshop_grid_filter', { defaultValue: 'Filter' })}:</Text>
      <Select
        value={value ?? ''}
        onChange={(val) => onChange(val === '' ? null : val)}
        loading={loading}
        options={options}
        style={{ minWidth: 180 }}
        showSearch
        filterOption={(input, option) =>
          (option?.label ?? '').toString().toLowerCase().includes(input.toLowerCase())
        }
        placeholder={t('coreshop_grid_filter_select', { defaultValue: 'Select filter...' })}
      />
    </Space>
  )
}

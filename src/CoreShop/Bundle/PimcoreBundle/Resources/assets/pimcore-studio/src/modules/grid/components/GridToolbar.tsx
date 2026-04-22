/**
 * CoreShop PimcoreBundle Grid Toolbar
 *
 * Toolbar component that combines filter dropdown and refresh functionality.
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
import { Space, Button } from 'antd'
import { ReloadOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import { GridFilterDropdown } from './GridFilterDropdown'
import { coreshopBroker } from '../../broker'
import { GRID_EVENTS } from '../events'

interface GridToolbarProps {
  listType: string
  selectedFilter: string | null
  onFilterChange: (filterId: string | null) => void
  onRefresh?: () => void
  children?: React.ReactNode
  style?: React.CSSProperties
}

/**
 * Grid toolbar with filter dropdown and optional refresh button
 *
 * @example
 * <GridToolbar
 *   listType="coreshop_order"
 *   selectedFilter={filter}
 *   onFilterChange={setFilter}
 *   onRefresh={handleRefresh}
 * >
 *   <Button icon={<PlusOutlined />}>Create Order</Button>
 * </GridToolbar>
 */
export const GridToolbar: React.FC<GridToolbarProps> = ({
  listType,
  selectedFilter,
  onFilterChange,
  onRefresh,
  children,
  style
}) => {
  const { t } = useTranslation()

  // Fire event to allow extensions to add toolbar items
  const extensionItems: React.ReactNode[] = []
  coreshopBroker.fireEvent(GRID_EVENTS.TOOLBAR_ENHANCING, {
    listType,
    toolbarItems: extensionItems
  })

  const handleFilterChange = (filterId: string | null): void => {
    onFilterChange(filterId)
  }

  return (
    <Space style={{ width: '100%', justifyContent: 'space-between', ...style }}>
      <Space>
        {children}
        <GridFilterDropdown
          listType={listType}
          value={selectedFilter}
          onChange={handleFilterChange}
        />
        {extensionItems}
      </Space>
      <Space>
        {onRefresh && (
          <Button
            icon={<ReloadOutlined />}
            onClick={onRefresh}
            title={t('coreshop_grid_refresh', { defaultValue: 'Refresh' })}
          />
        )}
      </Space>
    </Space>
  )
}

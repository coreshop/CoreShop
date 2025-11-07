/**
 * CoreShop IndexBundle Index Detail
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
import { Tabs } from 'antd'
import type { Index, IndexConfig } from './api'
import { SettingsForm, ColumnsPanel } from './components'
import { useIndexDetailStyles } from './index-detail.styles'

interface IndexDetailProps {
  index: Index
  config: IndexConfig
  onChange: (index: Index) => void
}

export const IndexDetail: React.FC<IndexDetailProps> = ({
  index,
  config,
  onChange
}) => {
  const { styles } = useIndexDetailStyles()

  const tabs = [
    {
      key: 'settings',
      label: 'Settings',
      children: (
        <div style={{ padding: 24 }}>
          <SettingsForm
            index={index}
            config={config}
            onChange={onChange}
          />
        </div>
      )
    },
    {
      key: 'fields',
      label: 'Fields',
      children: (
        <ColumnsPanel
          index={index}
          config={config}
          onChange={onChange}
        />
      )
    }
  ]

  return (
    <div className={styles.root}>
      <Tabs
        defaultActiveKey="settings"
        items={tabs}
        tabBarStyle={{ paddingLeft: 24, paddingRight: 24, marginBottom: 0 }}
        destroyInactiveTabPane={false}
      />
    </div>
  )
}

/**
 * Zone Panel Component
 * 
 * React component for managing zones
 * Replaces the ExtJS coreshop.zone.panel
 */

import React from 'react'
import { Tag } from 'antd'
// import { ResourcePanel } from '@coreshop/resource-studio-plugin' // TODO: implement
import { Zone } from '@/types'

interface ZonePanelProps {
  onItemSelect?: (item: Zone) => void
}

export const ZonePanel: React.FC<ZonePanelProps> = ({ onItemSelect }) => {
  const config = {
    layoutId: 'coreshop_zones_panel',
    storeId: 'coreshop_zones',
    iconCls: 'coreshop_icon_zone',
    type: 'coreshop_zones',
    title: 'Zones',
    routing: {
      add: 'coreshop_zone_add',
      delete: 'coreshop_zone_delete',
      get: 'coreshop_zone_get',
      list: 'coreshop_zone_list'
    }
  }

  const columns = [
    {
      title: 'Name',
      dataIndex: 'name',
      render: (value: string, record: Zone) => (
        <span 
          style={{ cursor: 'pointer' }}
          onClick={() => onItemSelect?.(record)}
          title={`ID: ${record.id}`}
        >
          {value}
        </span>
      )
    },
    {
      title: 'Countries',
      dataIndex: 'countries',
      render: (countries: any[]) => 
        countries?.length ? (
          <span>{countries.length} countries</span>
        ) : (
          <span style={{ color: '#999' }}>No countries</span>
        )
    },
    {
      title: 'Active',
      dataIndex: 'active',
      width: 80,
      render: (active: boolean) => (
        <Tag color={active ? 'green' : 'red'}>
          {active ? 'Active' : 'Inactive'}
        </Tag>
      )
    }
  ]

  return (
    <div style={{ padding: '20px' }}>
      <h2>Zones Management</h2>
      <p>This is a placeholder for the Zones panel component.</p>
      <p>Would manage geographical zones containing multiple countries.</p>
      <Tag color="purple">Multi-Country Support</Tag>
      <Tag color="orange">Geographical Grouping</Tag>
    </div>
  )
}

export default ZonePanel
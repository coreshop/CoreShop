/**
 * State Panel Component
 * 
 * React component for managing states
 * Replaces the ExtJS coreshop.state.panel
 */

import React from 'react'
import { Tag } from 'antd'
// import { ResourcePanel } from '@coreshop/resource-studio-plugin' // TODO: implement
import { State } from '@/types'

interface StatePanelProps {
  onItemSelect?: (item: State) => void
}

export const StatePanel: React.FC<StatePanelProps> = ({ onItemSelect }) => {
  const config = {
    layoutId: 'coreshop_states_panel',
    storeId: 'coreshop_states',
    iconCls: 'coreshop_icon_state',
    type: 'coreshop_states',
    title: 'States',
    routing: {
      add: 'coreshop_state_add',
      delete: 'coreshop_state_delete',
      get: 'coreshop_state_get',
      list: 'coreshop_state_list'
    }
  }

  const columns = [
    {
      title: 'Name',
      dataIndex: 'name',
      render: (value: string, record: State) => (
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
      title: 'ISO Code',
      dataIndex: 'isoCode',
      width: 100
    },
    {
      title: 'Country',
      dataIndex: 'country',
      render: (country: any) => country ? <Tag color="blue">{country.name}</Tag> : '-'
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
      <h2>States Management</h2>
      <p>This is a placeholder for the States panel component.</p>
      <p>Would display states/provinces with country relationships.</p>
      <Tag color="blue">Country Integration</Tag>
      <Tag color="green">ISO Code Support</Tag>
    </div>
  )
}

export default StatePanel
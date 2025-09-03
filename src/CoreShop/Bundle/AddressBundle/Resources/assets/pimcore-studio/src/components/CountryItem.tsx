/**
 * Country Item Component
 * 
 * React component for editing individual countries
 * Replaces the ExtJS coreshop.country.item
 */

import React from 'react'
import { Form, Input, Switch, Select } from 'antd'
// import { ResourceItem } from '@coreshop/resource-studio-plugin' // TODO: implement
import { Country, Zone } from '@/types'
import { useZones } from '@/hooks/useZones'

interface CountryItemProps {
  data: Country
  onSave?: (item: Country) => void
  onCancel?: () => void
}

export const CountryItem: React.FC<CountryItemProps> = ({ 
  data, 
  onSave, 
  onCancel 
}) => {
  const { zones, loading: zonesLoading } = useZones()
  
  const config = {
    layoutId: 'coreshop_countries_panel',
    storeId: 'coreshop_countries',
    iconCls: 'coreshop_icon_country',
    type: 'coreshop_countries',
    title: 'Countries',
    routing: {
      add: 'coreshop_country_add',
      delete: 'coreshop_country_delete',
      get: 'coreshop_country_get',
      list: 'coreshop_country_list'
    },
    data,
    panelKey: `country_${data.id}`
  }

  const customFields = (
    <>
      <Form.Item
        name="isoCode"
        label="ISO Code"
        rules={[
          { required: true, message: 'Please enter an ISO code' },
          { len: 2, message: 'ISO code must be exactly 2 characters' },
          { pattern: /^[A-Z]{2}$/, message: 'ISO code must be 2 uppercase letters' }
        ]}
      >
        <Input 
          placeholder="e.g., US, DE, AT" 
          maxLength={2}
          style={{ textTransform: 'uppercase' }}
        />
      </Form.Item>

      <Form.Item
        name="zone"
        label="Zone"
        rules={[{ required: false }]}
      >
        <Select
          placeholder="Select a zone"
          loading={zonesLoading}
          allowClear
          showSearch
          optionFilterProp="children"
        >
          {zones.map(zone => (
            <Select.Option key={zone.id} value={zone.id}>
              {zone.name}
            </Select.Option>
          ))}
        </Select>
      </Form.Item>

      <Form.Item
        name="active"
        label="Active"
        valuePropName="checked"
      >
        <Switch />
      </Form.Item>
    </>
  )

  return (
    <div style={{ padding: '20px' }}>
      <h3>Country Item Editor</h3>
      <p>Placeholder for country editing form with:</p>
      <ul>
        <li>Name field</li>
        <li>ISO Code validation</li>
        <li>Zone selection</li>
        <li>Active/inactive toggle</li>
      </ul>
    </div>
  )
}

export default CountryItem
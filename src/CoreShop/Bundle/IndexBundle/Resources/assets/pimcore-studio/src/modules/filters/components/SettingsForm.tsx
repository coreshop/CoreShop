/**
 * CoreShop IndexBundle Filter Settings Form
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
import { Form, Input, InputNumber, Select } from 'antd'
import type { Filter } from '../types'
import { IndexSelect } from '../../shared/IndexSelect'

interface SettingsFormProps {
  filter: Filter
  onChange: (filter: Filter) => void
}

export const SettingsForm: React.FC<SettingsFormProps> = ({
  filter,
  onChange
}) => {
  return (
    <div style={{ padding: 24 }}>
      <Form layout="vertical">
        <Form.Item
          label="Name"
          required
          help="Unique identifier for the filter"
        >
          <Input
            value={filter.name}
            onChange={(e) => onChange({ ...filter, name: e.target.value })}
            placeholder="Filter name"
          />
        </Form.Item>

        <Form.Item
          label="Index"
          required
          help="Select the index to filter"
        >
          <IndexSelect
            value={filter.index ?? undefined}
            onChange={(value) => onChange({ ...filter, index: value ?? null })}
            placeholder="Select an index"
          />
        </Form.Item>

        <Form.Item
          label="Order Direction"
          help="Sort direction for results"
        >
          <Select
            value={filter.orderDirection ?? 'desc'}
            onChange={(value) => onChange({ ...filter, orderDirection: value })}
            options={[
              { label: 'Descending', value: 'desc' },
              { label: 'Ascending', value: 'asc' }
            ]}
          />
        </Form.Item>

        <Form.Item
          label="Order Key"
          help="Field to sort by"
        >
          <Input
            value={filter.orderKey ?? ''}
            onChange={(e) => onChange({ ...filter, orderKey: e.target.value })}
            placeholder="e.g., name, price"
          />
        </Form.Item>

        <Form.Item
          label="Results Per Page"
          help="Number of results to show per page"
        >
          <InputNumber
            value={filter.resultsPerPage ?? 10}
            onChange={(value) => onChange({ ...filter, resultsPerPage: value ?? 10 })}
            min={1}
            max={1000}
            style={{ width: '100%' }}
          />
        </Form.Item>
      </Form>
    </div>
  )
}

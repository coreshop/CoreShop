/**
 * CoreShop IndexBundle OpenSearch Worker Configurator
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
import { Form, Select, InputNumber } from 'antd'
import type { WorkerConfiguratorProps } from '../../registry'
import { indexApi } from '../../api'

export const OpenSearchWorkerConfigurator: React.FC<WorkerConfiguratorProps> = ({
  configuration,
  onChange
}) => {
  const [openSearchClients, setOpenSearchClients] = React.useState<Array<{ name: string }>>([])

  // Load OpenSearch clients on mount
  React.useEffect(() => {
    indexApi.getOpenSearchClients()
      .then(setOpenSearchClients)
      .catch(err => {
        console.error('Failed to load OpenSearch clients:', err)
      })
  }, [])

  const handleChange = (field: string, value: any) => {
    onChange({
      ...configuration,
      [field]: value
    })
  }

  return (
    <div>
      <Form.Item
        label="Client"
        help="OpenSearch client name configured in Symfony"
      >
        <Select
          value={configuration?.client}
          onChange={(value) => handleChange('client', value)}
          options={openSearchClients.map(c => ({
            label: c.name,
            value: c.name
          }))}
          placeholder="Select OpenSearch client"
          showSearch
        />
      </Form.Item>

      <Form.Item
        label="Number of Shards"
        help="Primary shards for the index (default: 1)"
      >
        <InputNumber
          value={configuration?.numberOfShards ?? 1}
          onChange={(value) => handleChange('numberOfShards', value)}
          min={1}
          max={100}
          style={{ width: '100%' }}
        />
      </Form.Item>

      <Form.Item
        label="Number of Replicas"
        help="Replica shards for the index (default: 1)"
      >
        <InputNumber
          value={configuration?.numberOfReplicas ?? 1}
          onChange={(value) => handleChange('numberOfReplicas', value)}
          min={0}
          max={10}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </div>
  )
}

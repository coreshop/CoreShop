/**
 * CoreShop IndexBundle MySQL Worker Configurator
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
import { Button, Select, Space, Tag } from 'antd'
import { PlusOutlined, DeleteOutlined } from '@ant-design/icons'
import type { WorkerConfiguratorProps } from '../../registry'

interface IndexDefinition {
  type: 'INDEX' | 'UNIQUE'
  columns: string[]
}

export const MysqlWorkerConfigurator: React.FC<WorkerConfiguratorProps & { availableColumns?: string[] }> = ({
  configuration,
  onChange,
  availableColumns = []
}) => {
  const indexes = (configuration?.indexes || []) as IndexDefinition[]
  const localizedIndexes = (configuration?.localizedIndexes || []) as IndexDefinition[]

  const handleAddIndex = (localized: boolean) => {
    const newIndex: IndexDefinition = {
      type: 'INDEX',
      columns: []
    }

    if (localized) {
      onChange({
        ...configuration,
        localizedIndexes: [...localizedIndexes, newIndex]
      })
    } else {
      onChange({
        ...configuration,
        indexes: [...indexes, newIndex]
      })
    }
  }

  const handleRemoveIndex = (localized: boolean, index: number) => {
    if (localized) {
      onChange({
        ...configuration,
        localizedIndexes: localizedIndexes.filter((_, i) => i !== index)
      })
    } else {
      onChange({
        ...configuration,
        indexes: indexes.filter((_, i) => i !== index)
      })
    }
  }

  const handleUpdateIndex = (localized: boolean, index: number, field: keyof IndexDefinition, value: any) => {
    if (localized) {
      const updated = [...localizedIndexes]
      updated[index] = { ...updated[index], [field]: value }
      onChange({
        ...configuration,
        localizedIndexes: updated
      })
    } else {
      const updated = [...indexes]
      updated[index] = { ...updated[index], [field]: value }
      onChange({
        ...configuration,
        indexes: updated
      })
    }
  }

  const renderIndexList = (indexList: IndexDefinition[], localized: boolean) => (
    <div>
      {indexList.map((idx, i) => {
        // Ensure columns is always an array
        const columns = Array.isArray(idx.columns) ? idx.columns : []

        return (
          <div
            key={i}
            style={{
              display: 'flex',
              gap: 8,
              marginBottom: 12,
              padding: 12,
              background: 'var(--ant-color-bg-container)',
              border: '1px solid var(--ant-color-border)',
              borderRadius: 4
            }}
          >
            <Select
              value={idx.type}
              onChange={(value) => handleUpdateIndex(localized, i, 'type', value)}
              style={{ width: 120 }}
              options={[
                { label: 'INDEX', value: 'INDEX' },
                { label: 'UNIQUE', value: 'UNIQUE' }
              ]}
            />
            <Select
              mode="multiple"
              value={columns}
              onChange={(value) => handleUpdateIndex(localized, i, 'columns', value)}
              placeholder="Select columns"
              style={{ flex: 1 }}
              options={availableColumns.map(col => ({
                label: col,
                value: col
              }))}
            />
            <Button
              danger
              icon={<DeleteOutlined />}
              onClick={() => handleRemoveIndex(localized, i)}
            />
          </div>
        )
      })}
    </div>
  )

  return (
    <div>
      <div style={{ marginBottom: 24 }}>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 12 }}>
          <h4 style={{ margin: 0 }}>
            Indexes <Tag color="blue">{indexes.length}</Tag>
          </h4>
          <Button
            type="dashed"
            icon={<PlusOutlined />}
            onClick={() => handleAddIndex(false)}
            size="small"
          >
            Add Index
          </Button>
        </div>
        <p style={{ color: 'var(--ant-color-text-secondary)', fontSize: 12, marginBottom: 12 }}>
          Define database indexes for non-localized fields to improve query performance.
        </p>
        {indexes.length === 0 ? (
          <div style={{
            padding: 16,
            textAlign: 'center',
            color: 'var(--ant-color-text-tertiary)',
            border: '1px dashed var(--ant-color-border)',
            borderRadius: 4
          }}>
            No indexes defined
          </div>
        ) : (
          renderIndexList(indexes, false)
        )}
      </div>

      <div>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 12 }}>
          <h4 style={{ margin: 0 }}>
            Localized Indexes <Tag color="green">{localizedIndexes.length}</Tag>
          </h4>
          <Button
            type="dashed"
            icon={<PlusOutlined />}
            onClick={() => handleAddIndex(true)}
            size="small"
          >
            Add Localized Index
          </Button>
        </div>
        <p style={{ color: 'var(--ant-color-text-secondary)', fontSize: 12, marginBottom: 12 }}>
          Define database indexes for localized fields to improve query performance.
        </p>
        {localizedIndexes.length === 0 ? (
          <div style={{
            padding: 16,
            textAlign: 'center',
            color: 'var(--ant-color-text-tertiary)',
            border: '1px dashed var(--ant-color-border)',
            borderRadius: 4
          }}>
            No localized indexes defined
          </div>
        ) : (
          renderIndexList(localizedIndexes, true)
        )}
      </div>
    </div>
  )
}

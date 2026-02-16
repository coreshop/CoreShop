/**
 * CoreShop IndexBundle Nested Interpreter Configurator
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
import { Form, Select, Button, List, Card, Space, Typography } from 'antd'
import { PlusOutlined, DeleteOutlined } from '@ant-design/icons'
import type { IndexConfig } from '../api'
import { InterpreterConfigRenderer } from './InterpreterConfigRenderer'

const { Text } = Typography

interface NestedInterpreterItem {
  type: string
  interpreterConfig?: Record<string, any>
}

interface NestedInterpreterConfiguratorProps {
  value?: Record<string, any>
  onChange: (value: Record<string, any>) => void
  indexConfig?: IndexConfig
  depth?: number
}

export const NestedInterpreterConfigurator: React.FC<NestedInterpreterConfiguratorProps> = ({
  value,
  onChange,
  indexConfig,
  depth = 0
}) => {
  const interpreters: NestedInterpreterItem[] = value?.interpreters || []

  const interpreterOptions = indexConfig?.interpreters?.map(i => ({
    label: i.name,
    value: i.type
  })) || []

  const handleAdd = () => {
    const newInterpreters = [...interpreters, { type: '', interpreterConfig: {} }]
    onChange({ ...value, interpreters: newInterpreters })
  }

  const handleRemove = (index: number) => {
    const newInterpreters = interpreters.filter((_, i) => i !== index)
    onChange({ ...value, interpreters: newInterpreters })
  }

  const handleInterpreterTypeChange = (index: number, type: string) => {
    const newInterpreters = [...interpreters]
    newInterpreters[index] = { type, interpreterConfig: {} }
    onChange({ ...value, interpreters: newInterpreters })
  }

  const handleInterpreterConfigChange = (index: number, interpreterConfig: Record<string, any>) => {
    const newInterpreters = [...interpreters]
    newInterpreters[index] = { ...newInterpreters[index], interpreterConfig }
    onChange({ ...value, interpreters: newInterpreters })
  }

  // Visual indicators for nesting depth
  const depthColors = [
    'var(--ant-color-primary-bg)',
    'var(--ant-color-success-bg)',
    'var(--ant-color-warning-bg)',
    'var(--ant-color-error-bg)',
    'var(--ant-color-info-bg)'
  ]
  const borderColor = depthColors[depth % depthColors.length]
  const leftBorderWidth = Math.min(depth * 3 + 2, 10) // Max 10px

  return (
    <div style={{ marginBottom: 12 }}>
      <Form.Item label={depth === 0 ? 'Nested Interpreters' : undefined}>
        <List
          dataSource={interpreters}
          locale={{ emptyText: 'No interpreters added' }}
          renderItem={(item, index) => {
            const interpreterName = interpreterOptions.find(opt => opt.value === item.type)?.label || item.type

            return (
              <Card
                key={index}
                size="small"
                style={{
                  marginBottom: 8,
                  borderLeft: `${leftBorderWidth}px solid ${borderColor}`,
                  background: depth > 0 ? 'var(--ant-color-fill-quaternary)' : undefined
                }}
                title={
                  <Space>
                    <Text strong>#{index + 1}</Text>
                    {item.type && <Text type="secondary">{interpreterName}</Text>}
                    {!item.type && <Text type="warning">Not configured</Text>}
                  </Space>
                }
                extra={
                  <Button
                    type="text"
                    danger
                    size="small"
                    icon={<DeleteOutlined />}
                    onClick={() => handleRemove(index)}
                  />
                }
              >
                <Space direction="vertical" style={{ width: '100%' }}>
                  <Form.Item label="Interpreter Type" style={{ marginBottom: 8 }}>
                    <Select
                      value={item.type || undefined}
                      onChange={(type) => handleInterpreterTypeChange(index, type)}
                      options={interpreterOptions}
                      placeholder="Select interpreter type"
                      style={{ width: '100%' }}
                    />
                  </Form.Item>

                  {item.type && (
                    <div style={{
                      padding: 8,
                      background: 'var(--ant-color-bg-container)',
                      borderRadius: 4,
                      border: '1px solid var(--ant-color-border-secondary)'
                    }}>
                      <InterpreterConfigRenderer
                        type={item.type}
                        value={item.interpreterConfig || {}}
                        onChange={(interpreterConfig) => handleInterpreterConfigChange(index, interpreterConfig)}
                        indexConfig={indexConfig}
                        depth={depth + 1}
                      />
                    </div>
                  )}
                </Space>
              </Card>
            )
          }}
        />
      </Form.Item>

      <Button
        type="dashed"
        onClick={handleAdd}
        icon={<PlusOutlined />}
        block
        size="small"
        style={{ marginLeft: leftBorderWidth }}
      >
        Add Interpreter
      </Button>
    </div>
  )
}

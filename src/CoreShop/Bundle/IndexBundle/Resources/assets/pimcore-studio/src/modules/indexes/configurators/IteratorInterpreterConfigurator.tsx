/**
 * CoreShop IndexBundle Iterator Interpreter Configurator
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
import { Form, Select, Space } from 'antd'
import type { IndexConfig } from '../api'
import { InterpreterConfigRenderer } from './InterpreterConfigRenderer'

interface IteratorInterpreterItem {
  type: string
  interpreterConfig?: Record<string, any>
}

interface IteratorInterpreterConfiguratorProps {
  value?: Record<string, any>
  onChange: (value: Record<string, any>) => void
  indexConfig?: IndexConfig
  depth?: number
}

export const IteratorInterpreterConfigurator: React.FC<IteratorInterpreterConfiguratorProps> = ({
  value,
  onChange,
  indexConfig,
  depth = 0
}) => {
  const interpreter: IteratorInterpreterItem = value?.interpreter || { type: '', interpreterConfig: {} }

  const interpreterOptions = indexConfig?.interpreters?.map(i => ({
    label: i.name,
    value: i.type
  })) || []

  const handleTypeChange = (type: string) => {
    onChange({
      ...value,
      interpreter: { type, interpreterConfig: {} }
    })
  }

  const handleConfigChange = (interpreterConfig: Record<string, any>) => {
    onChange({
      ...value,
      interpreter: { ...interpreter, interpreterConfig }
    })
  }

  return (
    <Space direction="vertical" style={{ width: '100%' }}>
      <Form.Item label="Iterator Interpreter">
        <Select
          value={interpreter.type || undefined}
          onChange={handleTypeChange}
          options={interpreterOptions}
          placeholder="Select interpreter for iteration"
          style={{ width: '100%' }}
        />
      </Form.Item>

      {interpreter.type && (
        <div style={{
          padding: 8,
          background: 'var(--ant-color-fill-quaternary)',
          borderRadius: 4,
          border: '1px solid var(--ant-color-border-secondary)'
        }}>
          <InterpreterConfigRenderer
            type={interpreter.type}
            value={interpreter.interpreterConfig || {}}
            onChange={handleConfigChange}
            indexConfig={indexConfig}
            depth={depth + 1}
          />
        </div>
      )}
    </Space>
  )
}

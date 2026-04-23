/**
 * CoreShop IndexBundle - Interpreter Widget
 *
 * Widget for coreshop_index_column_interpreter block prefix.
 * Renders a type selector and dynamically loads the SchemaForm
 * for the selected interpreter type using context-provided mapping.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Select, Space, Typography } from 'antd'
import { SchemaForm } from '@coreshop/studio-form/src/schema-adapter'
import type { FormSchemaField } from '@coreshop/studio-form/src/schema-adapter/types'
import { useInterpreterSchema } from '../InterpreterSchemaContext'
import { mergeFormDraft } from '../mergeFormDraft'

interface InterpreterWidgetProps {
  value?: Record<string, any>
  onChange?: (next: Record<string, any>) => void
  disabled?: boolean
  field: FormSchemaField
}

export const InterpreterWidget: React.FC<InterpreterWidgetProps> = ({
  value,
  onChange,
  disabled,
  field,
}) => {
  const interpreterSchemaByType = useInterpreterSchema()
  const selectedType = value?.type
  const filterSelectOption = (input: string, option?: { label?: React.ReactNode; value?: string | number }) => {
    const term = input.toLowerCase()
    const label = (option?.label ?? '').toString().toLowerCase()
    const valueText = (option?.value ?? '').toString().toLowerCase()

    return label.includes(term) || valueText.includes(term)
  }

  // Build type options from the choice field's choices
  const typeField = field.children?.fields?.find(f => f.name === 'type')
  const typeOptions = React.useMemo(() => {
    if (typeField?.choices) {
      return typeField.choices.map(c => ({
        value: String(c.value),
        label: String(c.label),
      }))
    }
    // Fallback: derive from interpreterSchemaByType keys
    return Object.keys(interpreterSchemaByType).map(type => ({
      value: type,
      label: type,
    }))
  }, [typeField?.choices, interpreterSchemaByType])

  const blockPrefix = selectedType ? interpreterSchemaByType[selectedType] : undefined

  const handleTypeChange = (type: string | undefined) => {
    if (!type) {
      onChange?.({})
    } else {
      onChange?.({ type })
    }
  }

  const handleConfigChange = (draft: Record<string, any>) => {
    onChange?.({
      ...value,
      interpreterConfig: mergeFormDraft(value?.interpreterConfig ?? {}, draft),
    })
  }

  return (
    <Space direction="vertical" style={{ width: '100%' }}>
      <div>
        <Typography.Text type="secondary" style={{ fontSize: 12 }}>
          Interpreter Type
        </Typography.Text>
        <Select
          value={selectedType}
          onChange={handleTypeChange}
          options={typeOptions}
          placeholder="Select interpreter type"
          disabled={disabled}
          allowClear
          showSearch
          optionFilterProp="label"
          filterOption={filterSelectOption}
          style={{ width: '100%', marginTop: 6 }}
        />
      </div>
      {selectedType && blockPrefix && (
        <div style={{ marginTop: 4 }}>
          <SchemaForm
            key={selectedType}
            blockPrefix={blockPrefix}
            embedded
            data={value?.interpreterConfig ?? {}}
            onChange={handleConfigChange}
          />
        </div>
      )}
    </Space>
  )
}

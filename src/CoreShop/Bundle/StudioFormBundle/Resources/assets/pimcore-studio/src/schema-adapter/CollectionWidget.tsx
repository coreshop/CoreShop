import React from 'react'
import { Button, Card, Select } from 'antd'
import { DeleteOutlined, PlusOutlined } from '@ant-design/icons'
import type { FormSchemaField } from './types'
import type { WidgetRegistry } from './WidgetRegistry'
import { toFormBuilderConfig } from './FormSchemaAdapter'
import { DynamicForm } from '../form-builder/components/DynamicForm'

interface CollectionWidgetProps {
  value?: unknown[]
  onChange?: (nextValue: unknown[]) => void
  disabled?: boolean
  field: FormSchemaField
  widgetRegistry: WidgetRegistry
}

export const CollectionWidget: React.FC<CollectionWidgetProps> = ({
  value,
  onChange,
  disabled,
  field,
  widgetRegistry,
}) => {
  const values = Array.isArray(value) ? value : []

  const prototypeEntries = React.useMemo(
    () => Object.entries(field.prototypes ?? {}),
    [field.prototypes],
  )

  const defaultType = prototypeEntries[0]?.[0]
  const [newItemType, setNewItemType] = React.useState<string | undefined>(defaultType)

  React.useEffect(() => {
    if (!newItemType && defaultType) {
      setNewItemType(defaultType)
    }
  }, [defaultType, newItemType])

  const addItem = () => {
    if (!onChange) {
      return
    }

    if (prototypeEntries.length > 0) {
      const type = newItemType ?? defaultType
      if (!type) {
        return
      }

      onChange([...values, { type, configuration: {} }])
      return
    }

    onChange([...values, {}])
  }

  const removeItem = (index: number) => {
    if (!onChange) {
      return
    }

    onChange(values.filter((_, i) => i !== index))
  }

  const updateItem = (index: number, draft: Record<string, any>) => {
    if (!onChange) {
      return
    }

    const nextValues = [...values]
    const current = (nextValues[index] ?? {}) as Record<string, any>
    nextValues[index] = { ...current, ...draft }
    onChange(nextValues)
  }

  const getItemSchema = (item: unknown) => {
    if (field.prototypes && Object.keys(field.prototypes).length > 0) {
      const itemType = typeof item === 'object' && item !== null ? (item as Record<string, any>).type : undefined
      if (typeof itemType === 'string' && field.prototypes[itemType]) {
        return field.prototypes[itemType]
      }

      return field.prototypes[defaultType ?? '']
    }

    return field.prototype
  }

  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
      {values.map((item, index) => {
        const schema = getItemSchema(item)
        const config = schema
          ? {
              ...toFormBuilderConfig(schema, widgetRegistry),
              formProps: { component: false },
            }
          : null

        return (
          <Card
            key={index}
            size="small"
            extra={(
              <Button
                type="text"
                danger
                icon={<DeleteOutlined />}
                onClick={() => removeItem(index)}
                disabled={disabled}
              />
            )}
          >
            {config ? (
              <DynamicForm
                config={config}
                data={(item ?? {}) as Record<string, any>}
                onChange={(draft) => updateItem(index, draft as Record<string, any>)}
              />
            ) : null}
          </Card>
        )
      })}

      <div style={{ display: 'flex', gap: 8 }}>
        {prototypeEntries.length > 1 ? (
          <Select
            style={{ minWidth: 200 }}
            value={newItemType}
            onChange={setNewItemType}
            options={prototypeEntries.map(([type]) => ({ value: type, label: type }))}
            disabled={disabled}
          />
        ) : null}

        <Button
          icon={<PlusOutlined />}
          onClick={addItem}
          disabled={disabled || (prototypeEntries.length > 1 && !newItemType)}
        >
          Add
        </Button>
      </div>
    </div>
  )
}

/**
 * CoreShop IndexBundle - Interpreter Collection Widget
 *
 * Collection widget for InterpreterCollectionType.
 * Each item renders an InterpreterWidget (type select + dynamic config).
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Button, Card, Space, Tag, Typography } from 'antd'
import { DeleteOutlined, PlusOutlined } from '@ant-design/icons'
import type { FormSchemaField } from '@coreshop/studio-form/src/schema-adapter/types'
import { InterpreterWidget } from './InterpreterWidget'

interface InterpreterCollectionWidgetProps {
  value?: Array<Record<string, any>>
  onChange?: (nextValue: Array<Record<string, any>>) => void
  disabled?: boolean
  field: FormSchemaField
}

export const InterpreterCollectionWidget: React.FC<InterpreterCollectionWidgetProps> = ({
  value,
  onChange,
  disabled,
  field,
}) => {
  const items = Array.isArray(value) ? value : []

  // Get the prototype field (the InterpreterType schema) for passing to each InterpreterWidget
  const prototypeField: FormSchemaField | undefined = field.prototype
    ? {
        name: '__proto__',
        blockPrefixes: ['form', 'coreshop_index_column_interpreter'],
        required: false,
        children: field.prototype,
      }
    : undefined

  const addItem = () => {
    onChange?.([...items, {}])
  }

  const removeItem = (index: number) => {
    onChange?.(items.filter((_, i) => i !== index))
  }

  const updateItem = (index: number, next: Record<string, any>) => {
    const nextItems = [...items]
    nextItems[index] = next
    onChange?.(nextItems)
  }

  return (
    <div
      className="coreshop-interpreter-collection"
      style={{ display: 'flex', flexDirection: 'column', gap: 12, paddingBottom: 8 }}
    >
      {items.length === 0 && (
        <div
          style={{
            padding: '10px 12px',
            borderRadius: 8,
            border: '1px dashed var(--ant-color-border)',
            background: 'var(--ant-color-fill-quaternary)',
          }}
        >
          <Typography.Text type="secondary" style={{ fontSize: 12 }}>
            No interpreters configured yet.
          </Typography.Text>
        </div>
      )}

      {items.map((item, index) => (
        <Card
          key={index}
          size="small"
          style={{
            borderColor: 'var(--ant-color-border-secondary)',
            background: 'var(--ant-color-bg-container)',
          }}
          headStyle={{
            minHeight: 40,
            background: 'var(--ant-color-fill-tertiary)',
            borderBottomColor: 'var(--ant-color-border-secondary)',
          }}
          title={(
            <Space size={8}>
              <Typography.Text strong style={{ fontSize: 13 }}>
                Interpreter #{index + 1}
              </Typography.Text>
              {item?.type && (
                <Tag color="default" style={{ marginInlineEnd: 0 }}>
                  {String(item.type)}
                </Tag>
              )}
            </Space>
          )}
          bodyStyle={{ padding: 12 }}
          extra={
            <Button
              type="text"
              danger
              icon={<DeleteOutlined />}
              onClick={() => removeItem(index)}
              disabled={disabled}
            />
          }
        >
          {prototypeField && (
            <InterpreterWidget
              field={prototypeField}
              value={item}
              onChange={(next) => updateItem(index, next)}
              disabled={disabled}
            />
          )}
        </Card>
      ))}

      <Button
        className="coreshop-interpreter-collection__add"
        type="dashed"
        icon={<PlusOutlined />}
        onClick={addItem}
        disabled={disabled}
        style={{
          alignSelf: 'flex-start',
          width: 'auto',
          position: 'static',
          transform: 'none',
          marginTop: 4,
        }}
      >
        Add
      </Button>
    </div>
  )
}

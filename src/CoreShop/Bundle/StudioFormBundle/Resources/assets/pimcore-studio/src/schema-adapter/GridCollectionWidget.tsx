/**
 * CoreShop Schema Adapter - Grid Collection Widget
 *
 * Renders a Symfony CollectionType as an editable table/grid
 * instead of the default card-based layout.
 *
 * Columns are derived from the prototype's field schema.
 * Each cell uses the WidgetRegistry to resolve the appropriate component.
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
import { Table, Button, Popconfirm } from 'antd'
import { DeleteOutlined, PlusOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { FormSchemaField, FormSchemaResponse } from './types'
import type { WidgetRegistry } from './WidgetRegistry'

interface GridCollectionWidgetProps {
  value?: unknown[]
  onChange?: (nextValue: unknown[]) => void
  disabled?: boolean
  field: FormSchemaField
  widgetRegistry: WidgetRegistry
}

export const GridCollectionWidget: React.FC<GridCollectionWidgetProps> = ({
  value,
  onChange,
  disabled,
  field,
  widgetRegistry,
}) => {
  const { t } = useTranslation()
  const values = Array.isArray(value) ? value : []
  const prototype: FormSchemaResponse | undefined = field.prototype

  const updateItem = React.useCallback(
    (index: number, fieldName: string, fieldValue: unknown) => {
      if (!onChange) return
      const next = [...values]
      const current = (next[index] ?? {}) as Record<string, any>
      next[index] = { ...current, [fieldName]: fieldValue }
      onChange(next)
    },
    [values, onChange],
  )

  const removeItem = React.useCallback(
    (index: number) => {
      if (!onChange) return
      onChange(values.filter((_, i) => i !== index))
    },
    [values, onChange],
  )

  const addItem = React.useCallback(() => {
    if (!onChange) return
    onChange([...values, {}])
  }, [values, onChange])

  const columns = React.useMemo(() => {
    if (!prototype?.fields) return []

    const cols = prototype.fields.map((protoField) => {
      const resolved = widgetRegistry.resolve(protoField)
      if (!resolved) return null

      const Component = resolved.component
      const baseProps = resolved.props ?? {}
      const valueProp = resolved.valuePropName ?? 'value'
      const rawLabel = protoField.label ?? protoField.name

      return {
        title: t(rawLabel, { defaultValue: rawLabel }),
        dataIndex: protoField.name,
        key: protoField.name,
        render: (cellValue: unknown, _record: unknown, index: number) => {
          const props: Record<string, any> = {
            ...baseProps,
            [valueProp]: cellValue ?? undefined,
            onChange: (valOrEvent: any) => {
              const newVal =
                valOrEvent?.target !== undefined
                  ? valOrEvent.target.value
                  : valOrEvent
              updateItem(index, protoField.name, newVal)
            },
            disabled,
            style: { width: '100%', ...((baseProps.style as any) ?? {}) },
          }

          return <Component {...props} />
        },
      }
    }).filter(Boolean)

    // Add actions column
    cols.push({
      title: '',
      dataIndex: '__actions',
      key: '__actions',
      width: 60,
      render: (_: unknown, __: unknown, index: number) => (
        <Popconfirm
          title={t('coreshop_delete_confirm', { defaultValue: 'Delete?' })}
          onConfirm={() => removeItem(index)}
          okText={t('coreshop_yes', { defaultValue: 'Yes' })}
          cancelText={t('coreshop_no', { defaultValue: 'No' })}
        >
          <Button type="text" icon={<DeleteOutlined />} danger disabled={disabled} />
        </Popconfirm>
      ),
    } as any)

    return cols
  }, [prototype, widgetRegistry, updateItem, removeItem, disabled, t])

  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
      <Table
        columns={columns}
        dataSource={values}
        pagination={false}
        rowKey={(_, index) => String(index)}
        size="small"
      />
      <div>
        <Button
          icon={<PlusOutlined />}
          onClick={addItem}
          disabled={disabled}
        >
          {t('coreshop_add', { defaultValue: 'Add' })}
        </Button>
      </div>
    </div>
  )
}

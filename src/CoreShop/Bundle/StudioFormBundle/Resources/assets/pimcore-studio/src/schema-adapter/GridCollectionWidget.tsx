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
  value?: unknown[] | Record<string, unknown>
  onChange?: (nextValue: unknown[] | Record<string, unknown>) => void
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
  const isObject = value != null && typeof value === 'object' && !Array.isArray(value)
  const entries: Array<[string, unknown]> = isObject
    ? Object.entries(value as Record<string, unknown>)
    : (Array.isArray(value) ? value : []).map((v, i) => [String(i), v])
  const values = entries.map(([, v]) => v)
  const keys = entries.map(([k]) => k)
  const prototype: FormSchemaResponse | undefined = field.prototype

  const allowAdd = field.extra?.allow_add ?? true
  const allowDelete = field.extra?.allow_delete ?? true

  const emitChange = React.useCallback(
    (nextEntries: Array<[string, unknown]>) => {
      if (!onChange) return
      if (isObject) {
        onChange(Object.fromEntries(nextEntries))
      } else {
        onChange(nextEntries.map(([, v]) => v))
      }
    },
    [onChange, isObject],
  )

  const updateItem = React.useCallback(
    (index: number, fieldName: string, fieldValue: unknown) => {
      const nextEntries = [...entries]
      const current = (nextEntries[index]?.[1] ?? {}) as Record<string, any>
      nextEntries[index] = [keys[index], { ...current, [fieldName]: fieldValue }]
      emitChange(nextEntries)
    },
    [entries, keys, emitChange],
  )

  const removeItem = React.useCallback(
    (index: number) => {
      emitChange(entries.filter((_, i) => i !== index))
    },
    [entries, emitChange],
  )

  const addItem = React.useCallback(() => {
    const nextKey = isObject ? String(Date.now()) : String(entries.length)
    emitChange([...entries, [nextKey, {}]])
  }, [entries, emitChange, isObject])

  const columns = React.useMemo(() => {
    if (!prototype?.fields) return []

    const cols = prototype.fields.map((protoField) => {
      const resolved = widgetRegistry.resolve(protoField)
      if (!resolved) return null

      // Skip hidden fields (e.g. orderItemId) — data is preserved but no column shown
      if (resolved.extra?.hidden) return null

      const Component = resolved.component
      const baseProps = resolved.props ?? {}
      const valueProp = resolved.valuePropName ?? 'value'
      const rawLabel = protoField.label ?? protoField.name

      return {
        title: t(rawLabel, { defaultValue: rawLabel }),
        dataIndex: protoField.name,
        key: protoField.name,
        render: (cellValue: unknown, _record: unknown, index: number) => {
          // Render disabled fields as plain text info
          if (protoField.disabled) {
            return <span>{cellValue != null ? String(cellValue) : ''}</span>
          }

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
    }).filter((col): col is NonNullable<typeof col> => col != null)

    // Add actions column only when deletion is allowed
    if (allowDelete) {
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
    }

    return cols
  }, [prototype, widgetRegistry, updateItem, removeItem, disabled, allowDelete, t])

  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
      <Table
        columns={columns}
        dataSource={values}
        pagination={false}
        rowKey={(_, index) => keys[index!] ?? String(index)}
        size="small"
      />
      {allowAdd && (
        <div>
          <Button
            icon={<PlusOutlined />}
            onClick={addItem}
            disabled={disabled}
          >
            {t('coreshop_add', { defaultValue: 'Add' })}
          </Button>
        </div>
      )}
    </div>
  )
}

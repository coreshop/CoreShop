/**
 * CoreShop IndexBundle Selected Fields Cards
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
import { Card, Space, Tag, Button, Empty } from 'antd'
import { EditOutlined, DeleteOutlined, DragOutlined } from '@ant-design/icons'
import {
  ContentLayout,
  Toolbar,
  IconButton,
  Icon
} from '@pimcore/studio-ui-bundle/components'
import type { IndexColumn } from '../api'

interface SelectedFieldsCardsProps {
  columns: IndexColumn[]
  onChange: (columns: IndexColumn[]) => void
  onEdit: (column: IndexColumn, index: number) => void
  onClearAll?: () => void
}

export const SelectedFieldsCards: React.FC<SelectedFieldsCardsProps> = ({
  columns,
  onChange,
  onEdit,
  onClearAll
}) => {
  const handleDelete = (index: number) => {
    onChange(columns.filter((_, i) => i !== index))
  }

  const content = columns.length === 0 ? (
    <div style={{ padding: 16 }}>
      <Empty
        description="No fields selected"
      >
        <p style={{ color: 'var(--ant-color-text-secondary)', fontSize: 14 }}>
          Click fields from the class definition tree to add them
        </p>
      </Empty>
    </div>
  ) : (
    <div style={{ padding: 16 }}>
      <Space direction="vertical" style={{ width: '100%' }} size="middle">
        {columns.map((column, index) => (
          <Card
            key={index}
            size="small"
            style={{
              borderLeft: '3px solid var(--ant-color-primary)',
              cursor: 'grab'
            }}
            title={
              <Space>
                <DragOutlined style={{ color: 'var(--ant-color-text-tertiary)', cursor: 'grab' }} />
                <span className={`pimcore_icon_${column.dataType || 'data'}`} />
                <strong>{column.name}</strong>
              </Space>
            }
            extra={
              <Space>
                <Button
                  type="text"
                  icon={<EditOutlined />}
                  onClick={() => onEdit(column, index)}
                />
                <Button
                  type="text"
                  danger
                  icon={<DeleteOutlined />}
                  onClick={() => handleDelete(index)}
                />
              </Space>
            }
          >
            <Space direction="vertical" size="small" style={{ width: '100%' }}>
              <Space wrap>
                <Tag color="blue">{column.dataType}</Tag>
                <Tag color="green">{column.columnType}</Tag>
              </Space>

              {column.getter && (
                <div style={{ fontSize: 12, color: 'var(--ant-color-text-secondary)' }}>
                  Getter: <Tag>{column.getter}</Tag>
                </div>
              )}

              {column.interpreter && (
                <div style={{ fontSize: 12, color: 'var(--ant-color-text-secondary)' }}>
                  Interpreter: <Tag>{column.interpreter}</Tag>
                </div>
              )}
            </Space>
          </Card>
        ))}
      </Space>
    </div>
  )

  return (
    <ContentLayout
      renderToolbar={
        columns.length > 0 && onClearAll ? (
          <Toolbar>
            <IconButton icon={{ value: 'trash' }} onClick={onClearAll}>
              Clear All
            </IconButton>
          </Toolbar>
        ) : undefined
      }
    >
      {content}
    </ContentLayout>
  )
}

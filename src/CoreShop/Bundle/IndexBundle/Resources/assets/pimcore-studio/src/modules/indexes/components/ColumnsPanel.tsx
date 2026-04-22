/**
 * CoreShop IndexBundle Columns Panel
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
import { SplitLayout, useMessage, type DragAndDropInfo } from '@pimcore/studio-ui-bundle/components'
import type { Index, IndexConfig, IndexColumn, ClassDefinitionResponse } from '../api'
import { indexApi } from '../api'
import { ClassDefinitionTree } from './ClassDefinitionTree'
import { SelectedFieldsTree } from './SelectedFieldsTree'
import { FieldEditModal } from './FieldEditModal'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'

interface ColumnsPanelProps {
  index: Index
  config: IndexConfig
  onChange: (index: Index) => void
}

export const ColumnsPanel: React.FC<ColumnsPanelProps> = ({
  index,
  config,
  onChange
}) => {
  const messageApi = useMessage()
  const [classDefinition, setClassDefinition] = React.useState<ClassDefinitionResponse | null>(null)
  const [loading, setLoading] = React.useState(false)
  const [dialogVisible, setDialogVisible] = React.useState(false)
  const [editingColumn, setEditingColumn] = React.useState<IndexColumn | null>(null)
  const [editingIndex, setEditingIndex] = React.useState<number | null>(null)

  const columns = index.columns || []

  // Load class definition when class changes
  React.useEffect(() => {
    if (!index.class) {
      setClassDefinition(null)
      return
    }

    setLoading(true)
    indexApi.getClassDefinition(index.class)
      .then(setClassDefinition)
      .catch(err => {
        void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load class definition')))
      })
      .finally(() => {
        setLoading(false)
      })
  }, [index.class])

  const handleAddField = (field: Partial<IndexColumn>) => {
    // Check if field already exists
    const exists = columns.some(col =>
      col.objectKey === field.objectKey &&
      col.objectType === field.objectType
    )

    if (exists) {
      void messageApi.warning(`Field "${field.objectKey}" already added`)
      return
    }

    // Create new column with defaults
    const newColumn: IndexColumn = {
      name: field.name || field.objectKey || '',
      objectKey: field.objectKey || '',
      objectType: field.objectType,
      dataType: field.dataType,
      columnType: field.dataType || 'TEXT',
      getter: field.getter,
      getterConfig: field.configuration,
      interpreter: field.interpreter,
      interpreterConfig: undefined,
      configuration: field.configuration
    }

    onChange({
      ...index,
      columns: [...columns, newColumn]
    })
  }

  const handleDrop = (info: DragAndDropInfo) => {
    if (info.type === 'coreshop-index-field' && info.data) {
      handleAddField(info.data as Partial<IndexColumn>)
    }
  }

  const handleEdit = (column: IndexColumn, columnIndex: number) => {
    setEditingColumn(column)
    setEditingIndex(columnIndex)
    setDialogVisible(true)
  }

  const handleSaveField = (updatedColumn: IndexColumn) => {
    if (editingIndex !== null) {
      const newColumns = [...columns]
      newColumns[editingIndex] = updatedColumn
      onChange({
        ...index,
        columns: newColumns
      })
    }
  }

  const handleCloseModal = () => {
    setDialogVisible(false)
    setEditingColumn(null)
    setEditingIndex(null)
  }

  const handleColumnsChange = (newColumns: IndexColumn[]) => {
    onChange({
      ...index,
      columns: newColumns
    })
  }

  if (!index.class) {
    return (
      <div style={{
        padding: 48,
        textAlign: 'center',
        color: 'var(--ant-color-text-tertiary)'
      }}>
        Please select a class in the Settings tab first
      </div>
    )
  }

  const leftItem = {
    id: 'class-definition',
    size: 30,
    minSize: 300,
    children: [
      <div
        key='class-definition-tree-wrapper'
        style={{
          height: '100%',
          minHeight: 0,
          display: 'flex',
          flexDirection: 'column',
          overflow: 'hidden'
        }}
      >
        <ClassDefinitionTree
          key='class-definition-tree'
          classDefinition={classDefinition}
          loading={loading}
          onAddField={handleAddField}
        />
      </div>
    ]
  }

  const rightItem = {
    id: 'selected-fields',
    size: 70,
    minSize: 400,
    children: [
      <div
        key='selected-fields-tree-wrapper'
        style={{
          height: '100%',
          minHeight: 0,
          display: 'flex',
          flexDirection: 'column',
          overflow: 'hidden'
        }}
      >
        <SelectedFieldsTree
          key='selected-fields-tree'
          columns={columns}
          onChange={handleColumnsChange}
          onEdit={handleEdit}
          onDrop={handleDrop}
        />
      </div>
    ]
  }

  return (
    <div style={{ height: '100%', minHeight: 0, display: 'flex', flexDirection: 'column' }}>
      <div style={{ flex: 1, minHeight: 0, display: 'flex', overflow: 'hidden' }}>
        <SplitLayout
          leftItem={leftItem}
          rightItem={rightItem}
          withDivider
        />
      </div>

      <FieldEditModal
        open={dialogVisible}
        field={editingColumn}
        config={config}
        onClose={handleCloseModal}
        onSave={handleSaveField}
      />
    </div>
  )
}

/**
 * CoreShop IndexBundle Selected Fields Tree
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
import { Empty, Typography } from 'antd'
import { createStyles } from 'antd-style'
import { useTranslation } from 'react-i18next'
import {
  Icon,
  Droppable,
  TreeElement,
  type TreeDataItem,
  type DragAndDropInfo
} from '@pimcore/studio-ui-bundle/components'
import type { IndexColumn } from '../api'
import { getIconForFieldType } from './field-type-icons'

const { Title } = Typography

interface NodeMeta {
  index: number
}

interface SelectedFieldsTreeProps {
  columns: IndexColumn[]
  onChange: (columns: IndexColumn[]) => void
  onEdit: (column: IndexColumn, index: number) => void
  onDrop?: (info: DragAndDropInfo) => void
}

export const SelectedFieldsTree: React.FC<SelectedFieldsTreeProps> = ({
  columns,
  onChange,
  onEdit,
  onDrop
}) => {
  const { t } = useTranslation()
  const { styles } = useSelectedFieldsTreeStyles()

  const handleDelete = (index: number): void => {
    onChange(columns.filter((_, i) => i !== index))
  }

  const treeData = React.useMemo<TreeDataItem[]>(() => {
    if (columns.length === 0) return []

    return [{
      key: 'root',
      title: `${t('coreshop_indexes_selected_fields')} (${columns.length})`,
      icon: <Icon value='folder' />,
      children: columns.map((column, index) => ({
        key: `field-${index}`,
        title: `${column.name} (${column.columnType})`,
        icon: <Icon value={ getIconForFieldType(column.dataType ?? 'data') } />,
        isLeaf: true,
        actions: [
          { key: 'edit', icon: 'edit', translationKey: 'coreshop_indexes_field_edit' },
          { key: 'delete', icon: 'trash' }
        ],
        meta: { index } satisfies NodeMeta
      }))
    }]
  }, [columns, t])

  // TreeElement has no double-click hook, so the title is wrapped for that alone
  const renderTitle = (node: any, initialComponent: React.ReactElement): React.ReactNode => {
    const index = (node.meta as NodeMeta | undefined)?.index

    if (index === undefined) return initialComponent

    return (
      <span onDoubleClick={ () => { onEdit(columns[index], index) } }>
        {initialComponent}
      </span>
    )
  }

  const content = treeData.length === 0 ? (
    <div className={styles.stateContainer}>
      <Empty description={t('coreshop_indexes_no_fields_selected')} />
      <p className={styles.hint}>
        {t('coreshop_indexes_drag_fields_hint')}
      </p>
    </div>
  ) : (
    <div className={styles.scrollArea}>
      <TreeElement
        defaultExpandAll
        onActionsClick={ (key, action, node) => {
          const index = (node as any).meta?.index as number | undefined
          if (index === undefined) return
          if (action === 'edit') onEdit(columns[index], index)
          if (action === 'delete') handleDelete(index)
        } }
        titleRender={ renderTitle }
        treeData={ treeData }
      />
    </div>
  )

  const wrappedContent = onDrop !== undefined ? (
    <Droppable
      className={styles.droppable}
      isValidContext={(info) => info.type === 'coreshop-index-field'}
      onDrop={onDrop}
    >
      <div className={styles.body}>
        {content}
      </div>
    </Droppable>
  ) : (
    <div className={styles.body}>
      {content}
    </div>
  )

  return (
    <div className={styles.root}>
      <div className={styles.header}>
        <Title level={5} className={styles.title}>
          {t('coreshop_indexes_selected_fields')} ({columns.length})
        </Title>
      </div>
      {wrappedContent}
    </div>
  )
}

const useSelectedFieldsTreeStyles = createStyles(({ css, token }) => ({
  root: css`
    height: 100%;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: ${token.colorBgElevated};
  `,
  header: css`
    padding: 12px 16px;
    background: ${token.colorBgContainer};
    border-bottom: 1px solid ${token.colorBorderSecondary};
  `,
  title: css`
    margin: 0 !important;
    font-size: ${token.fontSize}px;
    font-weight: ${token.fontWeightStrong};
    color: ${token.colorText};
  `,
  droppable: css`
    flex: 1 1 auto;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  `,
  body: css`
    flex: 1 1 auto;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  `,
  stateContainer: css`
    flex: 1 1 auto;
    min-height: 0;
    overflow: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    align-items: center;
    justify-content: center;
    text-align: center;
  `,
  hint: css`
    color: ${token.colorTextSecondary};
    font-size: ${token.fontSizeSM}px;
    margin: 0;
  `,
  scrollArea: css`
    flex: 1 1 auto;
    min-height: 0;
    overflow: auto;
    padding: ${token.paddingXS}px 0;
  `
}))

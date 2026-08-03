/**
 * CoreShop IndexBundle Class Definition Tree
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
import { Empty, Skeleton, Typography } from 'antd'
import { createStyles } from 'antd-style'
import { useTranslation } from 'react-i18next'
import {
  Draggable,
  Icon,
  TreeElement,
  type TreeDataItem,
  type DragAndDropInfo
} from '@pimcore/studio-ui-bundle/components'
import type { ClassDefinitionResponse, ClassDefinitionField, IndexColumn } from '../api'
import { getIconForFieldType } from './field-type-icons'

const { Title } = Typography

interface NodeMeta {
  fieldtype: string
  iconName: string
  label: string
  data: Partial<IndexColumn> | null
}

interface ClassDefinitionTreeProps {
  classDefinition: ClassDefinitionResponse | null
  loading: boolean
  onAddField: (field: Partial<IndexColumn>) => void
}

export const ClassDefinitionTree: React.FC<ClassDefinitionTreeProps> = ({
  classDefinition,
  loading,
  onAddField
}) => {
  const { t } = useTranslation()
  const { styles } = useTreeLayoutStyles()

  const buildNode = (field: ClassDefinitionField, key: string): TreeDataItem | null => {
    const nodeLabel = field.nodeLabel ?? field.name ?? key
    const fieldtype = field.fieldtype ?? field.nodeType ?? 'folder'
    const isLayout = fieldtype === 'layout' || fieldtype === 'panel' || !field.name

    const children = Array.isArray(field.childs)
      ? field.childs.map((child, index) => buildNode(child, `${key}-${index}`)).filter(Boolean) as TreeDataItem[]
      : undefined

    const iconName = getIconForFieldType(fieldtype)

    const fieldData = isLayout ? null : {
      name: field.name ?? nodeLabel,
      objectKey: field.name ?? '',
      objectType: field.nodeType ?? 'object',
      dataType: field.fieldtype ?? 'input',
      getter: field.getter,
      interpreter: field.interpreter,
      configuration: field.configuration
    }

    return {
      key: `${key}-${field.name ?? nodeLabel}`,
      title: isLayout ? nodeLabel : `${nodeLabel} (${fieldtype})`,
      icon: <Icon value={ iconName } />,
      // Pimcore's Draggable needs this class to keep rows at 24px
      className: isLayout ? undefined : 'ant-tree-node--has-drag-and-drop',
      isLeaf: children === undefined || children.length === 0,
      children,
      actions: fieldData !== null ? [{ key: 'add', icon: 'new', translationKey: 'coreshop_indexes_field_add' }] : undefined,
      meta: { fieldtype, iconName, label: nodeLabel, data: fieldData } satisfies NodeMeta
    }
  }

  const treeData = React.useMemo<TreeDataItem[]>(() => {
    if (classDefinition === null) return []

    const nodes: TreeDataItem[] = []
    Object.keys(classDefinition).forEach(key => {
      const section = classDefinition[key]
      if (section === undefined || section === null) return

      const node = buildNode(section, key)
      if (node !== null) {
        nodes.push(node)
      }
    })

    return nodes
  }, [classDefinition])

  const renderTitle = (node: any, initialComponent: React.ReactElement): React.ReactNode => {
    const meta = node.meta as NodeMeta | undefined

    if (meta?.data == null) {
      return initialComponent
    }

    const fieldData = meta.data

    return (
      <Draggable
        info={ {
          type: 'coreshop-index-field',
          title: meta.label,
          icon: { value: meta.iconName },
          data: fieldData
        } as DragAndDropInfo }
      >
        <span onDoubleClick={ () => { onAddField(fieldData) } }>
          {initialComponent}
        </span>
      </Draggable>
    )
  }

  return (
    <div className={styles.root}>
      <div className={styles.header}>
        <Title level={5} className={styles.title}>
          {t('coreshop_indexes_class_definitions')}
        </Title>
      </div>
      <div className={styles.content}>
        {loading ? (
          <div className={styles.stateContainer}>
            <Skeleton active title={false} paragraph={{ rows: 8 }} />
          </div>
        ) : treeData.length === 0 ? (
          <div className={styles.stateContainer}>
            <Empty description={t('coreshop_indexes_no_class_definition')} />
          </div>
        ) : (
          <div className={styles.scrollArea}>
            <TreeElement
              defaultExpandAll
              onActionsClick={ (key, action, node) => {
                const meta = (node as any).meta as NodeMeta | undefined
                if (action === 'add' && meta?.data != null) onAddField(meta.data)
              } }
              onSelected={ () => {} }
              titleRender={ renderTitle }
              treeData={ treeData }
            />
          </div>
        )}
      </div>
    </div>
  )
}

const useTreeLayoutStyles = createStyles(({ css, token }) => ({
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
  content: css`
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
  `,
  // Draggable renders a block-level div inside .ant-tree-title — the fixed button height keeps
  // the label on the icon's line box (same fix Pimcore applies to its own drag wrappers)
  scrollArea: css`
    flex: 1 1 auto;
    min-height: 0;
    overflow: auto;
    padding: ${token.paddingXS}px 0;

    .ant-tree-title__btn {
      height: 24px;
    }
  `
}))

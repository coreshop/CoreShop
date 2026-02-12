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
import { Tree, Empty, Skeleton, Typography } from 'antd'
import { createStyles } from 'antd-style'
import type { DataNode as AntDataNode } from 'antd/es/tree'

interface DataNode extends AntDataNode {
  data?: Record<string, any> | null
}
import {
  Draggable,
  Icon,
  type DragAndDropInfo
} from '@pimcore/studio-ui-bundle/components'
import type { ClassDefinitionResponse, ClassDefinitionField, IndexColumn } from '../api'

const { Title } = Typography

/**
 * Maps Pimcore and CoreShop field types to icon names
 * Uses only icons that are actually available in Pimcore's icon library
 */
const getIconForFieldType = (fieldType: string): string => {
  const icons: Record<string, string> = {
    // Basic Pimcore types
    'input': 'text-field',
    'textarea': 'long-text',
    'wysiwyg': 'wysiwyg-field',
    'checkbox': 'checkbox',
    'numeric': 'number-field',
    'number': 'number-field',
    'select': 'chevron-down',
    'multiselect': 'multi-select',
    'date': 'calendar',
    'datetime': 'calendar',
    'time': 'date-time-field',

    // Media
    'image': 'image',
    'hotspotimage': 'image',
    'advancedImage': 'image',
    'video': 'video',
    'asset': 'asset',

    // Relations
    'manyToOneRelation': 'data-object',
    'advancedManyToOneRelation': 'data-object',
    'manyToManyRelation': 'many-to-many',
    'advancedManyToManyRelation': 'many-to-many',
    'manyToManyObjectRelation': 'data-object',
    'reverseObjectRelation': 'data-object',
    'object': 'data-object',
    'objects': 'data-object',

    // Links
    'href': 'many-to-many',
    'multihref': 'many-to-many',
    'urlSlug': 'many-to-many',

    // Structure
    'folder': 'folder',
    'panel': 'layout',
    'layout': 'layout',
    'fieldcollections': 'collection',
    'localizedfields': 'country-select',
    'block': 'collection',
    'table': 'columns',
    'structuredTable': 'columns',

    // Localization
    'country': 'country-select',
    'countries': 'country-select',
    'language': 'country-select',
    'languages': 'country-select',

    // Special fields
    'currency': 'coreshop_icon_currency',
    'quantityValue': 'number-field',
    'inputQuantityValue': 'number-field',
    'calculatedValue': 'calculator',
    'data': 'widget',

    // CoreShop field types
    'coreShopRelation': 'many-to-many',
    'coreShopRelations': 'many-to-many',
    'coreShopMoney': 'coreshop_icon_currency',
    'coreShopMoneyCurrency': 'coreshop_icon_currency',
    'coreShopCurrency': 'coreshop_icon_currency',
    'coreShopCurrencyMultiselect': 'coreshop_icon_currency',
    'coreShopProductSpecificPriceRules': 'coreshop_icon_currency',
    'coreShopProductUnitDefinitions': 'number-field',
    'coreShopProductQuantityPriceRules': 'coreshop_icon_currency',
    'coreShopStoreValues': 'coreshop_store',
    'coreShopStore': 'coreshop_store',
    'coreShopQuantityValue': 'number-field',
    'coreShopQuantityPrice': 'coreshop_icon_currency',
    'coreShopSeo': 'seo',
    'coreShopPaymentProvider': 'coreshop_icon_payment_provider',
    'coreShopPaymentProviderMultiselect': 'coreshop_icon_payment_provider',
    'coreShopCarrier': 'coreshop_carriers',
    'coreShopCarrierMultiselect': 'coreshop_carriers',
    'coreShopShippingRule': 'coreshop_shipping',
    'coreShopTaxRate': 'coreshop_icon_currency',
    'coreShopTaxRuleGroup': 'coreshop_icon_currency',
    'coreShopCountry': 'country-select',
    'coreShopCountryMultiselect': 'country-select',
    'coreShopState': 'country-select',
    'coreShopAddressIdentifier': 'widget',
    'coreShopSuperBoxSelect': 'chevron-down',
    'coreShopItemSelector': 'multi-select',
    'coreShopDynamicDropdown': 'chevron-down',
    'coreShopDynamicDropdownMultiple': 'multi-select',
    'coreShopSerializedData': 'widget'
  }

  return icons[fieldType] || 'widget'
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
  const { styles } = useTreeLayoutStyles()

  const buildTreeData = (): DataNode[] => {
    if (!classDefinition) return []

    const nodes: DataNode[] = []

    // Build tree from class definition response
    Object.keys(classDefinition).forEach(key => {
      const section = classDefinition[key]
      if (!section) return

      const node = buildNode(section, key)
      if (node) {
        nodes.push(node)
      }
    })

    return nodes
  }

  const buildNode = (field: ClassDefinitionField, key: string): DataNode | null => {
    const nodeLabel = field.nodeLabel || field.name || key
    const fieldtype = field.fieldtype || field.nodeType || 'folder'
    const isLayout = fieldtype === 'layout' || fieldtype === 'panel' || !field.name

    // Build children if exist
    const children = field.childs && Array.isArray(field.childs)
      ? field.childs.map((child, index) => buildNode(child, `${key}-${index}`)).filter(Boolean) as DataNode[]
      : undefined

    const iconName = getIconForFieldType(fieldtype)

    const fieldData = isLayout ? null : {
      name: field.name || nodeLabel,
      objectKey: field.name || '',
      objectType: field.nodeType || 'object',
      dataType: field.fieldtype || 'input',
      getter: field.getter,
      interpreter: field.interpreter,
      configuration: field.configuration
    }

    // Make field items draggable with icon and type info
    const title = isLayout ? (
      <><Icon value={iconName} /> <span>{nodeLabel}</span></>
    ) : (
      <Draggable
        info={{
          type: 'coreshop-index-field',
          title: nodeLabel,
          icon: { value: iconName },
          data: fieldData
        } as DragAndDropInfo}
      >
        <><Icon value={iconName} /> <span>{nodeLabel}</span> <span style={{
          fontSize: 11,
          color: 'var(--ant-color-text-tertiary)',
          fontFamily: 'monospace'
        }}>
          {fieldtype}
        </span></>
      </Draggable>
    )

    return {
      key: `${key}-${field.name || nodeLabel}`,
      title,
      isLeaf: !children || children.length === 0,
      children,
      selectable: !isLayout,
      // Store field data for click handler
      data: fieldData
    }
  }

  const handleSelect = (selectedKeys: React.Key[], info: any) => {
    // Single click does nothing, only double click adds
  }

  const handleDoubleClick = (e: React.MouseEvent, node: any) => {
    if (node.data) {
      onAddField(node.data)
    }
  }

  const treeData = buildTreeData()

  return (
    <div className={styles.root}>
      <div className={styles.header}>
        <Title level={5} className={styles.title}>Class Definitions</Title>
      </div>
      <div className={styles.content}>
        {loading ? (
          <div className={styles.stateContainer}>
            <Skeleton active title={false} paragraph={{ rows: 8 }} />
          </div>
        ) : treeData.length === 0 ? (
          <div className={styles.stateContainer}>
            <Empty description="No class definition available" />
          </div>
        ) : (
          <div className={styles.scrollArea}>
            <Tree
              style={{ minHeight: '100%' }}
              showIcon
              showLine={false}
              defaultExpandAll
              treeData={treeData}
              onSelect={handleSelect}
              onDoubleClick={handleDoubleClick}
              virtual={false}
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
    font-size: 12px;
    font-weight: 600;
    color: ${token.colorTextSecondary};
    text-transform: uppercase;
    letter-spacing: 0.5px;
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
  scrollArea: css`
    flex: 1 1 auto;
    min-height: 0;
    overflow: auto;
    padding: 8px;

    .ant-tree {
      background: transparent;
      font-size: 13px;

      .ant-tree-treenode {
        padding: 1px 0;

        &:hover {
          .ant-tree-node-content-wrapper {
            background-color: ${token.colorFillQuaternary};
          }
        }
      }

      .ant-tree-node-content-wrapper {
        border-radius: 4px;
        transition: all 0.2s;
        padding: 3px 6px;
        line-height: 20px;

        &:hover {
          background-color: ${token.colorFillQuaternary};
        }

        &.ant-tree-node-selected {
          background-color: ${token.colorPrimaryBg};
        }
      }

      .ant-tree-iconEle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
      }

      .ant-tree-title {
        display: inline-flex;
        align-items: center;
        width: 100%;
      }

      .ant-tree-switcher {
        width: 16px;
        line-height: 20px;
      }
    }
  `
}))

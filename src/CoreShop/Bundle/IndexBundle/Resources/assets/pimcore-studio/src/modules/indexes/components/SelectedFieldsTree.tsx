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
import { Tree, Empty, Space, Typography, Dropdown } from 'antd'
import { createStyles } from 'antd-style'
import type { DataNode } from 'antd/es/tree'
import { EditOutlined, DeleteOutlined } from '@ant-design/icons'
import {
  Icon,
  Droppable,
  type DragAndDropInfo
} from '@pimcore/studio-ui-bundle/components'
import type { IndexColumn } from '../api'

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
  const { styles } = useSelectedFieldsTreeStyles()

  const handleDelete = (index: number) => {
    onChange(columns.filter((_, i) => i !== index))
  }

  const handleDoubleClick = (e: React.MouseEvent, node: any) => {
    // Find the column index from the key
    const keyMatch = node.key?.toString().match(/^field-(\d+)$/)
    if (keyMatch) {
      const index = parseInt(keyMatch[1], 10)
      const column = columns[index]
      if (column) {
        onEdit(column, index)
      }
    }
  }

  const buildTreeData = (): DataNode[] => {
    if (columns.length === 0) return []

    return [{
      key: 'root',
      title: (
        <Space>
          <Icon value='folder' />
          <span>Selected Fields ({columns.length})</span>
        </Space>
      ),
      selectable: false,
      children: columns.map((column, index) => {
        const iconName = getIconForFieldType(column.dataType || 'data')
        return {
          key: `field-${index}`,
          title: (
            <Dropdown
              trigger={['contextMenu']}
              menu={{
                items: [
                  {
                    key: 'edit',
                    icon: <EditOutlined />,
                    label: 'Edit',
                    onClick: () => onEdit(column, index)
                  },
                  {
                    key: 'delete',
                    icon: <DeleteOutlined />,
                    label: 'Delete',
                    danger: true,
                    onClick: () => handleDelete(index)
                  }
                ]
              }}
            >
              <div style={{ display: 'flex', alignItems: 'center', gap: '6px', width: '100%' }}>
                <Icon value={iconName} />
                <span>{column.name}</span>
                <span style={{ fontSize: 11, color: 'var(--ant-color-text-tertiary)' }}>
                  ({column.columnType})
                </span>
              </div>
            </Dropdown>
          ),
          isLeaf: true
        }
      })
    }]
  }

  const treeData = buildTreeData()

  const content = treeData.length === 0 ? (
    <div className={styles.stateContainer}>
      <Empty description="No fields selected" />
      <p className={styles.hint}>
        Drag fields from the class definition tree to add them
      </p>
    </div>
  ) : (
    <div className={styles.scrollArea}>
      <Tree
        style={{ minHeight: '100%' }}
        showIcon
        showLine={false}
        defaultExpandAll
        treeData={treeData}
        selectable={false}
        virtual={false}
        onDoubleClick={handleDoubleClick}
      />
    </div>
  )

  const wrappedContent = onDrop ? (
    <Droppable
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
          Selected Fields ({columns.length})
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
    font-size: 12px;
    font-weight: 600;
    color: ${token.colorTextSecondary};
    text-transform: uppercase;
    letter-spacing: 0.5px;
  `,
  body: css`
    flex: 1 1 auto;
    min-height: 300px;
    display: flex;
    flex-direction: column;
  `,
  stateContainer: css`
    flex: 1 1 auto;
    min-height: 300px;
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
    color: var(--ant-color-text-secondary);
    font-size: 12px;
    margin: 0;
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
      }

      .ant-tree-node-content-wrapper {
        border-radius: 4px;
        transition: all 0.2s;
        padding: 3px 6px;
        line-height: 20px;

        &:hover {
          background-color: ${token.colorFillQuaternary};
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

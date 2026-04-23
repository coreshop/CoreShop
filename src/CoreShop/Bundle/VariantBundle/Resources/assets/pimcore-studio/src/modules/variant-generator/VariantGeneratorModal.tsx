/**
 * CoreShop Variant Generator Modal
 *
 * Modal with a tree of attribute groups and checkable attribute nodes.
 * The "Apply" button is only enabled when ALL groups have at least one checked attribute.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Modal, Tree, Spin, Alert } from 'antd'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { renderApiError } from '@coreshop/resource/src/entities'
import { useTranslation } from 'react-i18next'
import { variantGeneratorApi, type AttributeNode } from './api'

interface TreeDataNode {
  key: string
  title: string
  isLeaf: boolean
  checkable: boolean
  children?: TreeDataNode[]
  groupId?: number
  attributeId?: number
}

interface Props {
  open: boolean
  objectId: number
  onClose: () => void
}

export const VariantGeneratorModal: React.FC<Props> = ({ open, objectId, onClose }) => {
  const { t } = useTranslation()
  const messageApi = useMessage()

  const [loading, setLoading] = React.useState(false)
  const [submitting, setSubmitting] = React.useState(false)
  const [treeData, setTreeData] = React.useState<TreeDataNode[]>([])
  const [checkedKeys, setCheckedKeys] = React.useState<React.Key[]>([])
  const [allValid, setAllValid] = React.useState(false)
  const [error, setError] = React.useState<string | null>(null)

  // Track group IDs to validate all groups have selections
  const groupIdsRef = React.useRef<Set<number>>(new Set())
  // Map attribute keys to their group IDs
  const attributeGroupMapRef = React.useRef<Map<string, number>>(new Map())

  React.useEffect(() => {
    if (open && objectId) {
      void loadAttributes()
    }
    if (!open) {
      setTreeData([])
      setCheckedKeys([])
      setAllValid(false)
      setError(null)
      groupIdsRef.current.clear()
      attributeGroupMapRef.current.clear()
    }
  }, [open, objectId])

  const loadAttributes = async (): Promise<void> => {
    setLoading(true)
    setError(null)
    try {
      const response = await variantGeneratorApi.getAttributes(objectId)
      if (!response.success || !response.data) {
        setError('Failed to load attributes')
        return
      }

      const groups = new Set<number>()
      const attrGroupMap = new Map<string, number>()

      const nodes: TreeDataNode[] = response.data.map((group: AttributeNode, groupIndex: number) => {
        const groupKey = `group-${groupIndex}`

        // Extract group ID from children
        let groupId = groupIndex
        const children: TreeDataNode[] = (group.data ?? []).map((attr: AttributeNode) => {
          if (attr.group_id !== undefined) {
            groupId = attr.group_id
          }
          const attrKey = `attr-${attr.id}`
          attrGroupMap.set(attrKey, attr.group_id ?? groupId)
          return {
            key: attrKey,
            title: attr.text,
            isLeaf: true,
            checkable: true,
            attributeId: attr.id,
            groupId: attr.group_id ?? groupId
          }
        })

        groups.add(groupId)

        return {
          key: groupKey,
          title: group.text,
          isLeaf: false,
          checkable: false,
          children
        }
      })

      groupIdsRef.current = groups
      attributeGroupMapRef.current = attrGroupMap
      setTreeData(nodes)
    } catch (err) {
      setError('Failed to load attributes')
    } finally {
      setLoading(false)
    }
  }

  const handleCheck = (
    checked: React.Key[] | { checked: React.Key[], halfChecked: React.Key[] }
  ): void => {
    const keys = Array.isArray(checked) ? checked : checked.checked
    setCheckedKeys(keys)

    // Validate: every group must have at least one checked attribute
    const checkedGroups = new Set<number>()
    for (const key of keys) {
      const groupId = attributeGroupMapRef.current.get(String(key))
      if (groupId !== undefined) {
        checkedGroups.add(groupId)
      }
    }

    const valid = groupIdsRef.current.size > 0 &&
      [...groupIdsRef.current].every(gid => checkedGroups.has(gid))
    setAllValid(valid)
  }

  const handleApply = async (): Promise<void> => {
    if (!allValid) return

    // Build grouped attributes: { groupId: [attributeId, ...], ... }
    const groupedAttributes: Record<number, number[]> = {}
    for (const key of checkedKeys) {
      const keyStr = String(key)
      const groupId = attributeGroupMapRef.current.get(keyStr)
      if (groupId === undefined) continue
      // Extract attribute ID from key (format: "attr-123")
      const attrId = parseInt(keyStr.replace('attr-', ''), 10)
      if (isNaN(attrId)) continue

      if (!groupedAttributes[groupId]) {
        groupedAttributes[groupId] = []
      }
      groupedAttributes[groupId].push(attrId)
    }

    setSubmitting(true)
    try {
      const response = await variantGeneratorApi.generateVariants(objectId, groupedAttributes)
      if (response.success) {
        void messageApi.success(response.message)
        onClose()
      } else {
        void messageApi.error(renderApiError(response.message))
      }
    } catch (err) {
      void messageApi.error(renderApiError('Failed to generate variants'))
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <Modal
      title={t('coreshop.variant_generator.generate', { defaultValue: 'Generate Variants' })}
      open={open}
      onCancel={onClose}
      onOk={handleApply}
      okText={t('apply', { defaultValue: 'Apply' })}
      cancelText={t('close', { defaultValue: 'Close' })}
      okButtonProps={{ disabled: !allValid, loading: submitting }}
      width={600}
      styles={{ body: { minHeight: 300, maxHeight: 500, overflow: 'auto' } }}
    >
      {loading && (
        <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: 200 }}>
          <Spin size="large" />
        </div>
      )}

      {error && (
        <Alert type="error" message={error} style={{ marginBottom: 16 }} />
      )}

      {!loading && !error && treeData.length > 0 && (
        <>
          {!allValid && checkedKeys.length > 0 && (
            <Alert
              type="warning"
              message={t('coreshop.variant_generator.select_all_groups', {
                defaultValue: 'Please select at least one attribute from each group.'
              })}
              style={{ marginBottom: 16 }}
            />
          )}
          <Tree
            checkable
            checkStrictly
            defaultExpandAll
            treeData={treeData}
            checkedKeys={checkedKeys}
            onCheck={handleCheck}
          />
        </>
      )}

      {!loading && !error && treeData.length === 0 && (
        <Alert
          type="info"
          message={t('coreshop.variant_generator.no_attributes', {
            defaultValue: 'No attribute groups found for this product.'
          })}
        />
      )}
    </Modal>
  )
}

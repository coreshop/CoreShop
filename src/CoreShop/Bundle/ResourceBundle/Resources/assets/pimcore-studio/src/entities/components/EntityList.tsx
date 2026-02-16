import React from 'react'
import { useTranslation } from 'react-i18next'
import { Tree, Skeleton, Tag } from 'antd'
import type { DataNode } from 'antd/es/tree'
import {
  ContentLayout,
  Dropdown,
  DropdownButton,
  Icon,
  IconButton,
  Toolbar as PimToolbar,
  Draggable,
  type DragAndDropInfo
} from '@pimcore/studio-ui-bundle/components'
import type { EntityListItem } from '../types'
import { DroppableEntity } from './dnd/DroppableEntity'
import { useEntityListStyles } from './entity-list.styles'

export interface GroupItem { id: number, name: string }

export interface EntityListProps {
  items: EntityListItem[]
  loading?: boolean
  groups?: GroupItem[]
  rootTitle?: string
  addLabel?: string
  leafIcon?: string
  resolveGroupId?: (item: EntityListItem, groups: GroupItem[]) => number | null | undefined
  onReload: () => void
  onAdd: (groupId?: number) => void
  onDelete: (id: number) => void
  onSelect: (id: number) => void
  onMove?: (id: number, targetGroupId: number | null) => void
  buildDragInfo?: (item: EntityListItem) => DragAndDropInfo | null
  dragType?: string
}

export const EntityList: React.FC<EntityListProps> = ({
  items,
  groups,
  loading,
  rootTitle,
  addLabel,
  leafIcon = 'widget-default',
  resolveGroupId,
  onReload,
  onAdd,
  onDelete,
  onSelect,
  onMove,
  buildDragInfo,
  dragType,
}) => {
  const { t } = useTranslation()
  const { styles } = useEntityListStyles()

  const leafRow = (it: EntityListItem): React.ReactNode => {
    const inactive = it.active === false

    const base = (
      <Dropdown
        trigger={ ['contextMenu'] }
        menu={ { items: [
          { key: 'delete', icon: <Icon value='trash' />, label: t('toolbar.delete', { defaultValue: 'Delete' }), onClick: () => onDelete(it.id) }
        ] } }
      >
          <span className={`${styles.leafNode} ${inactive ? styles.inactive : ''}`}>
            <span className={styles.leafIcon}><Icon value={leafIcon} /></span>
            {it.name}
            {inactive && <Tag className={styles.inactiveTag}>{t('entity.inactive', { defaultValue: 'Inactive' })}</Tag>}
          </span>
      </Dropdown>
    )
    return buildDragInfo ? (
      <Draggable info={ buildDragInfo(it) as DragAndDropInfo }>
        {base}
      </Draggable>
    ) : base
  }

  const groupHeader = (g: GroupItem | { id: null, name: string }, count: number): React.ReactNode => {
    const content = (
      <span className={styles.groupNode}>
        <Icon value='folder' />
        <span>{g.name}</span>
        <span className={styles.groupCount}>({count})</span>
      </span>
    )
    if (!dragType || !onMove) return content
    return (
      <DroppableEntity
        className={ styles.droppableInline }
        accept={ dragType }
        isValidData={ (info) => typeof info?.data?.id === 'number' }
        onDrop={ (info) => { const id = info?.data?.id; if (typeof id === 'number') onMove(id, (g.id === null ? null : g.id)) } }
      >
        {content}
      </DroppableEntity>
    )
  }

  const buildTreeData = React.useMemo<DataNode[]>(() => {
    const buildLeafNode = (it: EntityListItem): DataNode => ({
      key: it.id,
      title: leafRow(it),
      isLeaf: true,
    })

    if (!groups || groups.length === 0 || !resolveGroupId) {
      return [{
        key: 'root',
        title: (
          <span className={styles.groupNode}>
            <Icon value='folder' />
            <span>{rootTitle ?? t('entity.list.all', { defaultValue: 'All' })}</span>
            <span className={styles.groupCount}>({items.length})</span>
          </span>
        ),
        selectable: false,
        expanded: true,
        children: items.map(buildLeafNode)
      }]
    }

    // grouped view
    const groupedMap: Record<number, EntityListItem[]> = {}
    for (const g of groups) groupedMap[g.id] = []
    const ungrouped: EntityListItem[] = []
    const groupIds = new Set(groups.map(g => g.id))
    for (const it of items) {
      const gid = resolveGroupId(it, groups)
      if (gid != null && groupIds.has(gid)) groupedMap[gid].push(it)
      else ungrouped.push(it)
    }

    const nodes: DataNode[] = groups
      .filter(g => (groupedMap[g.id] ?? []).length > 0)
      .map(g => ({
        key: `group-${g.id}`,
        title: groupHeader(g, (groupedMap[g.id] ?? []).length),
        selectable: false,
        children: (groupedMap[g.id] ?? []).map(buildLeafNode)
      }))
    if (ungrouped.length > 0) {
      nodes.push({
        key: 'group-unknown',
        title: groupHeader({ id: null, name: t('entity.group.unknown', { defaultValue: 'unbekannt' }) }, ungrouped.length),
        selectable: false,
        children: ungrouped.map(buildLeafNode)
      })
    }
    return nodes
  }, [items, groups, resolveGroupId, t, dragType, onMove, leafIcon])

  const initialExpandedKeys = React.useMemo<React.Key[]>(() => {
    const keys: React.Key[] = []
    for (const n of buildTreeData) {
      if (n.key === 'root' || n.key === 'group-unknown') keys.push(n.key as React.Key)
    }
    return keys
  }, [buildTreeData])

  const [expandedKeys, setExpandedKeys] = React.useState<React.Key[]>(initialExpandedKeys)
  const expandedTouchedRef = React.useRef(false)
  React.useEffect(() => {
    if (!expandedTouchedRef.current) {
      setExpandedKeys(initialExpandedKeys)
    }
  }, [initialExpandedKeys])

  return (
    <ContentLayout
      renderToolbar={ (
        <PimToolbar>
          <IconButton icon={{ value: 'refresh' }} onClick={ onReload }>
            {t('toolbar.reload', { defaultValue: 'Reload' })}
          </IconButton>
          <Dropdown menu={{ items: [{ key: 'add', label: addLabel ?? t('toolbar.new', { defaultValue: 'New' }), icon: <Icon value='new' />, onClick: () => onAdd() }] }} trigger={ ['click'] }>
            <DropdownButton>
              {t('toolbar.new', { defaultValue: 'New' })}
            </DropdownButton>
          </Dropdown>
        </PimToolbar>
      ) }
    >
      {loading ? (
        <div className={styles.contentPadding}>
          <Skeleton active title={false} paragraph={{ rows: 8 }} />
        </div>
      ) : (
        <Tree
          className={ styles.tree }
          showLine={false}
          defaultExpandedKeys={ expandedKeys }
          expandedKeys={ expandedKeys }
          onExpand={ (keys) => { expandedTouchedRef.current = true; setExpandedKeys(keys as React.Key[]) } }
          selectable
          treeData={ buildTreeData }
          onSelect={ (keys) => {
            const key = Array.isArray(keys) ? keys[0] : keys as any
            if (typeof key === 'number') onSelect(key)
          } }
        />
      )}
    </ContentLayout>
  )
}

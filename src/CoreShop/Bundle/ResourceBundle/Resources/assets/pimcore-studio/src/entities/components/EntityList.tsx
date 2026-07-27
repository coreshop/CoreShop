import React from 'react'
import { useTranslation } from 'react-i18next'
import { Skeleton } from 'antd'
import {
  ContentLayout,
  Dropdown,
  DropdownButton,
  Icon,
  IconButton,
  Toolbar as PimToolbar,
  Draggable,
  TreeElement,
  type TreeDataItem,
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

interface NodeMeta {
  item?: EntityListItem
  group?: GroupItem | { id: null, name: string }
}

export const EntityList: React.FC<EntityListProps> = ({
  items,
  groups,
  loading,
  rootTitle,
  addLabel,
  leafIcon = 'widget',
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

  const buildTreeData = React.useMemo<TreeDataItem[]>(() => {
    const buildLeafNode = (it: EntityListItem): TreeDataItem => ({
      key: it.id,
      title: it.active === false
        ? `${it.name} (${t('entity.inactive', { defaultValue: 'Inactive' })})`
        : it.name,
      icon: <Icon value={ leafIcon } />,
      // Pimcore's drag wrappers need this class to keep rows at 24px
      className: `ant-tree-node--has-drag-and-drop ${it.active === false ? styles.inactive : ''}`,
      isLeaf: true,
      actions: [{ key: 'delete', icon: 'trash' }],
      meta: { item: it } satisfies NodeMeta,
    })

    const buildGroupNode = (
      key: string,
      group: GroupItem | { id: null, name: string },
      children: EntityListItem[]
    ): TreeDataItem => ({
      key,
      title: `${group.name} (${children.length})`,
      icon: <Icon value='folder' />,
      className: 'ant-tree-node--has-drag-and-drop',
      meta: { group } satisfies NodeMeta,
      children: children.map(buildLeafNode)
    })

    if (!groups || groups.length === 0 || !resolveGroupId) {
      return [buildGroupNode(
        'root',
        { id: null, name: rootTitle ?? t('entity.list.all', { defaultValue: 'All' }) },
        items
      )]
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

    const nodes: TreeDataItem[] = groups
      .filter(g => (groupedMap[g.id] ?? []).length > 0)
      .map(g => buildGroupNode(`group-${g.id}`, g, groupedMap[g.id] ?? []))

    if (ungrouped.length > 0) {
      nodes.push(buildGroupNode(
        'group-unknown',
        { id: null, name: t('entity.group.unknown', { defaultValue: 'unbekannt' }) },
        ungrouped
      ))
    }
    return nodes
  }, [items, groups, resolveGroupId, rootTitle, t, leafIcon])

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

  const toggleExpanded = (key: React.Key): void => {
    expandedTouchedRef.current = true
    setExpandedKeys((keys) => keys.includes(key) ? keys.filter(k => k !== key) : [...keys, key])
  }

  // only wraps the title for drag-and-drop — everything visual comes from TreeElement itself
  const renderTitle = (node: any, initialComponent: React.ReactElement): React.ReactNode => {
    const meta = node.meta as NodeMeta | undefined

    if (meta?.item !== undefined && buildDragInfo !== undefined) {
      return <Draggable info={ buildDragInfo(meta.item) as DragAndDropInfo }>{initialComponent}</Draggable>
    }

    if (meta?.group !== undefined && dragType !== undefined && onMove !== undefined) {
      const group = meta.group

      return (
        <DroppableEntity
          accept={ dragType }
          isValidData={ (info) => typeof info?.data?.id === 'number' }
          onDrop={ (info) => { const id = info?.data?.id; if (typeof id === 'number') onMove(id, group.id) } }
        >
          {initialComponent}
        </DroppableEntity>
      )
    }

    return initialComponent
  }

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
      {loading === true ? (
        <div className={styles.contentPadding}>
          <Skeleton active title={false} paragraph={{ rows: 8 }} />
        </div>
      ) : (
        <TreeElement
          className={ styles.tree }
          defaultExpandedKeys={ expandedKeys }
          onExpand={ (keys) => { expandedTouchedRef.current = true; setExpandedKeys(keys) } }
          onActionsClick={ (key, action) => {
            if (action === 'delete') {
              const id = Number.parseInt(key, 10)
              if (!Number.isNaN(id)) onDelete(id)
            }
          } }
          onSelected={ (key) => {
            if (typeof key === 'number') onSelect(key)
            else toggleExpanded(key as React.Key)
          } }
          titleRender={ renderTitle }
          treeData={ buildTreeData }
        />
      )}
    </ContentLayout>
  )
}

import React from 'react'
import type { EntityListItem } from '../types'
import { EntityApi } from '../api'
import { EntityTabbedLayout } from './EntityTabbedLayout'
import { EntityList, type GroupItem } from './EntityList'
import type { DragAndDropInfo } from '@pimcore/studio-ui-bundle/components'

export interface GroupedEntityTabbedManagerProps<TDetail extends Record<string, any>> {
  api: EntityApi<TDetail>
  loadGroups: () => Promise<GroupItem[]>
  resolveGroupId: (li: EntityListItem, groups: GroupItem[]) => number | null | undefined
  getTitle?: (listItem?: EntityListItem, data?: TDetail) => string
  buildSavePayload?: (data: TDetail) => Record<string, any>
  onAdd: (groupId?: number) => Promise<number>
  renderDetail: (data: TDetail | undefined, setData: (draft: Partial<TDetail>) => void, groups: GroupItem[], ctx?: { currentLocale?: string, locales?: string[] }) => React.ReactNode
  leftExtras?: React.ReactNode
  localizable?: boolean
  applyGroup?: (data: TDetail, groupId: number | null) => TDetail
  buildDragInfo?: (item: EntityListItem) => DragAndDropInfo | null
  dragType?: string
  leafIcon?: string
}

export function GroupedEntityTabbedManager<TDetail extends Record<string, any>>({ api, loadGroups, resolveGroupId, getTitle, buildSavePayload, onAdd, renderDetail, leftExtras, localizable, applyGroup, buildDragInfo, dragType, leafIcon }: GroupedEntityTabbedManagerProps<TDetail>): React.JSX.Element {
  const [groups, setGroups] = React.useState<GroupItem[]>([])
  const didInitialLoad = React.useRef(false)
  const computedBuildDragInfo = React.useMemo(() => {
    if (buildDragInfo) return buildDragInfo
    if (!dragType) return undefined
    return (li: EntityListItem): DragAndDropInfo => ({
      type: dragType,
      title: li?.name ?? `#${li?.id}`,
      icon: { value: 'widget-default' },
      data: li
    })
  }, [buildDragInfo, dragType])

  const loadAll = async (loadList: () => Promise<void>): Promise<void> => {
    const gs = await loadGroups()
    setGroups(gs)
    await loadList()
  }

  // Load groups only on mount (entity list is loaded by useEntityTabs)
  React.useEffect(() => {
    void (async () => {
      if (!didInitialLoad.current) {
        didInitialLoad.current = true
        const gs = await loadGroups()
        setGroups(gs)
      }
    })()
  }, [loadGroups])

  return (
    <EntityTabbedLayout<TDetail>
      api={ api }
      getTitle={ getTitle }
      buildSavePayload={ buildSavePayload }
      localizable={ localizable }
      leftExtras={ leftExtras }
      renderLeft={ ({ items, loading, loadList, openTab, onDelete }) => (
        <>
        <EntityList
          groups={ groups }
          items={ items }
          loading={ loading }
          leafIcon={ leafIcon }
          buildDragInfo={ computedBuildDragInfo }
          dragType={ dragType }
          onMove={ async (id, targetGroupId) => {
            if (!applyGroup) return
            const detail = await api.get(id)
            const updated = applyGroup(detail.data as TDetail, targetGroupId)
            const payload = buildSavePayload ? buildSavePayload(updated) : (updated as unknown as Record<string, any>)
            await api.save(payload)
            await loadList()
          } }
          onAdd={ async (gid?: number) => {
            if (!onAdd) return
            const id = await onAdd(gid)
            await loadList()
            await openTab(id)
          } }
          onDelete={ (id) => { void onDelete(id) } }
          onReload={ () => { void loadAll(loadList) } }
          onSelect={ (id) => { void openTab(id) } }
          resolveGroupId={ (li, gs) => resolveGroupId(li, gs) }
        />
        </>
      ) }
      renderDetail={ (data, setData, ctx) => renderDetail(data, setData, groups, ctx) }
    />
  )
}

import React from 'react'
import type { EntityListItem } from '../types'
import { EntityList } from './EntityList'
import { EntityApi } from '../api'
import { EntityTabbedLayout } from './EntityTabbedLayout'
import type { DragAndDropInfo } from '@pimcore/studio-ui-bundle/components'

export interface EntityTabbedManagerProps<TDetail extends Record<string, any>> {
  api: EntityApi<TDetail>
  getTitle?: (listItem?: EntityListItem, data?: TDetail) => string
  buildSavePayload?: (data: TDetail) => Record<string, any>
  onAdd?: () => Promise<number>
  renderDetail: (data: TDetail | undefined, setData: (draft: Partial<TDetail>) => void, ctx?: { currentLocale?: string, locales?: string[] }) => React.ReactNode
  leftExtras?: React.ReactNode
  localizable?: boolean
  buildDragInfo?: (item: EntityListItem) => DragAndDropInfo | null
  dragType?: string
  leftRootTitle?: string
  leafIcon?: string
}

export function EntityTabbedManager<TDetail extends Record<string, any>>({ api, getTitle, buildSavePayload, onAdd, renderDetail, leftExtras, localizable, buildDragInfo, dragType, leftRootTitle, leafIcon }: EntityTabbedManagerProps<TDetail>): React.JSX.Element {
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
  return (
    <EntityTabbedLayout<TDetail>
      api={ api }
      getTitle={ getTitle }
      buildSavePayload={ buildSavePayload }
      localizable={ localizable }
      leftExtras={ leftExtras }
      renderLeft={ ({ items, loading, loadList, openTab, onDelete }) => (
        (
          <EntityList
            items={ items }
            loading={ loading }
            rootTitle={ leftRootTitle }
            leafIcon={ leafIcon }
            buildDragInfo={ computedBuildDragInfo }
            onAdd={ async () => {
              if (!onAdd) return
              const id = await onAdd()
              await loadList()
              await openTab(id)
            } }
            onDelete={ (id) => { void onDelete(id) } }
            onReload={ () => { void loadList() } }
            onSelect={ (id) => { void openTab(id) } }
          />
        )
      ) }
      renderDetail={ (data, setData, ctx) => renderDetail(data, setData, ctx) }
    />
  )
}

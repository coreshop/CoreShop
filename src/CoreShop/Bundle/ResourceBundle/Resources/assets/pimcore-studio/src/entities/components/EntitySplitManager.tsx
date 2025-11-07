/**
 * CoreShop ResourceBundle Studio Plugin
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
import { SplitLayout, Dropdown, Icon } from '@pimcore/studio-ui-bundle/components'
import { EntityList } from './EntityList'
import type { EntityListItem } from '../types'
import { EntityApi } from '../api'
import type { DragAndDropInfo } from '@pimcore/studio-ui-bundle/components'

export interface EntitySplitManagerProps<TDetail extends Record<string, any>> {
  api: EntityApi<TDetail>
  renderDetail: (
    data: TDetail | undefined,
    loading: boolean,
    onSave: (data: TDetail) => Promise<void>,
    onChange: (data: TDetail) => void
  ) => React.ReactNode
  createEmpty: () => TDetail
  leftRootTitle?: string
  dragType?: string
  buildDragInfo?: (item: EntityListItem) => DragAndDropInfo | null
}

export function EntitySplitManager<TDetail extends Record<string, any>>({
  api,
  renderDetail,
  createEmpty,
  leftRootTitle,
  dragType,
  buildDragInfo
}: EntitySplitManagerProps<TDetail>): React.JSX.Element {
  const [list, setList] = React.useState<EntityListItem[]>([])
  const [loadingList, setLoadingList] = React.useState(false)
  const [selectedId, setSelectedId] = React.useState<number | null>(null)
  const [detail, setDetail] = React.useState<TDetail | undefined>(undefined)
  const [editingData, setEditingData] = React.useState<TDetail | undefined>(undefined)
  const [loadingDetail, setLoadingDetail] = React.useState(false)

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

  const loadList = React.useCallback(async () => {
    setLoadingList(true)
    try {
      const items = await api.list()
      setList(items)
    } catch (err) {
      console.error('Failed to load list:', err)
      setList([])
    } finally {
      setLoadingList(false)
    }
  }, [api])

  React.useEffect(() => {
    void loadList()
  }, [loadList])

  const loadDetail = React.useCallback(async (id: number) => {
    setLoadingDetail(true)
    try {
      const response = await api.get(id)
      setDetail(response.data)
      setEditingData({ ...response.data })
    } catch (err) {
      console.error('Failed to load detail:', err)
    } finally {
      setLoadingDetail(false)
    }
  }, [api])

  const handleSelect = React.useCallback((id: number) => {
    setSelectedId(id)
    void loadDetail(id)
  }, [loadDetail])

  const handleAdd = React.useCallback(async () => {
    const newItem = createEmpty()
    setSelectedId(null)
    setDetail(undefined)
    setEditingData(newItem)
  }, [createEmpty])

  const handleDelete = React.useCallback(async (id: number) => {
    try {
      await api.delete(id)
      await loadList()
      if (selectedId === id) {
        setSelectedId(null)
        setDetail(undefined)
        setEditingData(undefined)
      }
    } catch (err) {
      console.error('Failed to delete:', err)
    }
  }, [api, loadList, selectedId])

  const handleSave = React.useCallback(async (data: TDetail) => {
    try {
      await api.save(data)
      await loadList()
      if (data.id) {
        await loadDetail(data.id)
      }
    } catch (err) {
      console.error('Failed to save:', err)
      throw err
    }
  }, [api, loadList, loadDetail])

  const handleChange = React.useCallback((data: TDetail) => {
    setEditingData(data)
  }, [])

  const left = {
    id: 'entity-list',
    size: 25,
    minSize: 220,
    children: [
      <EntityList
        key='entity-list'
        items={list}
        loading={loadingList}
        rootTitle={leftRootTitle}
        buildDragInfo={computedBuildDragInfo}
        onAdd={handleAdd}
        onDelete={handleDelete}
        onReload={loadList}
        onSelect={handleSelect}
      />
    ]
  }

  const right = {
    id: 'entity-detail',
    size: 75,
    minSize: 400,
    children: [
      <>
        {editingData ? (
          renderDetail(editingData, loadingDetail, handleSave, handleChange)
        ) : (
          <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', height: '100%', color: '#999' }}>
            Select an item to edit or create a new one
          </div>
        )}
      </>
    ]
  }

  return (
    <SplitLayout
      leftItem={left}
      rightItem={right}
      withDivider
    />
  )
}

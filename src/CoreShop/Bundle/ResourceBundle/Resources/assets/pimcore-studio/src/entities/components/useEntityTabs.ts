import React from 'react'
import type { EntityListItem } from '../types'
import { EntityApi } from '../api'

export interface EntityTab<T> {
  id: number
  title: string
  data?: T
  dirty: boolean
  loading: boolean
}

export interface UseEntityTabsOptions<TDetail extends Record<string, any>> {
  api: EntityApi<TDetail>
  getTitle?: (li?: EntityListItem, data?: TDetail) => string
  buildSavePayload?: (data: TDetail) => Record<string, any>
}

export function useEntityTabs<TDetail extends Record<string, any>>({ api, getTitle, buildSavePayload }: UseEntityTabsOptions<TDetail>) {
  const [list, setList] = React.useState<EntityListItem[]>([])
  const [tabs, setTabs] = React.useState<Array<EntityTab<TDetail>>>([])
  const [activeKey, setActiveKey] = React.useState<string | undefined>(undefined)
  const [loadingList, setLoadingList] = React.useState<boolean>(false)

  const resolveTitle = (li?: EntityListItem, data?: TDetail): string => {
    if (getTitle !== undefined) return getTitle(li, data)
    return (data as any)?.name ?? li?.name ?? `#${li?.id ?? ''}`
  }

  const loadList = async (): Promise<void> => {
    setLoadingList(true)
    try {
      const items = await api.list()
      setList(items)
      // refresh tab titles from list if needed
      setTabs(prev => prev.map(tab => {
        const li = items.find(i => i.id === tab.id)
        return { ...tab, title: resolveTitle(li, tab.data) }
      }))
    } finally {
      setLoadingList(false)
    }
  }

  React.useEffect(() => { void loadList() }, [])

  const findTab = (id: number): EntityTab<TDetail> | undefined => tabs.find(t => t.id === id)
  const updateTab = (id: number, patch: Partial<EntityTab<TDetail>>): void => {
    setTabs(prev => prev.map(t => t.id === id ? { ...t, ...patch } : t))
  }

  const ensureTab = (id: number): void => {
    setTabs(prev => {
      if (prev.some(t => t.id === id)) return prev
      const li = list.find(i => i.id === id)
      return [...prev, { id, title: resolveTitle(li), dirty: false, loading: false }]
    })
  }

  const openTab = async (id: number): Promise<void> => {
    ensureTab(id)
    setActiveKey(String(id))
    await loadDetail(id)
  }

  const forceCloseTab = (id: number): void => {
    setTabs(prev => prev.filter(t => t.id !== id))
    if (activeKey === String(id)) {
      // Activate last remaining tab
      setTimeout(() => {
        setActiveKey(prev => {
          const remaining = tabs.filter(t => t.id !== id)
          return remaining.length ? String(remaining[remaining.length - 1].id) : undefined
        })
      }, 0)
    }
  }

  const loadDetail = async (id: number): Promise<void> => {
    updateTab(id, { loading: true })
    try {
      const res = await api.get(id)
      const li = list.find(i => i.id === id)
      updateTab(id, { data: res.data, dirty: false, loading: false, title: resolveTitle(li, res.data) })
    } catch (e) {
      updateTab(id, { loading: false })
    }
  }

  const onSave = async (id: number): Promise<void> => {
    const tab = findTab(id)
    if (tab?.data == null) return
    updateTab(id, { loading: true })
    const payload = buildSavePayload !== undefined ? buildSavePayload(tab.data) : (tab.data as any)
    try {
      await api.save(payload)
      updateTab(id, { dirty: false })
      await loadList()
    } finally {
      updateTab(id, { loading: false })
    }
  }

  const onReload = async (id: number): Promise<void> => {
    await loadDetail(id)
  }

  const onDelete = async (id: number): Promise<void> => {
    await api.delete(id)
    await loadList()
    forceCloseTab(id)
  }

  const activeId = activeKey !== undefined ? parseInt(activeKey) : tabs[0]?.id
  const activeTab = activeId !== undefined ? tabs.find(t => t.id === activeId) : undefined

  return {
    list,
    loadingList,
    loadList,
    tabs,
    activeKey,
    setActiveKey,
    activeTab,
    findTab,
    updateTab,
    openTab,
    loadDetail,
    forceCloseTab,
    onSave,
    onReload,
    onDelete,
  }
}

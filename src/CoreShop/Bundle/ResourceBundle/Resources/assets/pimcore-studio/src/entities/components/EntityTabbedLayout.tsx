import React from 'react'
import { SplitLayout, Content, ContentLayout, Popconfirm } from '@pimcore/studio-ui-bundle/components'
import { Tabs } from '@pimcore/studio-ui-bundle/components'
import type { EntityListItem } from '../types'
import { EntityApi } from '../api'
import { useEntityTabs } from './useEntityTabs'
import { EntityFooterToolbar } from './EntityFooterToolbar'
import { LocalizationProvider, useLocalization } from './localization/LocalizationContext'
import { Select, Skeleton } from 'antd'
import { useEntityTabbedLayoutStyles } from './entity-tabbed-layout.styles'

/** Deep-merge a draft into existing data, handling nested objects like translations. */
function deepMergeDraft<T extends Record<string, any>>(existing: T, draft: Partial<T>): T {
  const result = { ...existing }
  for (const key of Object.keys(draft) as Array<keyof T>) {
    const existingVal = existing[key]
    const draftVal = draft[key]
    if (
      draftVal !== null &&
      typeof draftVal === 'object' &&
      !Array.isArray(draftVal) &&
      existingVal !== null &&
      typeof existingVal === 'object' &&
      !Array.isArray(existingVal)
    ) {
      result[key] = deepMergeDraft(existingVal as any, draftVal as any)
    } else {
      result[key] = draftVal as any
    }
  }
  return result
}

export interface LeftPaneContext {
  items: EntityListItem[]
  loading: boolean
  loadList: () => Promise<void>
  openTab: (id: number) => Promise<void>
  onDelete: (id: number) => Promise<void>
}

export interface EntityTabbedLayoutProps<TDetail extends Record<string, any>> {
  api: EntityApi<TDetail>
  getTitle?: (listItem?: EntityListItem, data?: TDetail) => string
  buildSavePayload?: (data: TDetail) => Record<string, any>
  renderLeft: (ctx: LeftPaneContext) => React.ReactNode
  renderDetail: (data: TDetail | undefined, setData: (draft: Partial<TDetail>) => void, ctx?: { currentLocale?: string, locales?: string[] }) => React.ReactNode
  leftExtras?: React.ReactNode
  localizable?: boolean
}

export function EntityTabbedLayout<TDetail extends Record<string, any>>({ api, getTitle, buildSavePayload, renderLeft, renderDetail, leftExtras, localizable }: EntityTabbedLayoutProps<TDetail>): React.JSX.Element {
  const { styles } = useEntityTabbedLayoutStyles()
  const {
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
    onSave,
    onReload,
    onDelete,
    forceCloseTab,
  } = useEntityTabs<TDetail>({ api, getTitle, buildSavePayload })

  const [popConfirmOpen, setPopConfirmOpen] = React.useState<number | null>(null)
  const onHandleClose = (key: string): void => {
    const id = parseInt(key)
    const tab = findTab(id)
    if (!tab) return
    if (tab.dirty) {
      setPopConfirmOpen(id)
    } else {
      forceCloseTab(id)
    }
  }

  const left = {
    id: 'entity-list',
    size: 25,
    minSize: 220,
    children: [
      renderLeft({
        items: list,
        loading: loadingList,
        loadList,
        openTab,
        onDelete,
      })
    ]
  }

  const right = {
    id: 'entity-detail-tabs',
    size: 75,
    minSize: 400,
    children: [
      <ContentLayout
        key='tabs-layout'
        renderToolbar={ (
          localizable ? (
            <LocalizedToolbar 
              dirty={ activeTab?.dirty }
              loading={ activeTab?.loading }
              onReload={ () => { if (activeTab) void onReload(activeTab.id) } }
              onSave={ () => { if (activeTab) void onSave(activeTab.id) } }
              leftExtras={ leftExtras }
            />
          ) : (
            <EntityFooterToolbar
              dirty={ activeTab?.dirty }
              loading={ activeTab?.loading }
              onReload={ () => { if (activeTab) void onReload(activeTab.id) } }
              onSave={ () => { if (activeTab) void onSave(activeTab.id) } }
              leftExtras={ leftExtras }
            />
          )
        ) }
      >
          <Tabs
            activeKey={ activeKey }
            items={ tabs.map(t => ({ key: String(t.id), label: (
              <Popconfirm
                onCancel={ () => { setPopConfirmOpen(null) } }
                onConfirm={ () => { forceCloseTab(t.id); setPopConfirmOpen(null) } }
                open={ popConfirmOpen === t.id }
                title={ 'Discard changes and close?' }
              >
                {`${t.title}${t.dirty ? ' *' : ''}`}
              </Popconfirm>
            ) })) }
            onChange={ (key) => setActiveKey(key) }
            onClose={ (key) => onHandleClose(key as string) }
          />
          <Content className={`detail-tabs__content ${styles.detailContent}`}>
            {activeTab !== undefined && (
              activeTab.loading ? (
                <div className={ styles.contentPadding }>
                  <Skeleton active paragraph={{ rows: 10 }} />
                </div>
              ) : (
                localizable ? (
                  <RenderWithLocale
                    render={ (ctx) => renderDetail(activeTab.data, (draft) => {
                      if (activeTab) {
                        updateTab(activeTab.id, (tab) => ({ data: deepMergeDraft(tab.data as any, draft), dirty: true }))
                      }
                    }, ctx) }
                  />
                ) : (
                  renderDetail(activeTab.data, (draft) => {
                    if (activeTab) {
                      updateTab(activeTab.id, (tab) => ({ data: deepMergeDraft(tab.data as any, draft), dirty: true }))
                    }
                  })
                )
              )
            )}
          </Content>
      </ContentLayout>
    ]
  }

  const content = <SplitLayout leftItem={ left } rightItem={ right } withDivider withToolbar />

  if (!localizable) {
      return content
  }

  return <LocalizationProvider>{content}</LocalizationProvider>
}


function LocalizedToolbar({ dirty, loading, onReload, onSave, leftExtras }: { dirty?: boolean, loading?: boolean, onReload: () => void, onSave: () => void, leftExtras?: React.ReactNode }) {
  const { locales, currentLocale, setCurrentLocale } = useLocalization()
  return (
    <EntityFooterToolbar
      dirty={ dirty }
      loading={ loading }
      onReload={ onReload }
      onSave={ onSave }
      leftExtras={ (
        <>
          <Select
            size='small'
            value={ currentLocale }
            options={ locales.map(l => ({ value: l, label: l.toUpperCase() })) }
            onChange={ setCurrentLocale }
            style={{ marginRight: 8 }}
          />
          {leftExtras}
        </>
      ) }
    />
  )
}

const RenderWithLocale: React.FC<{ render: (ctx: { currentLocale: string, locales: string[] }) => React.ReactNode }> = ({ render }) => {
  const { locales, currentLocale } = useLocalization()
  return <>{render({ locales, currentLocale })}</>
}

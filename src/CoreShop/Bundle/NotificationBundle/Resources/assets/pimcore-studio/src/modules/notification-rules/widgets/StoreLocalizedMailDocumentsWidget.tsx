/**
 * CoreShop NotificationBundle Studio Plugin
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
import { Tabs, Collapse, Typography } from 'antd'
import { DocumentSelect } from '@coreshop/pimcore/src/components/DocumentSelect'
import { useStudioLanguages } from '@coreshop/resource/src/components/localization/useStudioLanguages'
import { storeApi } from '@coreshop/store/src/modules/stores/api'

interface Store {
  id: number
  name: string
}

// Module-level cache for stores
let cachedStores: Store[] | null = null
let storeLoadPromise: Promise<Store[]> | null = null

const loadStores = async (): Promise<Store[]> => {
  if (cachedStores) return cachedStores
  if (storeLoadPromise) return storeLoadPromise

  storeLoadPromise = (async () => {
    try {
      const stores = await storeApi.list()
      cachedStores = stores.map((s: any) => ({ id: s.id, name: s.name }))
      return cachedStores
    } catch (err) {
      console.error('Failed to load stores:', err)
      return []
    } finally {
      storeLoadPromise = null
    }
  })()

  return storeLoadPromise
}

interface StoreLocalizedMailDocumentsWidgetProps {
  value?: Record<number, Record<string, number | null>>
  onChange?: (value: Record<number, Record<string, number | null>>) => void
}

export const StoreLocalizedMailDocumentsWidget: React.FC<StoreLocalizedMailDocumentsWidgetProps> = ({
  value,
  onChange,
}) => {
  const languages = useStudioLanguages()
  const mails = value ?? {}
  const [stores, setStores] = React.useState<Store[]>(cachedStores ?? [])
  const [loading, setLoading] = React.useState(!cachedStores)

  React.useEffect(() => {
    void (async () => {
      if (!cachedStores) {
        setLoading(true)
      }
      try {
        const loadedStores = await loadStores()
        setStores(loadedStores)
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  const handleMailChange = (storeId: number, lang: string, documentId: number | null) => {
    onChange?.({
      ...mails,
      [storeId]: {
        ...(mails[storeId] ?? {}),
        [lang]: documentId,
      },
    })
  }

  if (loading) {
    return <Typography.Text type="secondary">Loading stores...</Typography.Text>
  }

  const collapseItems = stores.map(store => {
    const storeMailConfig = mails[store.id] ?? {}

    const languageTabs = languages.map(lang => ({
      key: lang,
      label: lang.toUpperCase(),
      children: (
        <div style={{ padding: 8 }}>
          <DocumentSelect
            value={storeMailConfig[lang] ?? null}
            onChange={(id) => handleMailChange(store.id, lang, id)}
            documentTypes={['email']}
          />
        </div>
      ),
    }))

    return {
      key: String(store.id),
      label: store.name,
      children: (
        <Tabs
          defaultActiveKey={languages[0]}
          items={languageTabs}
          size="small"
        />
      ),
    }
  })

  return (
    <Collapse
      items={collapseItems}
      defaultActiveKey={stores.length > 0 ? [String(stores[0].id)] : []}
    />
  )
}

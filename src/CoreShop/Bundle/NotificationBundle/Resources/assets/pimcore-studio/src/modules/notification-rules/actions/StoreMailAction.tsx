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
import { Form, Checkbox, Tabs, Typography, Collapse } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ActionComponentProps } from '@coreshop/rule/src/rules/types'
import { DocumentSelect } from '@coreshop/pimcore/src/components/DocumentSelect'
import { storeApi } from '@coreshop/store/src/modules/stores/api'

interface StoreMailActionConfig {
  mails?: Record<number, Record<string, number | null>>
  doNotSendToDesignatedRecipient?: boolean
}

interface Store {
  id: number
  name: string
}

// Get available languages from Pimcore
const getAvailableLanguages = (): string[] => {
  try {
    // @ts-ignore - Pimcore global
    return window.pimcore?.settings?.websiteLanguages ?? ['en']
  } catch {
    return ['en']
  }
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

export const StoreMailAction: React.FC<ActionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const config = (data ?? {}) as StoreMailActionConfig
  const languages = getAvailableLanguages()
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
    onChange({
      ...config,
      mails: {
        ...(config.mails ?? {}),
        [storeId]: {
          ...((config.mails ?? {})[storeId] ?? {}),
          [lang]: documentId
        }
      }
    })
  }

  const handleCheckboxChange = (checked: boolean) => {
    onChange({
      ...config,
      doNotSendToDesignatedRecipient: checked
    })
  }

  if (loading) {
    return <Typography.Text type="secondary">Loading stores...</Typography.Text>
  }

  // Build collapse items for each store
  const collapseItems = stores.map(store => {
    const storeMailConfig = config.mails?.[store.id] ?? {}

    const languageTabs = languages.map(lang => ({
      key: lang,
      label: lang.toUpperCase(),
      children: (
        <div style={{ padding: 8 }}>
          <Form.Item
            label={t('coreshop_email_document', { defaultValue: 'Email Document' })}
          >
            <DocumentSelect
              value={storeMailConfig[lang] ?? null}
              onChange={(id) => handleMailChange(store.id, lang, id)}
              documentTypes={['email']}
              placeholder={t('coreshop_select_email_document', { defaultValue: 'Select an email document' })}
            />
          </Form.Item>
        </div>
      )
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
      )
    }
  })

  return (
    <>
      <Typography.Text type="secondary" style={{ display: 'block', marginBottom: 16 }}>
        {t('coreshop_store_mail_action_description', {
          defaultValue: 'Configure email documents for each store and language. The appropriate email will be sent based on the order\'s store and the customer\'s language.'
        })}
      </Typography.Text>

      <Collapse
        items={collapseItems}
        defaultActiveKey={stores.length > 0 ? [String(stores[0].id)] : []}
        style={{ marginBottom: 16 }}
      />

      <Form.Item>
        <Checkbox
          checked={config.doNotSendToDesignatedRecipient ?? false}
          onChange={(e) => handleCheckboxChange(e.target.checked)}
        >
          {t('coreshop_mail_rule_do_not_send_to_designated_recipient', {
            defaultValue: 'Do not send to designated recipient'
          })}
        </Checkbox>
      </Form.Item>
    </>
  )
}

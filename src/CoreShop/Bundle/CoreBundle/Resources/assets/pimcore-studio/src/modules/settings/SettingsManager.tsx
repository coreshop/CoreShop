/**
 * CoreShop CoreBundle Studio Plugin
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
import { Tabs, Card, Spin, Empty, Button } from 'antd'
import { SaveOutlined } from '@ant-design/icons'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { storeApi } from '@coreshop/store/src/modules/stores/api'
import { settingsApi, type ConfigurationData } from './api'
import { StoreSettingsForm } from './StoreSettingsForm'
import { getErrorMessage } from '@coreshop/resource/src/entities'

interface StoreInfo {
  id: number
  name: string
}

/**
 * SettingsManager - Per-store configuration panel
 */
export const SettingsManager: React.FC = () => {
  const { t } = useTranslation()
  const messageApi = useMessage()

  const [stores, setStores] = React.useState<StoreInfo[]>([])
  const [configData, setConfigData] = React.useState<ConfigurationData>({})
  const [loading, setLoading] = React.useState(true)
  const [saving, setSaving] = React.useState(false)

  // Load stores and configuration data
  React.useEffect(() => {
    const load = async () => {
      setLoading(true)
      try {
        const [storeList, config] = await Promise.all([
          storeApi.list(),
          settingsApi.getAll()
        ])

        setStores(storeList.map(s => ({ id: s.id!, name: s.name ?? `Store #${s.id}` })))
        setConfigData(config)
      } catch (error) {
        void messageApi.error(getErrorMessage(error, 'Failed to load settings'))
      } finally {
        setLoading(false)
      }
    }

    void load()
  }, [])

  // Update a specific store's config value
  const handleStoreValueChange = (storeId: string, key: string, value: any) => {
    setConfigData(prev => ({
      ...prev,
      [storeId]: {
        ...(prev[storeId] ?? {}),
        [key]: value
      }
    }))
  }

  // Save all configuration
  const handleSave = async () => {
    setSaving(true)
    try {
      await settingsApi.saveAll(configData)
      void messageApi.success(t('coreshop_settings_save_success', { defaultValue: 'Settings saved successfully' }))
    } catch (error) {
      void messageApi.error(getErrorMessage(error, 'Failed to save settings'))
    } finally {
      setSaving(false)
    }
  }

  if (loading) {
    return (
      <Card title={t('coreshop_settings', { defaultValue: 'Settings' })} style={{ height: '100%' }}>
        <div style={{ display: 'flex', justifyContent: 'center', padding: 40 }}>
          <Spin size="large" />
        </div>
      </Card>
    )
  }

  if (stores.length === 0) {
    return (
      <Card title={t('coreshop_settings', { defaultValue: 'Settings' })} style={{ height: '100%' }}>
        <Empty description={t('coreshop_no_stores', { defaultValue: 'No stores configured' })} />
      </Card>
    )
  }

  const tabItems = stores.map(store => ({
    key: String(store.id),
    label: store.name,
    children: (
      <StoreSettingsForm
        storeId={String(store.id)}
        values={configData[String(store.id)] ?? {}}
        onChange={(key, value) => handleStoreValueChange(String(store.id), key, value)}
      />
    )
  }))

  return (
    <Card
      title={t('coreshop_settings', { defaultValue: 'Settings' })}
      style={{ height: '100%' }}
      extra={
        <Button
          type="primary"
          icon={<SaveOutlined />}
          onClick={handleSave}
          loading={saving}
        >
          {t('save', { defaultValue: 'Save' })}
        </Button>
      }
    >
      <Tabs
        defaultActiveKey={String(stores[0].id)}
        items={tabItems}
      />
    </Card>
  )
}

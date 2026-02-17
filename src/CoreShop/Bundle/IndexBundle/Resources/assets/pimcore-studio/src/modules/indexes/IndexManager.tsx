/**
 * CoreShop IndexBundle Index Manager
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
import { EntityTabbedManager } from '@coreshop/resource'
import { useFormModal, useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { indexApi, type Index, type IndexConfig } from './api'
import { IndexDetail } from './IndexDetail'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'

export const IndexManager: React.FC = () => {
  const { t } = useTranslation()
  const modal = useFormModal()
  const messageApi = useMessage()
  const [config, setConfig] = React.useState<IndexConfig | null>(null)

  // Load config on mount
  React.useEffect(() => {
    indexApi.getConfig()
      .then(setConfig)
      .catch(err => {
        void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load index config')))
      })
  }, [])

  return (
    <EntityTabbedManager<Index>
      api={indexApi}
      dragType='coreshop:index'
      leftRootTitle={t('coreshop_indexes', { defaultValue: 'Indices' })}
      getTitle={(li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}`}
      buildSavePayload={(data) => {
        // Convert columns array to object keyed by objectKey (ExtJS format)
        const columnsObj: Record<string, any> = {}
        data.columns?.forEach(col => {
          const { id, getter, getterConfig, interpreter, interpreterConfig, configuration, ...rest } = col
          const cleanCol: any = { ...rest }

          // Only include getter/interpreter if not null/empty
          if (getter) cleanCol.getter = getter
          if (getterConfig && Object.keys(getterConfig).length > 0) cleanCol.getterConfig = getterConfig
          if (interpreter) cleanCol.interpreter = interpreter
          if (interpreterConfig && Object.keys(interpreterConfig).length > 0) cleanCol.interpreterConfig = interpreterConfig

          // Add to object keyed by objectKey
          columnsObj[col.objectKey] = cleanCol
        })

        // Prepare configuration - convert arrays to objects
        const configObj: any = {}
        if (data.configuration) {
          if (data.configuration.indexes) {
            configObj.indexes = Array.isArray(data.configuration.indexes) && data.configuration.indexes.length === 0
              ? {}
              : data.configuration.indexes
          }
          if (data.configuration.localizedIndexes) {
            configObj.localizedIndexes = Array.isArray(data.configuration.localizedIndexes) && data.configuration.localizedIndexes.length === 0
              ? {}
              : data.configuration.localizedIndexes
          }
        }

        const payload: any = {
          id: data.id,
          name: data.name,
          class: data.class,
          worker: data.worker,
          indexLastVersion: data.indexLastVersion,
          columns: columnsObj,
          configuration: Object.keys(configObj).length > 0 ? configObj : { indexes: {}, localizedIndexes: {} }
        }

        return payload
      }}
      onAdd={async () => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop_indexes_add', { defaultValue: 'Add Index' }),
          label: t('coreshop_name', { defaultValue: 'Name' }),
          rule: {
            required: true,
            message: t('coreshop_name_required', { defaultValue: 'Name is required' }),
            pattern: /^[a-zA-Z0-9]+$/,
          },
          onOk: async (nameValue: string) => {
            const res = await indexApi.add({ name: nameValue })
            resolve(res.data.id!)
          }
        })
      })}
      renderDetail={(data, setData) => {
        if (!data) {
          return (
            <div style={{ padding: 12, color: 'var(--ant-color-text-tertiary)' }}>
              {t('coreshop_indexes_select', { defaultValue: 'Select an index to view details.' })}
            </div>
          )
        }

        if (!config) {
          return (
            <div style={{ padding: 12, color: 'var(--ant-color-text-tertiary)' }}>
              {t('coreshop_loading_configuration', { defaultValue: 'Loading configuration...' })}
            </div>
          )
        }

        return (
          <IndexDetail
            index={data}
            config={config}
            onChange={setData}
          />
        )
      }}
    />
  )
}

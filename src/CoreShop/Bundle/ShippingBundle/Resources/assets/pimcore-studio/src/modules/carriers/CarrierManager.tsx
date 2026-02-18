/**
 * CoreShop ShippingBundle Studio Plugin
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
import { EntityTabbedManager, getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'
import { carrierApi, type CarrierDetail, type CarrierConfig } from './api'
import { CarrierForm } from './CarrierForm'
import { useFormModal, useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'

export const CarrierManager: React.FC = () => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const [config, setConfig] = React.useState<CarrierConfig | null>(null)
  const modal = useFormModal()

  React.useEffect(() => {
    void loadConfig()
  }, [])

  const loadConfig = async (): Promise<void> => {
    try {
      const cfg = await carrierApi.getConfig()
      setConfig(cfg)
    } catch (err) {
      void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load carrier config')))
    }
  }

  if (!config) {
    return <div style={{ padding: 20 }}>{t('coreshop_loading_configuration', { defaultValue: 'Loading configuration...' })}</div>
  }

  return (
    <EntityTabbedManager<CarrierDetail>
      api={carrierApi}
      dragType="coreshop:carrier"
      leftRootTitle={t('coreshop_carriers', { defaultValue: 'Carriers' })}
      localizable
      getTitle={(li, data) => data?.identifier ?? li?.identifier ?? `#${li?.id ?? ''}`}
      buildSavePayload={(data) => data}
      onAdd={async () => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop_carrier', { defaultValue: 'Add Carrier' }),
          label: t('coreshop_identifier', { defaultValue: 'Identifier' }),
          rule: { required: true, message: t('coreshop_identifier_required', { defaultValue: 'Identifier is required' }) },
          onOk: async (value: string) => {
            const res = await carrierApi.add({
              identifier: value.toLowerCase().replace(/\s+/g, '-')
            })
            resolve(res.data.id!)
          }
        })
      })}
      renderDetail={(data, setData, ctx) => {
        if (!data) {
          return <div style={{ padding: 12, color: 'var(--ant-color-text-tertiary)' }}>{t('coreshop_carrier_select', { defaultValue: 'Select a carrier to view details.' })}</div>
        }

        return (
          <CarrierForm
            data={data}
            onChange={(draft) => setData(draft)}
            currentLocale={ctx?.currentLocale ?? 'en'}
            locales={ctx?.locales}
          />
        )
      }}
    />
  )
}

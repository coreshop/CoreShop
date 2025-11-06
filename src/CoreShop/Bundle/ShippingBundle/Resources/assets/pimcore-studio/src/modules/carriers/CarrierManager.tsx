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
import { EntityTabbedManager } from '@coreshop/resource/src/entities'
import { carrierApi, type CarrierDetail, type CarrierConfig } from './api'
import { CarrierForm } from './CarrierForm'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'

export const CarrierManager: React.FC = () => {
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
      console.error('Failed to load carrier config:', err)
    }
  }

  if (!config) {
    return <div style={{ padding: 20 }}>Loading configuration...</div>
  }

  return (
    <EntityTabbedManager<CarrierDetail>
      api={carrierApi}
      dragType="coreshop:carrier"
      leftRootTitle="Carriers"
      localizable
      getTitle={(li, data) => data?.identifier ?? li?.identifier ?? `#${li?.id ?? ''}`}
      buildSavePayload={(data) => {
        // Ensure stores is number[] (Ant Design Select can return string[])
        const stores = Array.isArray(data.stores)
          ? data.stores.map(s => typeof s === 'string' ? parseInt(s, 10) : s)
          : undefined

        // Remove 'id' from shipping rules (only include shippingRule, priority, stopPropagation, carrier)
        const shippingRules = data.shippingRules?.map(rule => ({
          shippingRule: rule.shippingRule,
          priority: rule.priority,
          stopPropagation: rule.stopPropagation,
          carrier: data.id
        }))

        return {
          id: data.id,
          identifier: data.identifier,
          name: data.name,
          trackingUrl: data.trackingUrl,
          logo: data.logo,
          translations: data.translations,
          taxCalculationStrategy: data.taxCalculationStrategy,
          hideFromCheckout: data.hideFromCheckout,
          shippingRules,
          stores,
          taxRule: data.taxRule
        }
      }}
      onAdd={async () => await new Promise<number>((resolve) => {
        modal.input({
          title: 'Add Carrier',
          label: 'Identifier',
          rule: { required: true, message: 'Identifier is required' },
          onOk: async (value: string) => {
            const res = await carrierApi.add({
              identifier: value.toLowerCase().replace(/\s+/g, '-')
            })
            resolve(res.data.id)
          }
        })
      })}
      renderDetail={(data, setData, ctx) => {
        if (!data) {
          return <div style={{ padding: 12, color: 'var(--ant-color-text-tertiary)' }}>Select a carrier to view details.</div>
        }

        return (
          <CarrierForm
            data={data}
            config={config}
            onChange={(draft) => setData(draft)}
            currentLocale={ctx?.currentLocale ?? 'en'}
          />
        )
      }}
    />
  )
}

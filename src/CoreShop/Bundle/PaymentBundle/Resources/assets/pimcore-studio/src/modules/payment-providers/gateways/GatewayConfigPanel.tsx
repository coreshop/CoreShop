/**
 * CoreShop PaymentBundle Studio Plugin
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
import { Form, Select, Typography, Alert, Spin } from 'antd'
import { useTranslation } from 'react-i18next'
import { container } from '@pimcore/studio-ui-bundle'
import { GatewayRegistry } from './GatewayRegistry'
import { coreshopPaymentServiceIds } from '../../payment-provider-rules/service-ids'
import type { GatewayConfig } from '../api'

interface GatewayFactory {
  type: string
  name: string
}

// Module-level cache to avoid multiple API calls
let cachedFactories: GatewayFactory[] | null = null
let loadPromise: Promise<GatewayFactory[]> | null = null

const loadGatewayFactories = async (): Promise<GatewayFactory[]> => {
  if (cachedFactories) {
    return cachedFactories
  }

  if (loadPromise) {
    return loadPromise
  }

  loadPromise = (async () => {
    try {
      const response = await fetch('/admin/coreshop/payment_providers/get-config', {
        credentials: 'same-origin'
      })

      if (!response.ok) {
        throw new Error(`Failed to load gateway factories: ${response.status}`)
      }

      const data = await response.json()
      cachedFactories = data.factories || []
      return cachedFactories
    } catch (err) {
      console.error('Failed to load gateway factories:', err)
      throw err
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

export interface GatewayConfigPanelProps {
  gatewayConfig?: GatewayConfig
  onChange: (gatewayConfig: GatewayConfig) => void
}

export const GatewayConfigPanel: React.FC<GatewayConfigPanelProps> = ({
  gatewayConfig,
  onChange
}) => {
  const { t } = useTranslation()
  const [factories, setFactories] = React.useState<GatewayFactory[]>(cachedFactories || [])
  const [loading, setLoading] = React.useState(!cachedFactories)

  // Track if the factory was already saved when data was first loaded
  // Only lock if: had a factory name when first loaded with an ID
  const [initialFactory, setInitialFactory] = React.useState<string | null | undefined>(undefined)

  React.useEffect(() => {
    // Only capture once when we have a gatewayConfig with an ID (loaded from server)
    // undefined = not initialized yet, null = initialized but no factory, string = initialized with factory
    if (initialFactory === undefined && gatewayConfig?.id !== undefined && gatewayConfig.id > 0) {
      setInitialFactory(gatewayConfig.factoryName ?? null)
    }
  }, [gatewayConfig, initialFactory])

  React.useEffect(() => {
    void (async () => {
      if (!cachedFactories) {
        setLoading(true)
      }
      try {
        const data = await loadGatewayFactories()
        setFactories(data)
      } catch {
        // Error already logged
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  const selectedFactory = gatewayConfig?.factoryName

  // Lock the select only if the gateway was saved with a factory
  // initialFactory: undefined = not loaded yet, null = loaded without factory, string = loaded with factory
  const isLocked = typeof initialFactory === 'string' && initialFactory.length > 0

  // Get the gateway registry from container
  const gatewayRegistry = React.useMemo(() => {
    if (container.isBound(coreshopPaymentServiceIds.gatewayConfiguratorRegistry)) {
      return container.get<GatewayRegistry>(coreshopPaymentServiceIds.gatewayConfiguratorRegistry)
    }
    return null
  }, [])

  // Get the custom configurator for the selected factory
  const GatewayConfigurator = selectedFactory && gatewayRegistry
    ? gatewayRegistry.get(selectedFactory)
    : undefined

  const handleFactoryChange = (factoryName: string) => {
    onChange({
      ...(gatewayConfig ?? { id: 0, gatewayName: '', config: [], decryptedConfig: {} }),
      factoryName,
      // Reset config when factory changes
      config: [],
      decryptedConfig: {}
    })
  }

  const handleConfigChange = (config: Record<string, any>) => {
    onChange({
      ...(gatewayConfig ?? { id: 0, factoryName: '', gatewayName: '', config: [] }),
      decryptedConfig: config
    })
  }

  if (loading) {
    return (
      <div style={{ textAlign: 'center', padding: 24 }}>
        <Spin />
        <Typography.Text type="secondary" style={{ display: 'block', marginTop: 8 }}>
          {t('coreshop_loading_gateway_factories', { defaultValue: 'Loading gateway factories...' })}
        </Typography.Text>
      </div>
    )
  }

  return (
    <div>
      <Form.Item
        label={t('coreshop_payment_provider_factory', { defaultValue: 'Payment Gateway' })}
        required
      >
        <Select
          value={selectedFactory}
          onChange={handleFactoryChange}
          placeholder={t('coreshop_select_payment_gateway', { defaultValue: 'Select a payment gateway' })}
          disabled={isLocked}
          options={factories.map(f => ({
            value: f.type,
            label: f.name
          }))}
          showSearch
          filterOption={(input, option) =>
            (option?.label ?? '').toLowerCase().includes(input.toLowerCase())
          }
        />
        {isLocked && (
          <Typography.Text type="secondary" style={{ display: 'block', marginTop: 4 }}>
            {t('coreshop_payment_gateway_readonly', { defaultValue: 'Gateway cannot be changed after saving' })}
          </Typography.Text>
        )}
      </Form.Item>

      {selectedFactory && (
        <div style={{ marginTop: 16 }}>
          {GatewayConfigurator ? (
            <GatewayConfigurator
              config={gatewayConfig?.decryptedConfig ?? {}}
              onChange={handleConfigChange}
            />
          ) : (
            <Alert
              type="info"
              message={t('coreshop_no_gateway_configuration', { defaultValue: 'No additional configuration required' })}
              description={t('coreshop_gateway_no_config_description', {
                defaultValue: 'This payment gateway does not require any additional configuration.'
              })}
            />
          )}
        </div>
      )}
    </div>
  )
}

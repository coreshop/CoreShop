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
import { SchemaForm } from '@coreshop/studio-form/src/schema-adapter'
import { GatewayRegistry } from './GatewayRegistry'
import { coreshopPaymentServiceIds } from '../../payment-provider-rules/service-ids'
import type { GatewayConfig } from '../api'

interface GatewayFactory {
  type: string
  name: string
}

interface GatewayConfigResponse {
  factories: GatewayFactory[]
  gatewayBlockPrefixes: Record<string, string>
}

// Module-level cache to avoid multiple API calls
let cachedConfig: GatewayConfigResponse | null = null
let loadPromise: Promise<GatewayConfigResponse> | null = null

const loadGatewayConfig = async (): Promise<GatewayConfigResponse> => {
  if (cachedConfig) {
    return cachedConfig
  }

  if (loadPromise) {
    return loadPromise
  }

  loadPromise = (async () => {
    try {
      const response = await fetch('/pimcore-studio/api/coreshop/payment_providers/get-config', {
        credentials: 'same-origin'
      })

      if (!response.ok) {
        throw new Error(`Failed to load gateway config: ${response.status}`)
      }

      const data = await response.json()
      cachedConfig = {
        factories: data.factories || [],
        gatewayBlockPrefixes: data.gatewayBlockPrefixes || {}
      }
      return cachedConfig
    } catch (err) {
      console.error('Failed to load gateway config:', err)
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
  const [factories, setFactories] = React.useState<GatewayFactory[]>(cachedConfig?.factories || [])
  const [blockPrefixes, setBlockPrefixes] = React.useState<Record<string, string>>(cachedConfig?.gatewayBlockPrefixes || {})
  const [loading, setLoading] = React.useState(!cachedConfig)

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
      if (!cachedConfig) {
        setLoading(true)
      }
      try {
        const data = await loadGatewayConfig()
        setFactories(data.factories)
        setBlockPrefixes(data.gatewayBlockPrefixes)
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

  // Get the gateway registry from container (fallback for custom configurators)
  const gatewayRegistry = React.useMemo(() => {
    if (container.isBound(coreshopPaymentServiceIds.gatewayConfiguratorRegistry)) {
      return container.get<GatewayRegistry>(coreshopPaymentServiceIds.gatewayConfiguratorRegistry)
    }
    return null
  }, [])

  // Check if factory has a schema-based form or a custom React configurator
  const factoryBlockPrefix = selectedFactory ? blockPrefixes[selectedFactory] : undefined
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
            // Custom React configurator takes precedence
            <GatewayConfigurator
              config={gatewayConfig?.decryptedConfig ?? {}}
              onChange={handleConfigChange}
            />
          ) : factoryBlockPrefix ? (
            // Schema-based form from backend FormType
            <SchemaForm
              blockPrefix={factoryBlockPrefix}
              data={gatewayConfig?.decryptedConfig ?? {}}
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

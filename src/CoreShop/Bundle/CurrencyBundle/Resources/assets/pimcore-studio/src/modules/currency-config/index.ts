/**
 * CoreShop CurrencyBundle Studio Plugin
 *
 * Currency configuration for price formatting.
 * Mirrors the ExtJS pimcore.globalmanager coreshop.currency.* values.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { useState, useEffect, useCallback, useMemo } from 'react'

export interface CurrencyConfig {
  decimalPrecision: number
  decimalFactor: number
}

const DEFAULT_CONFIG: CurrencyConfig = {
  decimalPrecision: 2,
  decimalFactor: 100
}

// Module-level cache
let cachedConfig: CurrencyConfig | null = null
let loadPromise: Promise<CurrencyConfig> | null = null

/**
 * Load currency configuration from backend
 */
const loadConfig = async (): Promise<CurrencyConfig> => {
  if (cachedConfig) {
    return cachedConfig
  }

  if (loadPromise) {
    return loadPromise
  }

  loadPromise = (async () => {
    try {
      const response = await fetch('/pimcore-studio/api/coreshop/currencies/get-config')
      if (!response.ok) {
        console.warn('[CoreShop] Failed to load currency config, using defaults')
        cachedConfig = DEFAULT_CONFIG
        return DEFAULT_CONFIG
      }
      const data = await response.json()
      cachedConfig = {
        decimalPrecision: data.decimal_precision ?? DEFAULT_CONFIG.decimalPrecision,
        decimalFactor: data.decimal_factor ?? DEFAULT_CONFIG.decimalFactor
      }
      return cachedConfig
    } catch (error) {
      console.warn('[CoreShop] Error loading currency config:', error)
      cachedConfig = DEFAULT_CONFIG
      return DEFAULT_CONFIG
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

/**
 * Initialize currency config (call once on startup)
 */
export const initCurrencyConfig = async (): Promise<void> => {
  await loadConfig()
}

/**
 * Hook to get currency configuration
 *
 * Usage:
 * const { toDisplayPrice, toIntegerPrice, decimalPrecision } = useCurrencyConfig()
 */
export const useCurrencyConfig = () => {
  const [config, setConfig] = useState<CurrencyConfig>(cachedConfig ?? DEFAULT_CONFIG)
  const [loading, setLoading] = useState(!cachedConfig)

  useEffect(() => {
    if (cachedConfig) {
      setConfig(cachedConfig)
      setLoading(false)
      return
    }

    void (async () => {
      const loadedConfig = await loadConfig()
      setConfig(loadedConfig)
      setLoading(false)
    })()
  }, [])

  const toDisplayPrice = useCallback((integerPrice: number | undefined | null): number => {
    if (integerPrice === undefined || integerPrice === null) {
      return 0
    }
    return integerPrice / config.decimalFactor
  }, [config.decimalFactor])

  const toIntegerPrice = useCallback((displayPrice: number | undefined | null): number => {
    if (displayPrice === undefined || displayPrice === null) {
      return 0
    }
    return Math.round(displayPrice * config.decimalFactor)
  }, [config.decimalFactor])

  return useMemo(() => ({
    loading,
    decimalPrecision: config.decimalPrecision,
    decimalFactor: config.decimalFactor,
    toDisplayPrice,
    toIntegerPrice
  }), [loading, config.decimalPrecision, config.decimalFactor, toDisplayPrice, toIntegerPrice])
}

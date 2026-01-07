/**
 * CoreShop OrderBundle - Base Step Component
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useEffect } from 'react'
import { Card, Form, Row, Col, Select } from 'antd'
import { useTranslation } from 'react-i18next'
import { StoreSelect } from '@coreshop/store/src/components/StoreSelect'
import type { OrderCreationStepConfig, OrderCreationState, OrderCreationStepProps } from '../types'

// Module-level cache for currencies
let currencyCache: Array<{ value: number; label: string }> | null = null
let currencyLoadPromise: Promise<Array<{ value: number; label: string }>> | null = null

const loadCurrencies = async (): Promise<Array<{ value: number; label: string }>> => {
  if (currencyCache) return currencyCache
  if (currencyLoadPromise) return currencyLoadPromise

  currencyLoadPromise = (async () => {
    try {
      const res = await fetch('/pimcore-studio/api/coreshop/currencies/list', {
        credentials: 'same-origin'
      })
      const data = await res.json()
      const currencies = data.data || data || []
      const result = currencies.map((c: { id: number; name?: string; isoCode?: string }) => ({
        value: c.id,
        label: c.name || c.isoCode || `#${c.id}`
      }))
      currencyCache = result
      return result
    } catch (err) {
      console.error('Failed to load currencies:', err)
      return []
    } finally {
      currencyLoadPromise = null
    }
  })()

  return currencyLoadPromise
}

// Module-level cache for locales
let localeCache: Array<{ value: string; label: string }> | null = null
let localeLoadPromise: Promise<Array<{ value: string; label: string }>> | null = null

const loadLocales = async (): Promise<Array<{ value: string; label: string }>> => {
  if (localeCache) return localeCache
  if (localeLoadPromise) return localeLoadPromise

  localeLoadPromise = (async () => {
    try {
      // Use Pimcore Studio settings API to get valid languages
      const res = await fetch('/pimcore-studio/api/settings', {
        credentials: 'same-origin'
      })
      const data = await res.json()
      const validLanguages = Array.isArray(data?.validLanguages) ? data.validLanguages : []
      const result = validLanguages.map((lang: string) => ({
        value: lang,
        label: lang.toUpperCase()
      }))
      localeCache = result
      return result
    } catch (err) {
      console.error('Failed to load locales:', err)
      // Fallback to common locales
      return [
        { value: 'en', label: 'EN' },
        { value: 'de', label: 'DE' }
      ]
    } finally {
      localeLoadPromise = null
    }
  })()

  return localeLoadPromise
}

const BaseStepComponent: React.FC<OrderCreationStepProps> = ({ state, dispatch, triggerPreview }) => {
  const { t } = useTranslation()

  const [currencies, setCurrencies] = React.useState<Array<{ value: number; label: string }>>(
    currencyCache || []
  )
  const [locales, setLocales] = React.useState<Array<{ value: string; label: string }>>(
    localeCache || []
  )
  const [loadingCurrencies, setLoadingCurrencies] = React.useState(!currencyCache)
  const [loadingLocales, setLoadingLocales] = React.useState(!localeCache)

  useEffect(() => {
    void (async () => {
      if (!currencyCache) setLoadingCurrencies(true)
      try {
        const opts = await loadCurrencies()
        setCurrencies(opts)
      } finally {
        setLoadingCurrencies(false)
      }
    })()

    void (async () => {
      if (!localeCache) setLoadingLocales(true)
      try {
        const opts = await loadLocales()
        setLocales(opts)
      } finally {
        setLoadingLocales(false)
      }
    })()
  }, [])

  const handleChange = (field: string, value: number | string | null): void => {
    dispatch({ type: 'UPDATE_FORM_DATA', payload: { [field]: value } })
    // Trigger preview after form changes
    setTimeout(triggerPreview, 0)
  }

  return (
    <Card
      title={t('coreshop_order_creation_base', { defaultValue: 'Base Settings' })}
      size="small"
    >
      <Form layout="vertical">
        <Row gutter={16}>
          <Col span={8}>
            <Form.Item
              label={t('coreshop_store', { defaultValue: 'Store' })}
              required
            >
              <StoreSelect
                value={state.formData.store ?? undefined}
                onChange={(value) => handleChange('store', value as number)}
                style={{ width: '100%' }}
                placeholder={t('coreshop_select_store', { defaultValue: 'Select Store' })}
              />
            </Form.Item>
          </Col>
          <Col span={8}>
            <Form.Item
              label={t('coreshop_currency', { defaultValue: 'Currency' })}
              required
            >
              <Select
                value={state.formData.currency ?? undefined}
                onChange={(value) => handleChange('currency', value)}
                options={currencies}
                loading={loadingCurrencies}
                style={{ width: '100%' }}
                placeholder={t('coreshop_select_currency', { defaultValue: 'Select Currency' })}
                showSearch
                filterOption={(input, option) =>
                  (option?.label ?? '').toLowerCase().includes(input.toLowerCase())
                }
              />
            </Form.Item>
          </Col>
          <Col span={8}>
            <Form.Item
              label={t('coreshop_locale', { defaultValue: 'Locale' })}
              required
            >
              <Select
                value={state.formData.localeCode ?? undefined}
                onChange={(value) => handleChange('localeCode', value)}
                options={locales}
                loading={loadingLocales}
                style={{ width: '100%' }}
                placeholder={t('coreshop_select_locale', { defaultValue: 'Select Locale' })}
                showSearch
                filterOption={(input, option) =>
                  (option?.label ?? '').toLowerCase().includes(input.toLowerCase())
                }
              />
            </Form.Item>
          </Col>
        </Row>
      </Form>
    </Card>
  )
}

// Step configuration
export const BaseStepConfig: OrderCreationStepConfig = {
  key: 'base',
  label: 'coreshop_order_creation_base',
  icon: 'coreshop_icon_localization',
  priority: 20,
  component: BaseStepComponent,

  isValid: (state: OrderCreationState) => {
    return Boolean(
      state.formData.store && state.formData.currency && state.formData.localeCode
    )
  },

  getValues: (state: OrderCreationState) => ({
    store: state.formData.store,
    currency: state.formData.currency,
    localeCode: state.formData.localeCode
  })
}

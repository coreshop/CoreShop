/**
 * CoreShop AddressBundle - Country Salutation Widget
 *
 * Linked Country + Salutation combo where salutation options
 * are loaded based on the selected country.
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
import { Form, Select } from 'antd'
import { useTranslation } from 'react-i18next'
import { countryApi, type CountryDetail } from '../modules/countries/api'
import { loadCountries } from './CountrySelect'

type Option = { value: string, label: string }

export interface CountrySalutationProps {
  countryValue?: number
  salutationValue?: string
  onCountryChange?: (value: number | undefined) => void
  onSalutationChange?: (value: string | undefined) => void
  countryName?: string
  salutationName?: string
  countryLabel?: string
  salutationLabel?: string
  disabled?: boolean
}

export const CountrySalutation: React.FC<CountrySalutationProps> = ({
  countryValue,
  salutationValue,
  onCountryChange,
  onSalutationChange,
  countryName = 'country',
  salutationName = 'salutation',
  countryLabel,
  salutationLabel,
  disabled
}) => {
  const { t } = useTranslation()
  const [countryOptions, setCountryOptions] = React.useState<Array<{ value: number, label: string }>>([])
  const [countryLoading, setCountryLoading] = React.useState(true)
  const [salutationOptions, setSalutationOptions] = React.useState<Option[]>([])
  const [salutationLoading, setSalutationLoading] = React.useState(false)

  // Load country list
  React.useEffect(() => {
    void (async () => {
      try {
        const opts = await loadCountries()
        setCountryOptions(opts)
      } finally {
        setCountryLoading(false)
      }
    })()
  }, [])

  // Load salutations when country changes
  React.useEffect(() => {
    if (!countryValue) {
      setSalutationOptions([])
      return
    }

    void (async () => {
      setSalutationLoading(true)
      try {
        const res = await countryApi.get(countryValue)
        const detail = res.data as CountryDetail
        const salutations = detail.salutations ?? []
        setSalutationOptions(
          salutations.map((s) => ({
            value: s,
            label: t(`coreshop_salutation_${s}`, { defaultValue: s })
          }))
        )
      } catch (err) {
        console.error('Failed to load country salutations:', err)
        setSalutationOptions([])
      } finally {
        setSalutationLoading(false)
      }
    })()
  }, [countryValue, t])

  return (
    <>
      <Form.Item
        label={countryLabel ?? t('coreshop_country', { defaultValue: 'Country' })}
        name={countryName}
      >
        <Select
          loading={countryLoading}
          options={countryOptions}
          value={countryValue}
          onChange={(val) => {
            onCountryChange?.(val)
            // Reset salutation when country changes
            onSalutationChange?.(undefined)
          }}
          placeholder={t('coreshop.ui.select', { defaultValue: 'Select' })}
          disabled={disabled}
          allowClear
          showSearch
          optionFilterProp="label"
        />
      </Form.Item>
      <Form.Item
        label={salutationLabel ?? t('coreshop_country_salutation', { defaultValue: 'Salutation' })}
        name={salutationName}
      >
        <Select
          loading={salutationLoading}
          options={salutationOptions}
          value={salutationValue}
          onChange={(val) => onSalutationChange?.(val)}
          placeholder={t('coreshop.ui.select', { defaultValue: 'Select' })}
          disabled={disabled || !countryValue || salutationOptions.length === 0}
          allowClear
        />
      </Form.Item>
    </>
  )
}
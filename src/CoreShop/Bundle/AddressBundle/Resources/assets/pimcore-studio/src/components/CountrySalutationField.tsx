/**
 * CoreShop AddressBundle - Country Salutation Field (without Form.Item)
 *
 * Linked Country + Salutation selects for use in FormBuilder context.
 * When a country is selected, the salutation dropdown loads that
 * country's available salutations.
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
import { Select, Space } from 'antd'
import { useTranslation } from 'react-i18next'
import { countryApi, type CountryDetail } from '../modules/countries/api'
import { loadCountries } from './CountrySelect'

type Option = { value: string, label: string }

export interface CountrySalutationFieldProps {
  value?: { country?: number, salutation?: string }
  onChange?: (value: { country?: number, salutation?: string }) => void
  disabled?: boolean
}

export const CountrySalutationField: React.FC<CountrySalutationFieldProps> = ({
  value,
  onChange,
  disabled
}) => {
  const { t } = useTranslation()
  const [countryOptions, setCountryOptions] = React.useState<Array<{ value: number, label: string }>>([])
  const [countryLoading, setCountryLoading] = React.useState(true)
  const [salutationOptions, setSalutationOptions] = React.useState<Option[]>([])
  const [salutationLoading, setSalutationLoading] = React.useState(false)

  const countryValue = value?.country
  const salutationValue = value?.salutation

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
    <Space direction="vertical" style={{ width: '100%' }}>
      <Select
        loading={countryLoading}
        options={countryOptions}
        value={countryValue}
        onChange={(val) => {
          onChange?.({ country: val, salutation: undefined })
        }}
        placeholder={t('coreshop_country', { defaultValue: 'Country' })}
        disabled={disabled}
        allowClear
        showSearch
        optionFilterProp="label"
      />
      <Select
        loading={salutationLoading}
        options={salutationOptions}
        value={salutationValue}
        onChange={(val) => {
          onChange?.({ ...value, salutation: val })
        }}
        placeholder={t('coreshop_country_salutation', { defaultValue: 'Salutation' })}
        disabled={disabled || !countryValue || salutationOptions.length === 0}
        allowClear
      />
    </Space>
  )
}

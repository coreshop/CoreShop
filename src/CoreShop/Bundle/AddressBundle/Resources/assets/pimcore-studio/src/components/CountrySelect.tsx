import React from 'react'
import { Form, Select } from 'antd'
import { useTranslation } from 'react-i18next'
import { countryApi } from '../modules/countries/api'

type Option = { value: number, label: string }

// Module-level cache to avoid multiple API calls
let cachedOptions: Option[] | null = null
let loadPromise: Promise<Option[]> | null = null

const loadCountries = async (): Promise<Option[]> => {
  // Return cached data if available
  if (cachedOptions) {
    return cachedOptions
  }

  // If already loading, return the existing promise
  if (loadPromise) {
    return loadPromise
  }

  // Start new load
  loadPromise = (async () => {
    try {
      const rows = await countryApi.list()
      const list = Array.isArray(rows) ? rows : []
      cachedOptions = list
        .map((r: any) => ({ value: r.id, label: r.name ?? r.isoCode ?? String(r.id) }))
        .filter((o: any) => o.value != null && o.label)
      return cachedOptions
    } catch (err) {
      console.error('Failed to load countries:', err)
      return []
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

// Export function to clear cache if needed
export const clearCountryCache = () => {
  cachedOptions = null
  loadPromise = null
}

export interface CountrySelectProps {
  name?: string
  label?: string
  labelKey?: string
  placeholder?: string
  disabled?: boolean
  allowClear?: boolean
  size?: 'small' | 'middle' | 'large'
  className?: string
  style?: React.CSSProperties
}

export const CountrySelect: React.FC<CountrySelectProps> = ({
  name = 'country',
  label,
  labelKey,
  placeholder,
  disabled,
  allowClear,
  size,
  className,
  style,
}) => {
  const [options, setOptions] = React.useState<Option[]>(cachedOptions || [])
  const [loading, setLoading] = React.useState(!cachedOptions)
  const { t } = useTranslation()

  React.useEffect(() => {
    void (async () => {
      if (!cachedOptions) {
        setLoading(true)
      }
      try {
        const opts = await loadCountries()
        setOptions(opts)
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  const computedLabel = label ?? (labelKey ? t(labelKey) : t('coreshop_country', { defaultValue: 'Country' }))
  const computedPlaceholder = placeholder ?? t('coreshop.ui.select', { defaultValue: 'Select' })

  return (
    <Form.Item label={ computedLabel } name={ name }>
      <Select
        loading={ loading }
        options={ options }
        placeholder={ computedPlaceholder }
        disabled={ disabled }
        allowClear={ allowClear }
        size={ size }
        showSearch
        className={ className }
        style={ style }
        optionFilterProp='label'
      />
    </Form.Item>
  )
}


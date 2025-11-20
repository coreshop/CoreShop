import React from 'react'
import { Select } from 'antd'
import type { SelectProps } from 'antd'
import { useTranslation } from 'react-i18next'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'
import { currencyApi } from '../modules/currencies/api'

type Option = { value: number, label: string }

// Module-level cache
let cachedOptions: Option[] | null = null
let loadPromise: Promise<Option[]> | null = null

const loadCurrencies = async (): Promise<Option[]> => {
  if (cachedOptions) return cachedOptions
  if (loadPromise) return loadPromise

  loadPromise = (async () => {
    try {
      const rows = await currencyApi.list()
      cachedOptions = (Array.isArray(rows) ? rows : [])
        .map((r: any) => ({ value: r.id, label: r.name ?? r.isoCode ?? r.code ?? String(r.id) }))
        .filter((o: any) => o.value != null && o.label)
      return cachedOptions
    } catch (err) {
      console.error('Failed to load currencies:', err)
      return []
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

export const clearCurrencyCache = () => {
  cachedOptions = null
  loadPromise = null
}

export const CurrencySelectField: React.FC<SelectProps> = (props) => {
  const [options, setOptions] = React.useState<Option[]>(cachedOptions || [])
  const [loading, setLoading] = React.useState(!cachedOptions)
  const { t } = useTranslation()

  React.useEffect(() => {
    void (async () => {
      if (!cachedOptions) setLoading(true)
      try {
        const opts = await loadCurrencies()
        setOptions(opts)
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  return (
    <DroppableEntity
      accept='coreshop:currency'
      isValidData={(info) => typeof info?.data?.id === 'number'}
      onDrop={(info) => {
        if (props.onChange && info?.data?.id) {
          const event = { target: { value: info.data.id } } as any
          props.onChange(info.data.id, event)
        }
      }}
    >
      <Select
        {...props}
        loading={loading}
        options={options}
        placeholder={props.placeholder ?? t('coreshop.ui.select', { defaultValue: 'Select' })}
        showSearch
        optionFilterProp="label"
      />
    </DroppableEntity>
  )
}

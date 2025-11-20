import React from 'react'
import { Select } from 'antd'
import type { SelectProps } from 'antd'
import { useTranslation } from 'react-i18next'
import { storeApi } from '../modules/stores/api'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'

type Option = { value: number, label: string }

let cachedOptions: Option[] | null = null
let loadPromise: Promise<Option[]> | null = null

const loadStores = async (): Promise<Option[]> => {
  if (cachedOptions) return cachedOptions
  if (loadPromise) return loadPromise

  loadPromise = (async () => {
    try {
      const rows = await storeApi.list()
      cachedOptions = (Array.isArray(rows) ? rows : [])
        .map((r: any) => ({ value: r.id, label: r.name ?? String(r.id) }))
        .filter((o: any) => o.value != null && o.label)
      return cachedOptions
    } catch (err) {
      console.error('Failed to load stores:', err)
      return []
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

export const clearStoreCache = () => {
  cachedOptions = null
  loadPromise = null
}

export const StoreMultiSelectField: React.FC<SelectProps<number[]>> = (props) => {
  const [options, setOptions] = React.useState<Option[]>(cachedOptions || [])
  const [loading, setLoading] = React.useState(!cachedOptions)
  const { t } = useTranslation()

  React.useEffect(() => {
    void (async () => {
      if (!cachedOptions) setLoading(true)
      try {
        const opts = await loadStores()
        setOptions(opts)
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  return (
    <DroppableEntity
      accept='coreshop:store'
      isValidData={(info) => typeof info?.data?.id === 'number'}
      onDrop={(info) => {
        if (props.onChange && info?.data?.id) {
          const currentValue = props.value || []
          const newValue = Array.isArray(currentValue)
            ? [...currentValue, info.data.id]
            : [info.data.id]
          const event = { target: { value: newValue } } as any
          props.onChange(newValue, event)
        }
      }}
    >
      <Select
        {...props}
        mode="multiple"
        loading={loading}
        options={options}
        placeholder={props.placeholder ?? t('coreshop.ui.select', { defaultValue: 'Select' })}
        showSearch
        optionFilterProp="label"
      />
    </DroppableEntity>
  )
}

import React from 'react'
import { Select } from 'antd'
import type { SelectProps } from 'antd'
import type { SelectOption } from '../utils/createOptionsLoader'

export interface EntitySelectProps extends Omit<SelectProps, 'options' | 'loading'> {
  loadOptions: () => Promise<SelectOption[]>
  getCachedOptions?: () => SelectOption[] | null
}

export const EntitySelect: React.FC<EntitySelectProps> = ({
  loadOptions,
  getCachedOptions,
  ...selectProps
}) => {
  const cached = getCachedOptions?.() ?? null
  const [options, setOptions] = React.useState<SelectOption[]>(cached || [])
  const [loading, setLoading] = React.useState(!cached)

  React.useEffect(() => {
    void (async () => {
      if (!getCachedOptions?.()) setLoading(true)
      try {
        const opts = await loadOptions()
        setOptions(opts)
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  return (
    <Select
      {...selectProps}
      loading={loading}
      options={options}
      showSearch
      optionFilterProp="label"
    />
  )
}

export interface EntityMultiSelectProps extends Omit<SelectProps<number[]>, 'options' | 'loading' | 'mode'> {
  loadOptions: () => Promise<SelectOption[]>
  getCachedOptions?: () => SelectOption[] | null
}

export const EntityMultiSelect: React.FC<EntityMultiSelectProps> = ({
  loadOptions,
  getCachedOptions,
  ...selectProps
}) => {
  const cached = getCachedOptions?.() ?? null
  const [options, setOptions] = React.useState<SelectOption[]>(cached || [])
  const [loading, setLoading] = React.useState(!cached)

  React.useEffect(() => {
    void (async () => {
      if (!getCachedOptions?.()) setLoading(true)
      try {
        const opts = await loadOptions()
        setOptions(opts)
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  return (
    <Select
      {...selectProps}
      mode="multiple"
      loading={loading}
      options={options}
      showSearch
      optionFilterProp="label"
      maxTagCount="responsive"
    />
  )
}

import React from 'react'
import { Form } from 'antd'
import { useTranslation } from 'react-i18next'
import { EntityMultiSelect } from '@coreshop/resource/src/components/EntitySelect'
import { loadStores, getStoreCache, clearStoreCache } from './StoreSelect'

export { loadStores, clearStoreCache }

export interface StoreMultiSelectProps {
  name?: string
  label?: string
  labelKey?: string
  placeholder?: string
  disabled?: boolean
  size?: 'small' | 'middle' | 'large'
  className?: string
  style?: React.CSSProperties
  value?: number[]
  onChange?: (value: number[]) => void
}

export const StoreMultiSelect: React.FC<StoreMultiSelectProps> = ({
  name = 'stores',
  label,
  labelKey,
  placeholder,
  disabled,
  value,
  onChange,
}) => {
  const { t } = useTranslation()

  const computedLabel = label ?? (labelKey ? t(labelKey) : t('coreshop_stores', { defaultValue: 'Stores' }))

  return (
    <Form.Item label={computedLabel} name={name}>
      <EntityMultiSelect
        value={value}
        onChange={onChange}
        disabled={disabled}
        placeholder={placeholder}
        loadOptions={loadStores}
        getCachedOptions={getStoreCache}
      />
    </Form.Item>
  )
}

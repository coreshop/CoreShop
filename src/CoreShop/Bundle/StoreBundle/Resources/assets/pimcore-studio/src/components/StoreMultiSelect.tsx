import React from 'react'
import { Form, Select } from 'antd'
import { useTranslation } from 'react-i18next'
import { storeApi } from '../modules/stores/api'

type Option = { value: number, label: string }

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

// Module-level cache to avoid multiple API calls
let cachedOptions: Option[] | null = null
let loadPromise: Promise<Option[]> | null = null

// Export for use in StoresCondition
export const loadStores = async (): Promise<Option[]> => {
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
            const rows = await storeApi.list()
            const list = Array.isArray(rows) ? rows : []
            cachedOptions = list
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

// Export function to clear cache if needed
export const clearStoreCache = () => {
    cachedOptions = null
    loadPromise = null
}

export const StoreMultiSelect: React.FC<StoreMultiSelectProps> = ({
    name = 'stores',
    label,
    labelKey,
    placeholder,
    disabled,
    size,
    className,
    style,
    value,
    onChange,
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
                const opts = await loadStores()
                setOptions(opts)
            } finally {
                setLoading(false)
            }
        })()
    }, [])

    const computedLabel = label ?? (labelKey ? t(labelKey) : t('coreshop_stores', { defaultValue: 'Stores' }))
    const computedPlaceholder = placeholder ?? t('coreshop.ui.select', { defaultValue: 'Select' })

    const selectProps: any = {
        mode: 'multiple' as const,
        options,
        loading,
        placeholder: computedPlaceholder,
        disabled,
        size,
        showSearch: true,
        className,
        style,
        optionFilterProp: 'label',
        maxTagCount: 'responsive' as const,
    }

    // If value/onChange provided, use controlled mode (bypass Form.Item)
    if (value !== undefined || onChange !== undefined) {
        selectProps.value = value
        selectProps.onChange = onChange
    }

    return (
        <Form.Item label={computedLabel} name={name}>
            <Select {...selectProps} />
        </Form.Item>
    )
}

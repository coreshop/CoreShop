import React from 'react'
import { Form, Select } from 'antd'
import { useTranslation } from 'react-i18next'
import { countryApi } from '../modules/countries/api'

type Option = { value: number, label: string }

export interface CountryMultiSelectProps {
    name?: string
    label?: string
    labelKey?: string
    placeholder?: string
    disabled?: boolean
    size?: 'small' | 'middle' | 'large'
    className?: string
    style?: React.CSSProperties
}

export const CountryMultiSelect: React.FC<CountryMultiSelectProps> = ({
    name = 'countries',
    label,
    labelKey,
    placeholder,
    disabled,
    size,
    className,
    style,
}) => {
    const [options, setOptions] = React.useState<Option[]>([])
    const { t } = useTranslation()

    React.useEffect(() => {
        countryApi.list()
            .then((rows: any) => {
                const list = Array.isArray(rows) ? rows : []
                const opts = list
                    .map((r: any) => ({ value: r.id, label: r.name ?? r.isoCode ?? String(r.id) }))
                    .filter((o: any) => o.value != null && o.label)
                setOptions(opts)
            })
            .catch(() => setOptions([]))
    }, [])

    const computedLabel = label ?? (labelKey ? t(labelKey) : t('coreshop_allowed_countries', { defaultValue: 'Allowed Countries' }))
    const computedPlaceholder = placeholder ?? t('coreshop.ui.select', { defaultValue: 'Select' })

    return (
        <Form.Item label={computedLabel} name={name}>
            <Select
                mode="multiple"
                options={options}
                placeholder={computedPlaceholder}
                disabled={disabled}
                size={size}
                showSearch
                className={className}
                style={style}
                optionFilterProp='label'
                maxTagCount='responsive'
            />
        </Form.Item>
    )
}

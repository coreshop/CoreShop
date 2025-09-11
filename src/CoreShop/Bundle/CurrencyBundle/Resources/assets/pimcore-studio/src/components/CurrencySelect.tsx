import React from 'react'
import {Form, Select} from 'antd'
import {DroppableEntity} from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'
import {useTranslation} from 'react-i18next'
import {currencyApi} from '../modules/currencies/api'

type Option = { value: number, label: string }

export interface CurrencySelectProps {
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

export const CurrencySelect: React.FC<CurrencySelectProps> = ({
                                                                  name = 'currency',
                                                                  label,
                                                                  labelKey,
                                                                  placeholder,
                                                                  disabled,
                                                                  allowClear,
                                                                  size,
                                                                  className,
                                                                  style,
                                                              }) => {
    const [options, setOptions] = React.useState<Option[]>([])
    const {t} = useTranslation()
    const form = (Form as any).useFormInstance ? (Form as any).useFormInstance() : undefined

    React.useEffect(() => {
        currencyApi.list()
            .then((rows) => {
                const opts = (Array.isArray(rows) ? rows : [])
                    .map((r: any) => ({value: r.id, label: r.name ?? r.isoCode ?? r.code ?? String(r.id)}))
                    .filter((o: any) => o.value != null && o.label)
                setOptions(opts)
            })
            .catch(() => {
                setOptions([])
            })
    }, [])

    const computedLabel = label ?? (labelKey ? t(labelKey) : t('coreshop_country_currency', {defaultValue: 'Currency'}))
    const computedPlaceholder = placeholder ?? t('coreshop.ui.select', {defaultValue: 'Select'})

    return (
        <DroppableEntity
            accept='coreshop:currency'
            className={'test-droppable'}
            isValidData={ (info) => typeof info?.data?.id === 'number' }
            onDrop={(info) => {
                const id = info?.data?.id
                if (typeof id === 'number') {
                    form?.setFieldValue?.(name as any, id)
                }
            }}
        >
            <Form.Item label={computedLabel}>
                <Form.Item name={name} noStyle>
                    <Select
                        options={options}
                        placeholder={computedPlaceholder}
                        disabled={disabled ?? options.length === 0}
                        allowClear={allowClear}
                        size={size}
                        showSearch
                        className={className}
                        style={style}
                    />
                </Form.Item>
            </Form.Item>
        </DroppableEntity>
    )
}

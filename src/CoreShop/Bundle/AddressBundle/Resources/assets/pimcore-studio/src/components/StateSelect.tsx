import React from 'react'
import { Form, Select } from 'antd'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'
import { useTranslation } from 'react-i18next'
import { stateApi } from '../modules/states/api'

type Option = { value: number, label: string }

export interface StateSelectProps {
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

export const StateSelect: React.FC<StateSelectProps> = ({
  name = 'state',
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
  const { t } = useTranslation()

  React.useEffect(() => {
    stateApi.list()
      .then((rows: any) => {
        const list = Array.isArray(rows) ? rows : []
        const opts = list
          .map((r: any) => ({
            value: r.id,
            label: r.countryName ? `${r.name} (${r.countryName})` : (r.name ?? String(r.id))
          }))
          .filter((o: any) => o.value != null && o.label)
        setOptions(opts)
      })
      .catch(() => setOptions([]))
  }, [])

  const computedLabel = label ?? (labelKey ? t(labelKey) : t('coreshop_state', { defaultValue: 'State' }))
  const computedPlaceholder = placeholder ?? t('coreshop.ui.select', { defaultValue: 'Select' })

  const form = (Form as any).useFormInstance ? (Form as any).useFormInstance() : undefined
  return (
    <Form.Item label={ computedLabel }>
      <DroppableEntity
        accept='coreshop:state'
        isValidData={ (info) => typeof info?.data?.id === 'number' }
        onDrop={ (info) => {
          const id = info?.data?.id
          if (typeof id === 'number') form?.setFieldValue?.(name as any, id)
        } }
      >
        <Form.Item name={ name } noStyle>
          <Select
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
      </DroppableEntity>
    </Form.Item>
  )
}

/**
 * CoreShop IndexBundle Select Filter Condition
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
import { Form, Input, Select } from 'antd'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import type { ConditionProps } from '../types'
import { filterApi } from '../api'
import type { FieldValue } from '../types'
import { QuantityUnitSelect } from '../../shared/QuantityUnitSelect'
import { getErrorMessage } from '@coreshop/resource/src/entities'

export const SelectCondition: React.FC<ConditionProps> = ({
  data,
  onChange,
  indexId
}) => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const [fieldOptions, setFieldOptions] = React.useState<Array<{ label: string, value: string }>>([])
  const [valueOptions, setValueOptions] = React.useState<FieldValue[]>([])
  const [loading, setLoading] = React.useState(false)

  React.useEffect(() => {
    if (!indexId) return

    setLoading(true)
    filterApi.getFieldsForIndex(indexId)
      .then(fields => {
        setFieldOptions(fields.map(f => ({ label: f.name, value: f.name })))
      })
      .catch(err => void messageApi.error(getErrorMessage(err, 'Failed to load fields')))
      .finally(() => setLoading(false))
  }, [indexId])

  React.useEffect(() => {
    if (!indexId || !data.configuration?.field) return

    filterApi.getValuesForFilterField(indexId, data.configuration.field)
      .then(setValueOptions)
      .catch(err => void messageApi.error(getErrorMessage(err, 'Failed to load field values')))
  }, [indexId, data.configuration?.field])

  return (
    <Form layout="vertical">
      <Form.Item label={t('coreshop_label', { defaultValue: 'Label' })}>
        <Input
          value={data.label}
          onChange={(e) => onChange({ label: e.target.value })}
        />
      </Form.Item>

      <Form.Item label={t('coreshop_filters_quantityUnit', { defaultValue: 'Quantity Value' })}>
        <QuantityUnitSelect
          value={data.quantityUnit ?? "0"}
          onChange={(value) => onChange({ quantityUnit: value })}
        />
      </Form.Item>

      <Form.Item label={t('coreshop_filters_field', { defaultValue: 'Field' })} required>
        <Select
          value={data.configuration?.field}
          onChange={(value) => onChange({
            configuration: { ...data.configuration, field: value }
          })}
          options={fieldOptions}
          loading={loading}
          showSearch
        />
      </Form.Item>

      <Form.Item label={t('coreshop_filters_select', { defaultValue: 'Select' })}>
        <Select
          value={data.configuration?.preSelect}
          onChange={(value) => onChange({
            configuration: { ...data.configuration, preSelect: value }
          })}
          options={valueOptions.map(v => ({ label: v.value, value: v.key }))}
          allowClear
        />
      </Form.Item>
    </Form>
  )
}

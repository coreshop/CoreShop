/**
 * CoreShop IndexBundle Search Filter Condition
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
import { QuantityUnitSelect } from '../../shared/QuantityUnitSelect'
import { getErrorMessage } from '@coreshop/resource/src/entities'

export const SearchCondition: React.FC<ConditionProps> = ({
  data,
  onChange,
  indexId
}) => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const [fieldOptions, setFieldOptions] = React.useState<Array<{ label: string, value: string }>>([])
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

      <Form.Item label={t('coreshop_filters_search_condition_name', { defaultValue: 'Search name' })}>
        <Input
          value={data.configuration?.name}
          onChange={(e) => onChange({
            configuration: { ...data.configuration, name: e.target.value }
          })}
        />
      </Form.Item>

      <Form.Item label={t('coreshop_filters_fields', { defaultValue: 'Fields' })} required>
        <Select
          mode="multiple"
          value={data.configuration?.fields ?? []}
          onChange={(values) => onChange({
            configuration: { ...data.configuration, fields: values }
          })}
          options={fieldOptions}
          loading={loading}
        />
      </Form.Item>

      <Form.Item label={t('coreshop_filters_search_term', { defaultValue: 'Search term' })}>
        <Input
          value={data.configuration?.searchTerm}
          onChange={(e) => onChange({
            configuration: { ...data.configuration, searchTerm: e.target.value }
          })}
        />
      </Form.Item>

      <Form.Item label={t('coreshop_filters_search_patterns_concatenator', { defaultValue: 'Choose concatenator' })}>
        <Select
          value={data.configuration?.concatenator ?? 'OR'}
          onChange={(value) => onChange({
            configuration: { ...data.configuration, concatenator: value }
          })}
          options={[
            { label: 'OR', value: 'OR' },
            { label: 'AND', value: 'AND' }
          ]}
        />
      </Form.Item>

      <Form.Item label={t('coreshop_filters_search_patterns_label', { defaultValue: 'Choose pattern' })}>
        <Select
          value={data.configuration?.pattern ?? 'both'}
          onChange={(value) => onChange({
            configuration: { ...data.configuration, pattern: value }
          })}
          options={[
            { label: t('coreshop_filters_search_patterns_both', { defaultValue: 'Contains' }), value: 'both' },
            { label: t('coreshop_filters_search_patterns_right', { defaultValue: 'Begins with' }), value: 'left' },
            { label: t('coreshop_filters_search_patterns_left', { defaultValue: 'Ends with' }), value: 'right' }
          ]}
        />
      </Form.Item>
    </Form>
  )
}

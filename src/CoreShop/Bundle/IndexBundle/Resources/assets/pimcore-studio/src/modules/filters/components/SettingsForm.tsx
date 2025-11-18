/**
 * CoreShop IndexBundle Filter Settings Form
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
import { Form, Input, InputNumber, Select } from 'antd'
import { useTranslation } from 'react-i18next'
import type { Filter } from '../types'
import { IndexSelect } from '../../shared/IndexSelect'

interface SettingsFormProps {
  filter: Filter
  onChange: (filter: Filter) => void
}

export const SettingsForm: React.FC<SettingsFormProps> = ({
  filter,
  onChange
}) => {
  const { t } = useTranslation()
  return (
    <div style={{ padding: 24 }}>
      <Form layout="vertical">
        <Form.Item
          label={t('coreshop_name', { defaultValue: 'Name' })}
          required
        >
          <Input
            value={filter.name}
            onChange={(e) => onChange({ ...filter, name: e.target.value })}
          />
        </Form.Item>

        <Form.Item
          label={t('coreshop_filters_index', { defaultValue: 'Index' })}
          required
        >
          <IndexSelect
            value={filter.index ?? undefined}
            onChange={(value) => onChange({ ...filter, index: value ?? null })}
          />
        </Form.Item>

        <Form.Item
          label={t('coreshop_filters_order_direction', { defaultValue: 'Order Direction' })}
        >
          <Select
            value={filter.orderDirection ?? 'desc'}
            onChange={(value) => onChange({ ...filter, orderDirection: value })}
            options={[
              { label: t('coreshop_filters_order_desc', { defaultValue: 'Descending' }), value: 'desc' },
              { label: t('coreshop_filters_order_asc', { defaultValue: 'Ascending' }), value: 'asc' }
            ]}
          />
        </Form.Item>

        <Form.Item
          label={t('coreshop_filters_order_key', { defaultValue: 'Order Key' })}
        >
          <Input
            value={filter.orderKey ?? ''}
            onChange={(e) => onChange({ ...filter, orderKey: e.target.value })}
          />
        </Form.Item>

        <Form.Item
          label={t('coreshop_filters_results_per_page', { defaultValue: 'Results Per Page' })}
        >
          <InputNumber
            value={filter.resultsPerPage ?? 10}
            onChange={(value) => onChange({ ...filter, resultsPerPage: value ?? 10 })}
            min={1}
            max={1000}
            style={{ width: '100%' }}
          />
        </Form.Item>
      </Form>
    </div>
  )
}

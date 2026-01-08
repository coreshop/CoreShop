/**
 * CoreShop NotificationBundle Studio Plugin
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
import { Form, Select } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'

export interface AbstractStateConditionProps extends ConditionComponentProps {
  label: string
  states: Array<{ value: string; label: string }>
}

export const AbstractStateCondition: React.FC<AbstractStateConditionProps> = ({
  data,
  onChange,
  label,
  states
}) => {
  const { t } = useTranslation()
  const config = data ?? {}

  const handleChange = (value: string) => {
    onChange({
      ...config,
      state: value
    })
  }

  return (
    <Form.Item label={label}>
      <Select
        value={config.state}
        onChange={handleChange}
        options={states}
        placeholder={t('coreshop_select_state', { defaultValue: 'Select a state' })}
        showSearch
        filterOption={(input, option) =>
          (option?.label ?? '').toLowerCase().includes(input.toLowerCase())
        }
      />
    </Form.Item>
  )
}

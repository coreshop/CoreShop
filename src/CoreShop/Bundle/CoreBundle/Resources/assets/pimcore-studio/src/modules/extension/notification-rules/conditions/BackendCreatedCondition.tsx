/**
 * CoreShop CoreBundle Studio Plugin
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

interface BackendCreatedConditionConfig {
  backendCreated?: boolean
}

/**
 * Backend Created condition for order notification rules
 * Allows filtering by whether the order was created in the backend
 */
export const BackendCreatedCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const backendCreated = data?.backendCreated

  const handleChange = (value: string) => {
    onChange({ ...data, backendCreated: value === 'true' })
  }

  const options = [
    { value: 'true', label: t('coreshop_yes', { defaultValue: 'Yes' }) },
    { value: 'false', label: t('coreshop_no', { defaultValue: 'No' }) }
  ]

  return (
    <Form.Item label={t('coreshop_backend_created', { defaultValue: 'Created in Backend' })}>
      <Select
        value={backendCreated === true ? 'true' : backendCreated === false ? 'false' : undefined}
        onChange={handleChange}
        options={options}
        placeholder={t('coreshop_select', { defaultValue: 'Select' })}
      />
    </Form.Item>
  )
}

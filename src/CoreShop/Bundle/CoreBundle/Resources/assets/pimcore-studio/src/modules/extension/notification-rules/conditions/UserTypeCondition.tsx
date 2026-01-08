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

interface UserTypeConditionConfig {
  userType?: string
}

/**
 * User Type condition for user notification rules
 */
export const UserTypeCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()

  const handleChange = (value: string) => {
    onChange({ ...data, userType: value })
  }

  const options = [
    { value: 'register', label: t('coreshop_user_type_register', { defaultValue: 'Register' }) },
    { value: 'password-reset', label: t('coreshop_user_type_password_reset', { defaultValue: 'Password Reset' }) },
    { value: 'newsletter-double-opt-in', label: t('coreshop_user_type_newsletter_doi', { defaultValue: 'Newsletter Double Opt-In' }) },
    { value: 'newsletter-confirmed', label: t('coreshop_user_type_newsletter_confirmed', { defaultValue: 'Newsletter Confirmed' }) }
  ]

  return (
    <Form.Item label={t('coreshop_user_type', { defaultValue: 'User Type' })}>
      <Select
        value={data?.userType}
        onChange={handleChange}
        options={options}
        placeholder={t('coreshop_select_user_type', { defaultValue: 'Select a user type' })}
      />
    </Form.Item>
  )
}

/**
 * CoreShop RuleBundle Studio Plugin
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
import { Alert } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '../types'

/**
 * EmptyCondition component for conditions that don't require configuration
 * Registered automatically for condition types that ship without a configuration form type
 */
export const EmptyCondition: React.FC<ConditionComponentProps> = () => {
  const { t } = useTranslation()

  return (
    <Alert
      message={t('coreshop_condition_no_configuration', { defaultValue: 'This condition has no configuration.' })}
      type="info"
      showIcon
      style={{ marginTop: 8 }}
    />
  )
}
EmptyCondition.displayName = 'EmptyCondition'

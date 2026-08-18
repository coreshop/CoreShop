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
import type { ActionComponentProps } from '../types'

/**
 * EmptyAction component for actions that don't require configuration
 * Registered automatically for action types that ship without a configuration form type
 */
export const EmptyAction: React.FC<ActionComponentProps> = () => {
  const { t } = useTranslation()

  return (
    <Alert
      message={t('coreshop_action_no_configuration', { defaultValue: 'This action has no configuration.' })}
      type="info"
      showIcon
      style={{ marginTop: 8 }}
    />
  )
}
EmptyAction.displayName = 'EmptyAction'

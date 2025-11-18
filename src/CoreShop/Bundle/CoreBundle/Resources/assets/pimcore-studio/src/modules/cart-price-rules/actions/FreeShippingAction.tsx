/**
 * CoreShop OrderBundle Studio Plugin
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
import type { ActionComponentProps } from '@coreshop/rule/src/rules'

export const FreeShippingAction: React.FC<ActionComponentProps> = () => {
  const { t } = useTranslation()

  return (
    <Alert
      message={t('coreshop_action_freeShipping', { defaultValue: 'Free Shipping' })}
      type="info"
      showIcon
    />
  )
}

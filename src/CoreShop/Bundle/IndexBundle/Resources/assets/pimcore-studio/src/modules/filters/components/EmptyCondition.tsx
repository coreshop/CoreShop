/**
 * CoreShop IndexBundle Empty Filter Condition
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
import type { ConditionProps } from '../types'

/**
 * Empty placeholder condition for unregistered types
 */
export const EmptyCondition: React.FC<ConditionProps> = ({ data }) => {
  const { t } = useTranslation()

  return (
    <Alert
      message={t('coreshop_not_implemented', { defaultValue: 'Not Implemented' })}
      description={t('coreshop_condition_not_implemented', { defaultValue: `This condition type (${data.type}) has not been implemented yet in Studio v2.` })}
      type="info"
      showIcon
    />
  )
}

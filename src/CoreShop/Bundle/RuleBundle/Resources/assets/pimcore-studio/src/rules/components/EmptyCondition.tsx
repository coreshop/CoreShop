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
import type { ConditionComponentProps } from '../types'

/**
 * EmptyCondition component for conditions that don't require configuration
 * Use this for conditions that just need to exist without any settings
 */
export const EmptyCondition: React.FC<ConditionComponentProps> = () => {
  return (
    <Alert
      message="This condition has no configuration options."
      type="info"
      showIcon
      style={{ marginTop: 8 }}
    />
  )
}

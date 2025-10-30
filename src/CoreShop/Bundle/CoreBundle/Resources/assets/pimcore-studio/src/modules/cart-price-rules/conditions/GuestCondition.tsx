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
import { Alert } from 'antd'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'

export const GuestCondition: React.FC<ConditionComponentProps> = () => {
  return (
    <Alert
      message="Guest Customers Only"
      description="This condition applies only to guest customers (not logged in)."
      type="info"
      showIcon
    />
  )
}

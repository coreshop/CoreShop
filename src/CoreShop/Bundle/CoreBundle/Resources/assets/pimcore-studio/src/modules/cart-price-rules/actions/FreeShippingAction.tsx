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
import type { ActionComponentProps } from '@coreshop/rule/src/rules'

export const FreeShippingAction: React.FC<ActionComponentProps> = () => {
  return (
    <Alert
      message="Free Shipping"
      description="This action will provide free shipping for the cart."
      type="info"
      showIcon
    />
  )
}

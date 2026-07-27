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
import type { ActionComponentProps } from '../types'

/**
 * EmptyAction component for actions that don't require configuration
 * Use this for actions that just need to exist without any settings
 */
export const EmptyAction: React.FC<ActionComponentProps> = () => {
  return (
    <Alert
      message="This action has no configuration options."
      type="info"
      showIcon
      style={{ marginTop: 8 }}
    />
  )
}

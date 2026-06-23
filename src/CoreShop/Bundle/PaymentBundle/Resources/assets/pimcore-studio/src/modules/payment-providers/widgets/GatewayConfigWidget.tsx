/**
 * CoreShop PaymentBundle - Gateway Config Widget
 *
 * Schema form widget that renders GatewayConfigPanel for the
 * 'gateway_config' block prefix.
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
import { GatewayConfigPanel } from '../gateways/GatewayConfigPanel'
import type { GatewayConfig } from '../api'

interface GatewayConfigWidgetProps {
  value?: GatewayConfig
  onChange?: (value: GatewayConfig) => void
}

export const GatewayConfigWidget: React.FC<GatewayConfigWidgetProps> = ({
  value,
  onChange,
}) => {
  return (
    <GatewayConfigPanel
      gatewayConfig={value}
      onChange={(gatewayConfig) => onChange?.(gatewayConfig)}
    />
  )
}

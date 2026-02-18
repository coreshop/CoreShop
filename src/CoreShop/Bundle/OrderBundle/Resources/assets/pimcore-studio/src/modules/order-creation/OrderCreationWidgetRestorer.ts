/**
 * CoreShop OrderBundle - Order Creation Widget Restorer
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type { WidgetManagerTabConfig } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import type { AppDispatch } from '@pimcore/studio-ui-bundle/app'

interface WidgetRestorer {
  supports(config: WidgetManagerTabConfig): boolean
  cleanConfig(config: WidgetManagerTabConfig): WidgetManagerTabConfig
  restore(config: WidgetManagerTabConfig, dispatch: AppDispatch): boolean
}

/**
 * Handles persistence and restoration of Order Creation detail widgets.
 * Only the customerId is persisted — form data is fresh on restore.
 */
export class OrderCreationWidgetRestorer implements WidgetRestorer {
  supports(config: WidgetManagerTabConfig): boolean {
    return (
      config.component === 'coreshop-order-creation-detail' &&
      typeof config.config?.customerId === 'number'
    )
  }

  cleanConfig(config: WidgetManagerTabConfig): WidgetManagerTabConfig {
    return {
      ...config,
      config: {
        customerId: config.config?.customerId
      }
    }
  }

  restore(config: WidgetManagerTabConfig, _dispatch: AppDispatch): boolean {
    return typeof config.config?.customerId === 'number' && config.config.customerId > 0
  }
}

export const orderCreationWidgetRestorer = new OrderCreationWidgetRestorer()

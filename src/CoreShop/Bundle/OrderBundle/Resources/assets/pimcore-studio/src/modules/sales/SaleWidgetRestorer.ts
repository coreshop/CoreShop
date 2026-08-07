/**
 * CoreShop OrderBundle - Sale Widget Restorer
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

// WidgetRestorer interface defined locally since the module path is not exported
interface WidgetRestorer {
  supports(config: WidgetManagerTabConfig): boolean
  cleanConfig(config: WidgetManagerTabConfig): WidgetManagerTabConfig
  restore(config: WidgetManagerTabConfig, dispatch: AppDispatch): boolean
}

/**
 * Widget component names that this restorer handles
 */
const SALE_WIDGETS = [
  'coreshop-order-detail',
  'coreshop-cart-detail',
  'coreshop-quote-detail'
] as const

/**
 * SaleWidgetRestorer handles persistence and restoration of
 * Order, Cart, and Quote detail widgets.
 *
 * When the browser is refreshed, Pimcore's WidgetManager restores
 * previously open widgets from localStorage. This restorer ensures
 * that CoreShop sale detail widgets can be properly restored by:
 *
 * 1. Supporting the three sale detail widget types
 * 2. Cleaning the config to only persist essential data (orderId)
 * 3. Allowing restoration when a valid orderId exists
 *
 * The actual data loading is handled by each widget component itself
 * (OrderDetailWidget, CartDetailWidget, QuoteDetailWidget) - they
 * fetch their data from the backend API using the orderId prop.
 */
export class SaleWidgetRestorer implements WidgetRestorer {
  /**
   * Check if this restorer can handle the given widget config
   */
  supports(config: WidgetManagerTabConfig): boolean {
    return (
      config.component !== undefined &&
      SALE_WIDGETS.includes(config.component as typeof SALE_WIDGETS[number]) &&
      typeof config.config?.orderId === 'number'
    )
  }

  /**
   * Clean the config before persisting to localStorage.
   * Only keep the orderId - everything else will be reloaded
   * from the backend when the widget is restored.
   */
  cleanConfig(config: WidgetManagerTabConfig): WidgetManagerTabConfig {
    return {
      ...config,
      config: {
        orderId: config.config?.orderId
      }
    }
  }

  /**
   * Restore the widget from persisted config.
   * The widgets themselves handle data loading, so we just need
   * to verify that we have a valid orderId and return true to
   * allow the widget to be rendered.
   */
  restore(config: WidgetManagerTabConfig, _dispatch: AppDispatch): boolean {
    // If we have a valid orderId, the widget can be restored
    // The widget component will load its own data from the backend
    if (typeof config.config?.orderId === 'number' && config.config.orderId > 0) {
      return true
    }
    return false
  }
}

export const saleWidgetRestorer = new SaleWidgetRestorer()

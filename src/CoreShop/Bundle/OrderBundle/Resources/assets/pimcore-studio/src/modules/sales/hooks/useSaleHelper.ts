/**
 * CoreShop OrderBundle - Sale Helper Hook
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { useWidgetManager } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { useCallback } from 'react'
import type { SaleType } from '../types'

type WidgetManager = ReturnType<typeof useWidgetManager>

export interface OpenSaleConfig {
  id: number
  type: SaleType
}

interface UseSaleHelperReturn {
  openSale: (config: OpenSaleConfig) => void
  openOrder: (id: number) => void
  openCart: (id: number) => void
  openQuote: (id: number) => void
}

export const saleWidgetConfig: Record<SaleType, { widget: string; label: string }> = {
  order: { widget: 'coreshop-order-detail', label: 'Order' },
  cart: { widget: 'coreshop-cart-detail', label: 'Cart' },
  quote: { widget: 'coreshop-quote-detail', label: 'Quote' }
}

/**
 * Opens a sale (order/cart/quote) detail widget using the provided widget manager.
 * Use this utility function when you have access to a widgetManager instance but can't use React hooks.
 */
export const openSaleWidget = (
  widgetManager: WidgetManager,
  { id, type }: OpenSaleConfig
): void => {
  const config = saleWidgetConfig[type]

  widgetManager.openMainWidget({
    name: `${config.label} #${id}`,
    id: `${config.widget}-${id}`,
    component: config.widget,
    config: {
      orderId: id
    }
  })
}

export const useSaleHelper = (): UseSaleHelperReturn => {
  const widgetManager = useWidgetManager()

  const openSale = useCallback(
    (config: OpenSaleConfig): void => {
      openSaleWidget(widgetManager, config)
    },
    [widgetManager]
  )

  const openOrder = useCallback(
    (id: number): void => {
      openSale({ id, type: 'order' })
    },
    [openSale]
  )

  const openCart = useCallback(
    (id: number): void => {
      openSale({ id, type: 'cart' })
    },
    [openSale]
  )

  const openQuote = useCallback(
    (id: number): void => {
      openSale({ id, type: 'quote' })
    },
    [openSale]
  )

  return {
    openSale,
    openOrder,
    openCart,
    openQuote
  }
}

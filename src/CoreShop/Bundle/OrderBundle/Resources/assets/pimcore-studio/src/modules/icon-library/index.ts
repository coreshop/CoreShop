/**
 * CoreShop OrderBundle Icon Library
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { type IconLibrary } from '@pimcore/studio-ui-bundle/modules/icon-library'

// @ts-ignore
import ordersIcon from '../../assets/orders.svg?react'
// @ts-ignore
import quotesIcon from '../../assets/quotes.svg?react'
// @ts-ignore
import invoiceIcon from '../../assets/invoice.svg?react'
// @ts-ignore
import shipmentIcon from '../../assets/shipment.svg?react'
// @ts-ignore
import pdfIcon from '../../assets/pdf.svg?react'
// @ts-ignore
import mailIcon from '../../assets/mail.svg?react'
// @ts-ignore
import orderCreateIcon from '../../assets/order-create.svg?react'
// @ts-ignore
import quoteCreateIcon from '../../assets/quote-create.svg?react'
// @ts-ignore
import conditionsIcon from '../../assets/conditions.svg?react'
// @ts-ignore
import cursorIcon from '../../assets/cursor.svg?react'
// @ts-ignore
import currenciesIcon from '../../assets/currencies.svg?react'
// @ts-ignore
import timeSpanIcon from '../../assets/time-span.svg?react'
// @ts-ignore
import voucherIcon from '../../assets/voucher.svg?react'
// @ts-ignore
import notCombinableIcon from '../../assets/not_combinable.svg?react'
// @ts-ignore
import cartIcon from '../../assets/cart.svg?react'
// @ts-ignore
import cartCreateIcon from '../../assets/cart-create.svg?react'
// @ts-ignore
import infoIcon from '../../assets/info.svg?react'
// @ts-ignore
import commentsIcon from '../../assets/comments.svg?react'
// @ts-ignore
import addIcon from '../../assets/add.svg?react'
// @ts-ignore
import removeIcon from '../../assets/remove.svg?react'
// @ts-ignore
import productAddIcon from '../../assets/product-add.svg?react'

export const OrderBundleIconModule: AbstractModule = {
  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    // Order icons
    iconLibrary.register({
      name: 'coreshop_icon_orders',
      component: ordersIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_icon_order',
      component: ordersIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_order',
      component: ordersIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_orders',
      component: ordersIcon
    })

    // Quote icons
    iconLibrary.register({
      name: 'coreshop_icon_quotes',
      component: quotesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_icon_quote',
      component: quotesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_quote',
      component: quotesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_quotes',
      component: quotesIcon
    })

    // Order states and operations
    iconLibrary.register({
      name: 'coreshop_icon_orders_invoice',
      component: invoiceIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_icon_condition_invoiceState',
      component: invoiceIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_icon_orders_shipment',
      component: shipmentIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_icon_condition_shipmentState',
      component: shipmentIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_icon_orders_invoice_pdf',
      component: pdfIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_icon_orders_shipment_pdf',
      component: pdfIcon
    })

    iconLibrary.register({
      name: 'coreshop_icon_mail',
      component: mailIcon
    })

    // Create icons
    iconLibrary.register({
      name: 'coreshop_icon_order_create',
      component: orderCreateIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_order_create',
      component: orderCreateIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_icon_quote_create',
      component: quoteCreateIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_quote_create',
      component: quoteCreateIcon
    })

    // Rule conditions
    iconLibrary.register({
      name: 'coreshop_rule_icon_condition_nested',
      component: conditionsIcon
    })
    
    iconLibrary.register({
      name: 'x-tool-coreshop-open',
      component: cursorIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_icon_open',
      component: cursorIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_icon_condition_amount',
      component: currenciesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_icon_condition_timespan',
      component: timeSpanIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_icon_condition_voucher',
      component: voucherIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_icon_condition_not_combinable',
      component: notCombinableIcon
    })

    // Cart icons
    iconLibrary.register({
      name: 'coreshop_icon_cart',
      component: cartIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_icon_carts',
      component: cartIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_carts',
      component: cartIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_icon_cart_create',
      component: cartCreateIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_cart_create',
      component: cartCreateIcon
    })

    // Additional icons
    iconLibrary.register({
      name: 'coreshop_icon_additional_data',
      component: infoIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_icon_operator_orderstate',
      component: infoIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_icon_order_comments',
      component: commentsIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_icon_operator_priceformatter',
      component: currenciesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_price_rule_vouchers',
      component: voucherIcon
    })

    // Price rule actions
    iconLibrary.register({
      name: 'coreshop_rule_icon_action_discountAmount',
      component: currenciesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_icon_action_surchargeAmount',
      component: currenciesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_icon_action_discountPercent',
      component: currenciesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_icon_action_surchargePercent',
      component: currenciesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_icon_action_price',
      component: currenciesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_icon_action_cartItemAction',
      component: ordersIcon
    })

    // Tools
    iconLibrary.register({
      name: 'x-tool-coreshop-add',
      component: addIcon
    })
    
    iconLibrary.register({
      name: 'x-tool-coreshop-remove',
      component: removeIcon
    })
    
    iconLibrary.register({
      name: 'x-tool-coreshop-add-product',
      component: productAddIcon
    })
  }
}
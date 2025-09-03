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

// Import CoreShop Order icons
import ordersIcon from '@CoreShopOrder/assets/orders.svg?react'
import quotesIcon from '@CoreShopOrder/assets/quotes.svg?react'
import invoiceIcon from '@CoreShopOrder/assets/invoice.svg?react'
import shipmentIcon from '@CoreShopOrder/assets/shipment.svg?react'
import pdfIcon from '@CoreShopOrder/assets/pdf.svg?react'
import mailIcon from '@CoreShopOrder/assets/mail.svg?react'
import orderCreateIcon from '@CoreShopOrder/assets/order-create.svg?react'
import quoteCreateIcon from '@CoreShopOrder/assets/quote-create.svg?react'
import conditionsIcon from '@CoreShopOrder/assets/conditions.svg?react'
import cursorIcon from '@CoreShopOrder/assets/cursor.svg?react'
import currenciesIcon from '@CoreShopOrder/assets/currencies.svg?react'
import timeSpanIcon from '@CoreShopOrder/assets/time-span.svg?react'
import voucherIcon from '@CoreShopOrder/assets/voucher.svg?react'
import notCombinableIcon from '@CoreShopOrder/assets/not_combinable.svg?react'
import cartIcon from '@CoreShopOrder/assets/cart.svg?react'
import cartCreateIcon from '@CoreShopOrder/assets/cart-create.svg?react'
import infoIcon from '@CoreShopOrder/assets/info.svg?react'
import commentsIcon from '@CoreShopOrder/assets/comments.svg?react'
import addIcon from '@CoreShopOrder/assets/add.svg?react'
import removeIcon from '@CoreShopOrder/assets/remove.svg?react'
import productAddIcon from '@CoreShopOrder/assets/product-add.svg?react'
import commentInternalIcon from '@CoreShopOrder/assets/comment_internal.svg?react'
import commentExternalIcon from '@CoreShopOrder/assets/comment_external.svg?react'

export const OrderBundleIconExtension: AbstractModule = {
  name: 'coreshop-order-icon-extension',

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
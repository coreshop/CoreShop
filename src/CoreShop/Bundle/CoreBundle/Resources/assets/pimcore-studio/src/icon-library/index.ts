/**
 * CoreShop CoreBundle Icon Library
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
import cartIcon from '../assets/cart.svg?react'
// @ts-ignore
import cartAbandonedIcon from '../assets/cart-abandoned.svg?react'
// @ts-ignore
import categoryIcon from '../assets/category.svg?react'
// @ts-ignore
import chartsIcon from '../assets/charts.svg?react'
// @ts-ignore
import collaborationIcon from '../assets/collaboration.svg?react'
// @ts-ignore
import commentIcon from '../assets/comment.svg?react'
// @ts-ignore
import countriesIcon from '../assets/countries.svg?react'
// @ts-ignore
import currenciesIcon from '../assets/currencies.svg?react'
// @ts-ignore
import customerIcon from '../assets/customer.svg?react'
// @ts-ignore
import customerGroupIcon from '../assets/customer-group.svg?react'
// @ts-ignore
import freeShippingIcon from '../assets/free-shipping.svg?react'
// @ts-ignore
import giftIcon from '../assets/gift.svg?react'
// @ts-ignore
import guestIcon from '../assets/guest.svg?react'
// @ts-ignore
import invoiceIcon from '../assets/invoice.svg?react'
// @ts-ignore
import logoFillIcon from '../assets/logo-fill.svg?react'
// @ts-ignore
import mailIcon from '../assets/mail.svg?react'
// @ts-ignore
import manufacturerIcon from '../assets/manufacturer.svg?react'
// @ts-ignore
import moneyIcon from '../assets/money.svg?react'
// @ts-ignore
import monitorIcon from '../assets/monitor.svg?react'
// @ts-ignore
import notCombinableIcon from '../assets/not_combinable.svg?react'
// @ts-ignore
import ordersIcon from '../assets/orders.svg?react'
// @ts-ignore
import ordersBackendIcon from '../assets/orders-backend.svg?react'
// @ts-ignore
import paymentsIcon from '../assets/payments.svg?react'
// @ts-ignore
import productIcon from '../assets/product.svg?react'
// @ts-ignore
import quotesIcon from '../assets/quotes.svg?react'
// @ts-ignore
import salesIcon from '../assets/sales.svg?react'
// @ts-ignore
import shipmentIcon from '../assets/shipment.svg?react'
// @ts-ignore
import stockIcon from '../assets/stock.svg?react'
// @ts-ignore
import storeMailIcon from '../assets/store-mail.svg?react'
// @ts-ignore
import storeValuesIcon from '../assets/store-values.svg?react'
// @ts-ignore
import voucherIcon from '../assets/voucher.svg?react'
// @ts-ignore
import customerToCompanyAssignToExisting from '../assets/customer_to_company_assign_to_existing.svg?react'
// @ts-ignore
import customerToCompanyAssignToNew from '../assets/customer_to_company_assign_to_new.svg?react'

export const CoreBundleIconModule: AbstractModule = {
  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    // Main logo icons
    iconLibrary.register({
      name: 'coreshop_logo',
      component: logoFillIcon
    })

    iconLibrary.register({
      name: 'coreshop_nav_icon_logo',
      component: logoFillIcon
    })

    iconLibrary.register({
      name: 'coreshop_nav_logo',
      component: logoFillIcon
    })

    // Localization/collaboration
    iconLibrary.register({
      name: 'coreshop_localization',
      component: collaborationIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_localization',
      component: collaborationIcon
    })

    // Customer related
    iconLibrary.register({
      name: 'coreshop_nav_icon_customer',
      component: customerIcon
    })

    // Reports
    iconLibrary.register({
      name: 'coreshop_report',
      component: chartsIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_report_carts',
      component: cartIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_report_carts_abandoned',
      component: cartAbandonedIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_report_manufacturer',
      component: manufacturerIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_report_vouchers',
      component: cartIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_report_sales',
      component: salesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_report_quantity',
      component: stockIcon
    })

    // Monitoring
    iconLibrary.register({
      name: 'coreshop_monitoring',
      component: monitorIcon
    })

    // Pimcore specific
    iconLibrary.register({
      name: 'pimcore_coreShopStorePrice',
      component: moneyIcon
    })
    
    iconLibrary.register({
      name: 'pimcore_coreShopStoreValues',
      component: storeValuesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_store_values',
      component: storeValuesIcon
    })

    // Store price operator
    iconLibrary.register({
      name: 'coreshop_operator_store_price',
      component: currenciesIcon
    })

    // Rule conditions
    iconLibrary.register({
      name: 'coreshop_rule_condition_categories',
      component: categoryIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_customers',
      component: customerIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_userType',
      component: customerIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_not_combinable_with_cart_price_voucher_rule',
      component: notCombinableIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_countries',
      component: countriesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_products',
      component: productIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_currencies',
      component: currenciesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_customerGroup',
      component: customerGroupIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_customerGroups',
      component: customerGroupIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_guest',
      component: guestIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_saleState',
      component: salesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_orderState',
      component: ordersIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_orderTransition',
      component: ordersIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_quoteState',
      component: quotesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_quoteTransition',
      component: quotesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_comment',
      component: commentIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_orderInvoiceState',
      component: invoiceIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_invoiceState',
      component: invoiceIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_orderInvoiceTransition',
      component: invoiceIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_invoiceTransition',
      component: invoiceIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_orderShippingState',
      component: shipmentIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_shippingState',
      component: shipmentIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_orderShippingTransition',
      component: shipmentIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_shipmentTransition',
      component: shipmentIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_payment',
      component: paymentsIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_paymentState',
      component: paymentsIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_orderPaymentState',
      component: paymentsIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_paymentTransition',
      component: paymentsIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_orderPaymentTransition',
      component: paymentsIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_backendCreated',
      component: ordersBackendIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_backendUpdated',
      component: ordersBackendIcon
    })

    // Rule actions
    iconLibrary.register({
      name: 'coreshop_rule_action_freeShipping',
      component: freeShippingIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_action_voucherCredit',
      component: voucherIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_action_giftProduct',
      component: giftIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_action_orderMail',
      component: mailIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_action_storeMail',
      component: storeMailIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_action_storeOrderMail',
      component: storeMailIcon
    })


    iconLibrary.register({
      name: 'coreshop_nav_icon_customer_to_company_assign_to_existing',
      component: customerToCompanyAssignToExisting
    })

    iconLibrary.register({
      name: 'coreshop_nav_icon_customer_to_company_assign_to_new',
      component: customerToCompanyAssignToNew
    })
  }
}
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
import { type IconLibrary } from '@pimcore/studio-ui-bundle'

// Import icons (excluding white variants)
import cartIcon from '@CoreShopCore/assets/cart.svg?react'
import cartAbandonedIcon from '@CoreShopCore/assets/cart-abandoned.svg?react'
import categoryIcon from '@CoreShopCore/assets/category.svg?react'
import chartsIcon from '@CoreShopCore/assets/charts.svg?react'
import collaborationIcon from '@CoreShopCore/assets/collaboration.svg?react'
import commentIcon from '@CoreShopCore/assets/comment.svg?react'
import countriesIcon from '@CoreShopCore/assets/countries.svg?react'
import currenciesIcon from '@CoreShopCore/assets/currencies.svg?react'
import customerIcon from '@CoreShopCore/assets/customer.svg?react'
import customerGroupIcon from '@CoreShopCore/assets/customer-group.svg?react'
import freeShippingIcon from '@CoreShopCore/assets/free-shipping.svg?react'
import giftIcon from '@CoreShopCore/assets/gift.svg?react'
import guestIcon from '@CoreShopCore/assets/guest.svg?react'
import invoiceIcon from '@CoreShopCore/assets/invoice.svg?react'
import logoIcon from '@CoreShopCore/assets/logo.svg?react'
import logoFillIcon from '@CoreShopCore/assets/logo-fill.svg?react'
import logoFullIcon from '@CoreShopCore/assets/logo-full.svg?react'
import mailIcon from '@CoreShopCore/assets/mail.svg?react'
import manufacturerIcon from '@CoreShopCore/assets/manufacturer.svg?react'
import moneyIcon from '@CoreShopCore/assets/money.svg?react'
import monitorIcon from '@CoreShopCore/assets/monitor.svg?react'
import notCombinableIcon from '@CoreShopCore/assets/not_combinable.svg?react'
import ordersIcon from '@CoreShopCore/assets/orders.svg?react'
import ordersBackendIcon from '@CoreShopCore/assets/orders-backend.svg?react'
import paymentsIcon from '@CoreShopCore/assets/payments.svg?react'
import productIcon from '@CoreShopCore/assets/product.svg?react'
import quotesIcon from '@CoreShopCore/assets/quotes.svg?react'
import salesIcon from '@CoreShopCore/assets/sales.svg?react'
import shipmentIcon from '@CoreShopCore/assets/shipment.svg?react'
import stockIcon from '@CoreShopCore/assets/stock.svg?react'
import storeMailIcon from '@CoreShopCore/assets/store-mail.svg?react'
import storeValuesIcon from '@CoreShopCore/assets/store-values.svg?react'
import voucherIcon from '@CoreShopCore/assets/voucher.svg?react'
import customerToCompanyAssignToExisting from '@CoreShopCore/assets/customer_to_company_assign_to_existing.svg?react'
import customerToCompanyAssignToNew from '@CoreShopCore/assets/customer_to_company_assign_to_new.svg?react'

export const CoreBundleIconExtension: AbstractModule = {
  name: 'coreshop-core-icon-extension',

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
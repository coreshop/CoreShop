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

import { IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import type { WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
// Widget restorer registry import removed - type not exported
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { Input } from 'antd'
import { widgetRegistryServiceId } from '@coreshop/studio-form'
import type { WidgetRegistry as StudioFormWidgetRegistry } from '@coreshop/studio-form'
import { registerMenuButton } from '@coreshop/menu/src'
import i18n from 'i18next'
import { OrderBundleIconModule } from './modules/icon-library'
import { DynamicTypeObjectDataCoreShopCartPriceRule } from './dynamic-types'
import { SalesListingBuildersModule } from './modules/sales/listing-builders'
import { CartPriceRuleManager } from './modules/cart-price-rules/CartPriceRuleManager'
import { ConditionRegistry, ActionRegistry } from '@coreshop/rule/src/rules/registry'
import { CartItemAction } from './modules/cart-price-rules/actions'
import { coreshopOrderServiceIds } from './modules/cart-price-rules/service-ids'
import {
  OrderList,
  CartList,
  QuoteList,
  OrderDetailWidget,
  CartDetailWidget,
  QuoteDetailWidget
} from './modules/sales'
import { saleWidgetRestorer } from './modules/sales/SaleWidgetRestorer'
import { SaleTabRegistry } from './modules/sales/registry'
import { serviceIds as saleServiceIds } from './modules/sales/service-ids'
import {
  HeaderTab,
  CustomerTab,
  DetailTab,
  PaymentTab,
  ShipmentTab,
  InvoiceTab,
  CommentsTab,
  InfoTab,
  CorrespondenceTab
} from './modules/sales/tabs'
import { OrderCreationStepRegistry } from './modules/order-creation/registry'
import { orderCreationServiceIds } from './modules/order-creation/service-ids'
import { OrderCreationPanel } from './modules/order-creation/components'
import { OrderCreationButton } from './modules/order-creation/components/OrderCreationButton'
import { orderCreationWidgetRestorer } from './modules/order-creation/OrderCreationWidgetRestorer'
import { BaseStepConfig, ProductsStepConfig, TotalsStepConfig } from './modules/order-creation/steps'

const plugin: IAbstractPlugin = {
    name: 'coreshop-order',

    onInit() {
        // ============================================
        // Dynamic Types Registration
        // ============================================
        const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
            serviceIds['DynamicTypes/ObjectDataRegistry']
        )
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopCartPriceRule())

        // ============================================
        // Widget Restorer Registration
        // ============================================
        // Register restorer for Order/Cart/Quote detail widgets
        // This enables widget persistence across browser refreshes
        const widgetRestorerRegistry = container.get<any>((serviceIds as any).widgetRestorerRegistry)
        if (widgetRestorerRegistry) {
          widgetRestorerRegistry.register(saleWidgetRestorer)
          widgetRestorerRegistry.register(orderCreationWidgetRestorer)
        }

        // ============================================
        // Cart Price Rules Registry Setup
        // ============================================
        // Register CartPriceRule registries as singleton services in the container
        container.bind(coreshopOrderServiceIds.cartPriceRuleConditionRegistry).to(ConditionRegistry).inSingletonScope()
        container.bind(coreshopOrderServiceIds.cartPriceRuleActionRegistry).to(ActionRegistry).inSingletonScope()

        // Register CartItem registries as singleton services in the container
        container.bind(coreshopOrderServiceIds.cartItemActionRegistry).to(ActionRegistry).inSingletonScope()
        container.bind(coreshopOrderServiceIds.cartItemConditionRegistry).to(ConditionRegistry).inSingletonScope()

        // Get the registries
        const conditionRegistry = container.get<ConditionRegistry>(coreshopOrderServiceIds.cartPriceRuleConditionRegistry)
        const actionRegistry = container.get<ActionRegistry>(coreshopOrderServiceIds.cartPriceRuleActionRegistry)
        const cartItemConditionRegistry = container.get<ConditionRegistry>(coreshopOrderServiceIds.cartItemConditionRegistry)
        const cartItemActionRegistry = container.get<ActionRegistry>(coreshopOrderServiceIds.cartItemActionRegistry)

        // Register custom non-schema action(s).
        // Schema-based conditions/actions are auto-registered at runtime from backend mappings.
        actionRegistry.register('cartItemAction', CartItemAction)

        // Cart Item Actions are registered by CoreBundle (glue layer)
        void conditionRegistry
        void cartItemConditionRegistry
        void cartItemActionRegistry

        // ============================================
        // Sales (Order/Cart/Quote) Registry Setup
        // ============================================
        // Create and bind sale tab registry
        container.bind(saleServiceIds.saleTabRegistry).to(SaleTabRegistry).inSingletonScope()

        // Get registry
        const tabRegistry = container.get<SaleTabRegistry>(saleServiceIds.saleTabRegistry)

        // Register tabs with priority and type filtering
        // Lower priority = displayed first

        const t = i18n.t;

        // TOP: Header Block
        tabRegistry.register('header', {
            key: 'header',
            label: t('coreshop_header', { defaultValue: 'Header' }),
            priority: 10,
            position: 'top',
            types: ['order', 'cart', 'quote'],
            component: HeaderTab
        })

        // LEFT: Info Block
        tabRegistry.register('info', {
            key: 'info',
            label: t('coreshop_order_info', { defaultValue: 'Order: Carrier/Payment Provider' }),
            priority: 10,
            position: 'left',
            types: ['order', 'cart', 'quote'],
            component: InfoTab
        })

        // LEFT: Payment Block
        tabRegistry.register('payment', {
            key: 'payment',
            label: t('coreshop_payments', { defaultValue: 'Payment(s)' }),
            priority: 20,
            position: 'left',
            types: ['order'],
            component: PaymentTab
            // Note: PaymentTab registers its own CreatePaymentButton dynamically
        })

        // LEFT: Shipment Block
        tabRegistry.register('shipment', {
            key: 'shipment',
            label: t('coreshop_shipments', { defaultValue: 'Shipments' }),
            priority: 30,
            position: 'left',
            types: ['order'],
            component: ShipmentTab
            // Note: ShipmentTab registers its own CreateShipmentButton dynamically
        })

        // LEFT: Invoice Block
        tabRegistry.register('invoice', {
            key: 'invoice',
            label: t('coreshop_invoices', { defaultValue: 'Invoices' }),
            priority: 40,
            position: 'left',
            types: ['order'],
            component: InvoiceTab
            // Note: InvoiceTab registers its own CreateInvoiceButton dynamically
        })

        // RIGHT: Customer Block
        tabRegistry.register('customer', {
            key: 'customer',
            label: t('coreshop_customer', { defaultValue: 'Customer' }),
            priority: 10,
            position: 'right',
            types: ['order', 'cart', 'quote'],
            component: CustomerTab
        })

        // RIGHT: Comments Block
        tabRegistry.register('comments', {
            key: 'comments',
            label: t('coreshop_order_comments', { defaultValue: 'Comments' }),
            priority: 20,
            position: 'right',
            types: ['order', 'quote'],
            component: CommentsTab
        })

        // RIGHT: Mail Correspondence Block
        tabRegistry.register('correspondence', {
            key: 'correspondence',
            label: t('coreshop_mail_correspondence', { defaultValue: 'Mail Correspondence' }),
            priority: 30,
            position: 'right',
            types: ['order'],
            component: CorrespondenceTab
        })

        // BOTTOM: Detail/Products Block
        tabRegistry.register('detail', {
            key: 'detail',
            label: t('coreshop_products', { defaultValue: 'Products' }),
            priority: 10,
            position: 'bottom',
            types: ['order', 'cart', 'quote'],
            component: DetailTab
        })

        // ============================================
        // Order Creation Registry Setup
        // ============================================
        // Create and bind order creation step registry
        container.bind(orderCreationServiceIds.stepRegistry).to(OrderCreationStepRegistry).inSingletonScope()

        // Get registry and register base steps
        const orderCreationStepRegistry = container.get<OrderCreationStepRegistry>(orderCreationServiceIds.stepRegistry)

        // Register OrderBundle steps (base implementation)
        orderCreationStepRegistry.register('base', BaseStepConfig)
        orderCreationStepRegistry.register('products', ProductsStepConfig)
        orderCreationStepRegistry.register('totals', TotalsStepConfig)

        registerMenuButton({
          name: 'coreshopCreateOrder',
          button: OrderCreationButton,
        })
    },

    onStartup({ moduleSystem }) {
        // Hide OrderBundle-owned rule collection prefixes from generic schema forms
        const formWidgetRegistry = container.get<StudioFormWidgetRegistry>(widgetRegistryServiceId)
        const hiddenWidget = () => ({ component: Input, extra: { hidden: true } })
        ;[
          'coreshop_cart_price_rule_condition_collection',
          'coreshop_cart_price_rule_action_collection',
          'coreshop_cart_item_price_rule_condition_collection',
          'coreshop_cart_item_price_rule_action_collection',
        ].forEach((prefix) => formWidgetRegistry.register(prefix, hiddenWidget))

        moduleSystem.registerModule(OrderBundleIconModule)
        moduleSystem.registerModule(SalesListingBuildersModule)

        // Register widgets
        const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)

        // Register Cart Price Rules widget
        widgets.registerWidget({
            name: 'coreshop-order-cart-price-rules',
            component: CartPriceRuleManager
        })

        // Register Sale List widgets
        widgets.registerWidget({
            name: 'coreshop-order-manager',
            component: OrderList
        })

        widgets.registerWidget({
            name: 'coreshop-cart-manager',
            component: CartList
        })

        widgets.registerWidget({
            name: 'coreshop-quote-manager',
            component: QuoteList
        })

        // Register standalone detail widgets (not DataObject editor tabs)
        widgets.registerWidget({
            name: 'coreshop-order-detail',
            component: OrderDetailWidget,
        })

        widgets.registerWidget({
            name: 'coreshop-cart-detail',
            component: CartDetailWidget,
        })

        widgets.registerWidget({
            name: 'coreshop-quote-detail',
            component: QuoteDetailWidget,
        })

        // Register Order Creation detail widget (persistent tab per customer)
        widgets.registerWidget({
            name: 'coreshop-order-creation-detail',
            component: OrderCreationPanel
        })
    }
}

export default plugin

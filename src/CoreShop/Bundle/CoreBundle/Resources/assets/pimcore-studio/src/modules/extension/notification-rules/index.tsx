/**
 * Notification Rules Extension Module
 *
 * This module extends notification rule registries from NotificationBundle
 * with conditions specific to different notification types.
 *
 * Notification rules support type-prefixed conditions, e.g.:
 * - "order.orderState" for order notifications
 * - "payment.paymentState" for payment notifications
 * - "stores" for all notification types (no prefix)
 */

import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import type { ConditionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopNotificationServiceIds } from '@coreshop/notification/src/modules/notification-rules/service-ids'

// Import all conditions
import {
  StoresCondition,
  CarriersCondition,
  PaymentCondition,
  CommentCondition,
  BackendCreatedCondition,
  UserTypeCondition,
  MessageTypeCondition,
  // Order conditions
  OrderStateCondition,
  OrderTransitionCondition,
  OrderPaymentStateCondition,
  OrderPaymentTransitionCondition,
  OrderShippingStateCondition,
  OrderShippingTransitionCondition,
  OrderInvoiceStateCondition,
  OrderInvoiceTransitionCondition,
  SaleStateCondition,
  // Payment conditions
  PaymentStateCondition,
  PaymentTransitionCondition,
  // Invoice conditions
  InvoiceStateCondition,
  InvoiceTransitionCondition,
  // Shipment conditions
  ShipmentStateCondition,
  ShipmentTransitionCondition,
  // Quote conditions
  QuoteStateCondition,
  QuoteTransitionCondition
} from './conditions'

/**
 * Notification type prefixes for conditions
 */
const NOTIFICATION_TYPES = {
  ORDER: 'order',
  PAYMENT: 'payment',
  INVOICE: 'invoice',
  SHIPMENT: 'shipment',
  QUOTE: 'quote',
  USER: 'user',
  MESSAGING: 'messaging'
}

/**
 * Register notification conditions with type prefixes
 */
function registerNotificationConditions(conditionRegistry: ConditionRegistry): void {
  // ============================================
  // Common conditions (available for all types)
  // Register both with and without prefix for compatibility
  // ============================================
  conditionRegistry.register('stores', StoresCondition)

  // Also register stores with type prefixes (backend sends prefixed types)
  const allTypes = Object.values(NOTIFICATION_TYPES)
  allTypes.forEach(type => {
    conditionRegistry.register(`${type}.stores`, StoresCondition)
  })

  // ============================================
  // Order notification conditions
  // ============================================
  const orderPrefix = `${NOTIFICATION_TYPES.ORDER}.`

  // Order-specific conditions
  conditionRegistry.register(`${orderPrefix}orderState`, OrderStateCondition)
  conditionRegistry.register(`${orderPrefix}orderTransition`, OrderTransitionCondition)
  conditionRegistry.register(`${orderPrefix}orderPaymentState`, OrderPaymentStateCondition)
  conditionRegistry.register(`${orderPrefix}orderPaymentTransition`, OrderPaymentTransitionCondition)
  conditionRegistry.register(`${orderPrefix}orderShippingState`, OrderShippingStateCondition)
  conditionRegistry.register(`${orderPrefix}orderShippingTransition`, OrderShippingTransitionCondition)
  conditionRegistry.register(`${orderPrefix}orderInvoiceState`, OrderInvoiceStateCondition)
  conditionRegistry.register(`${orderPrefix}orderInvoiceTransition`, OrderInvoiceTransitionCondition)
  conditionRegistry.register(`${orderPrefix}saleState`, SaleStateCondition)
  conditionRegistry.register(`${orderPrefix}carriers`, CarriersCondition)
  conditionRegistry.register(`${orderPrefix}payment`, PaymentCondition)
  conditionRegistry.register(`${orderPrefix}comment`, CommentCondition)
  conditionRegistry.register(`${orderPrefix}backendCreated`, BackendCreatedCondition)

  // ============================================
  // Payment notification conditions
  // ============================================
  const paymentPrefix = `${NOTIFICATION_TYPES.PAYMENT}.`

  conditionRegistry.register(`${paymentPrefix}paymentState`, PaymentStateCondition)
  conditionRegistry.register(`${paymentPrefix}paymentTransition`, PaymentTransitionCondition)

  // ============================================
  // Invoice notification conditions
  // ============================================
  const invoicePrefix = `${NOTIFICATION_TYPES.INVOICE}.`

  conditionRegistry.register(`${invoicePrefix}invoiceState`, InvoiceStateCondition)
  conditionRegistry.register(`${invoicePrefix}invoiceTransition`, InvoiceTransitionCondition)

  // ============================================
  // Shipment notification conditions
  // ============================================
  const shipmentPrefix = `${NOTIFICATION_TYPES.SHIPMENT}.`

  conditionRegistry.register(`${shipmentPrefix}shipmentState`, ShipmentStateCondition)
  conditionRegistry.register(`${shipmentPrefix}shipmentTransition`, ShipmentTransitionCondition)

  // ============================================
  // Quote notification conditions
  // ============================================
  const quotePrefix = `${NOTIFICATION_TYPES.QUOTE}.`

  conditionRegistry.register(`${quotePrefix}quoteState`, QuoteStateCondition)
  conditionRegistry.register(`${quotePrefix}quoteTransition`, QuoteTransitionCondition)

  // ============================================
  // User notification conditions
  // ============================================
  const userPrefix = `${NOTIFICATION_TYPES.USER}.`

  conditionRegistry.register(`${userPrefix}userType`, UserTypeCondition)

  // ============================================
  // Messaging notification conditions
  // ============================================
  const messagingPrefix = `${NOTIFICATION_TYPES.MESSAGING}.`

  conditionRegistry.register(`${messagingPrefix}messageType`, MessageTypeCondition)
}

/**
 * Wait for notification registry to be available
 */
async function waitForRegistry(maxAttempts: number = 50, interval: number = 100): Promise<boolean> {
  let attempts = 0

  return new Promise((resolve) => {
    const checkRegistry = () => {
      attempts++

      if (container.isBound(coreshopNotificationServiceIds.notificationRuleConditionRegistry)) {
        resolve(true)
        return
      }

      if (attempts >= maxAttempts) {
        console.warn('[CoreShop Core] Timeout waiting for notification rule registry')
        resolve(false)
        return
      }

      setTimeout(checkRegistry, interval)
    }

    checkRegistry()
  })
}

/**
 * Notification Rules Extension Module
 */
export const NotificationRulesExtensionModule: AbstractModule = {
  async onInit(): Promise<void> {
    // Wait for notification registry to be available
    const available = await waitForRegistry()

    if (!available) {
      console.warn('[CoreShop Core] Notification rule registry not available, skipping extension')
      return
    }

    try {
      // Get the notification condition registry
      const conditionRegistry = container.get<ConditionRegistry>(
        coreshopNotificationServiceIds.notificationRuleConditionRegistry
      )

      // Register all notification conditions
      registerNotificationConditions(conditionRegistry)

    } catch (error) {
      console.error('[CoreShop Core] Failed to extend notification rule registry:', error)
    }
  }
}

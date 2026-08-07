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

/**
 * Service IDs for CoreShop Order registries
 * Includes both CartPriceRule and CartItem registries
 */
export const coreshopOrderServiceIds = {
  // CartPriceRule registries
  cartPriceRuleConditionRegistry: Symbol.for('coreshop.order.cart_price_rule.condition_registry'),
  cartPriceRuleActionRegistry: Symbol.for('coreshop.order.cart_price_rule.action_registry'),

  // CartItem registries (used within CartPriceRule actions)
  cartItemActionRegistry: 'CoreShopOrderCartItemActionRegistry',
  cartItemConditionRegistry: 'CoreShopOrderCartItemConditionRegistry'
} as const

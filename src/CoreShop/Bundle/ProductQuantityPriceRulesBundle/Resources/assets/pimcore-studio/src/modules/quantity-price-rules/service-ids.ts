/**
 * CoreShop ProductQuantityPriceRulesBundle Studio Plugin
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
 * Service IDs for Quantity Price Rules
 * Uses Symbols for unique identification in the DI container
 */
export const coreshopQuantityPriceRulesServiceIds = {
  /**
   * Condition registry for quantity price rules
   */
  conditionRegistry: Symbol.for('coreshop.quantity_price_rules.condition_registry')
}

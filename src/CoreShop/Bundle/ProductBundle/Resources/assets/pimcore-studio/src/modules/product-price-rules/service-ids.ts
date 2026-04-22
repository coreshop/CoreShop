/**
 * CoreShop ProductBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

export const coreshopProductServiceIds = {
  productPriceRuleConditionRegistry: Symbol.for('coreshop.product.product_price_rule.condition_registry'),
  productPriceRuleActionRegistry: Symbol.for('coreshop.product.product_price_rule.action_registry'),
  productSpecificPriceRuleConditionRegistry: Symbol.for('coreshop.product.product_specific_price_rule.condition_registry'),
  productSpecificPriceRuleActionRegistry: Symbol.for('coreshop.product.product_specific_price_rule.action_registry')
}

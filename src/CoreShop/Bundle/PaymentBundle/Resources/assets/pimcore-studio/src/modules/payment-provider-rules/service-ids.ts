/**
 * CoreShop PaymentBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

export const coreshopPaymentServiceIds = {
  paymentProviderRuleConditionRegistry: Symbol.for('coreshop.payment.payment_provider_rule.condition_registry'),
  paymentProviderRuleActionRegistry: Symbol.for('coreshop.payment.payment_provider_rule.action_registry'),
  gatewayConfiguratorRegistry: Symbol.for('coreshop.payment.gateway_configurator_registry')
}

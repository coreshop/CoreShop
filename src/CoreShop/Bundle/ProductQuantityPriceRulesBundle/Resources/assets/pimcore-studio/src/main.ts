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

import { type IAbstractPlugin, type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { ProductQuantityPriceRulesBundleIconModule } from './modules/icon-library'
import { ConditionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopQuantityPriceRulesServiceIds } from './modules/quantity-price-rules'

/**
 * Module for registering Quantity Price Rules registries
 *
 * Note: All conditions (nested, timespan, categories, customers, etc.) are registered
 * by CoreBundle's RuleRegistryExtensionModule. This bundle only creates the registry.
 */
const QuantityPriceRulesRegistryModule: AbstractModule = {
  onInit(): void {
    // Create and bind condition registry for Quantity Price Rules
    container.bind(coreshopQuantityPriceRulesServiceIds.conditionRegistry)
      .to(ConditionRegistry)
      .inSingletonScope()
  }
}

const plugin: IAbstractPlugin = {
  name: 'coreshop-product-quantity-price-rules',

  onInit() {
  },

  onStartup({ moduleSystem }) {
    moduleSystem.registerModule(ProductQuantityPriceRulesBundleIconModule)
    moduleSystem.registerModule(QuantityPriceRulesRegistryModule)
  }
}

export default plugin

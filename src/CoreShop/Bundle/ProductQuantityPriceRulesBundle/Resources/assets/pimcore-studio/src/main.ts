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

import { type IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { ProductQuantityPriceRulesBundleIconModule } from './modules/icon-library'
import { ConditionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopQuantityPriceRulesServiceIds } from './modules/quantity-price-rules'
import { DynamicTypeObjectDataCoreShopProductQuantityPriceRules } from './dynamic-types'

const plugin: IAbstractPlugin = {
  name: 'coreshop-product-quantity-price-rules',

  onInit() {
    // Register Dynamic Type
    const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
      serviceIds['DynamicTypes/ObjectDataRegistry']
    )
    objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopProductQuantityPriceRules())

    // Create and bind condition registry for Quantity Price Rules
    // Note: All conditions (nested, timespan, categories, customers, etc.) are registered
    // by CoreBundle's RuleRegistryExtensionModule. This bundle only creates the registry.
    container.bind(coreshopQuantityPriceRulesServiceIds.conditionRegistry)
      .to(ConditionRegistry)
      .inSingletonScope()
  },

  onStartup({ moduleSystem }) {
    moduleSystem.registerModule(ProductQuantityPriceRulesBundleIconModule)
  }
}

export default plugin

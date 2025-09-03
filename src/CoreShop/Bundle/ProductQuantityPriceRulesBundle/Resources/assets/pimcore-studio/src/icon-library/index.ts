/**
 * CoreShop ProductQuantityPriceRulesBundle Icon Library
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

import priceRulesIcon from '@CoreShopProductQuantityPriceRules/assets/price-rules.svg?react'
import productQuantityPriceRulesIcon from '@CoreShopProductQuantityPriceRules/assets/product-quanity-price-rules.svg?react'

export const ProductQuantityPriceRulesBundleIconExtension: AbstractModule = {
  name: 'coreshop-product-quantity-price-rules-icon-extension',

  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    iconLibrary.register({
      name: 'coreshop_price_rules',
      component: priceRulesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_product_quantity_price_rules',
      component: productQuantityPriceRulesIcon
    })
  }
}

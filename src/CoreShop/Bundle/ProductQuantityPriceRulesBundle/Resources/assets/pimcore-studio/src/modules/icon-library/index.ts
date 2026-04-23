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

// @ts-ignore
import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
// @ts-ignore
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
// @ts-ignore
import { type IconLibrary } from '@pimcore/studio-ui-bundle/modules/icon-library'
// @ts-ignore
import priceRulesIcon from '../../assets/price-rules.svg?react'
// @ts-ignore
import productQuantityPriceRulesIcon from '../../assets/product-quanity-price-rules.svg?react'

export const ProductQuantityPriceRulesBundleIconModule: AbstractModule = {
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

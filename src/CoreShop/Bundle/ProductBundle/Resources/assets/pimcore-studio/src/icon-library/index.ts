/**
 * CoreShop ProductBundle Icon Library
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

// Import CoreShop Product icons
import cartIcon from '@CoreShopProduct/assets/cart.svg?react'
import categoryIcon from '@CoreShopProduct/assets/category.svg?react'
import conditionsIcon from '@CoreShopProduct/assets/conditions.svg?react'
import currenciesIcon from '@CoreShopProduct/assets/currencies.svg?react'
import notDiscountableIcon from '@CoreShopProduct/assets/not_discountable.svg?react'
import priceRulesIcon from '@CoreShopProduct/assets/price-rules.svg?react'
import productIcon from '@CoreShopProduct/assets/product.svg?react'
import productBlueIcon from '@CoreShopProduct/assets/product_blue.svg?react'
import productGreenIcon from '@CoreShopProduct/assets/product_green.svg?react'
import productListIcon from '@CoreShopProduct/assets/product-list.svg?react'
import productUnitIcon from '@CoreShopProduct/assets/product-unit.svg?react'
import productUnitsIcon from '@CoreShopProduct/assets/product-units.svg?react'
import timeSpanIcon from '@CoreShopProduct/assets/time-span.svg?react'

export const ProductBundleIconExtension: AbstractModule = {
  name: 'coreshop-product-icon-extension',

  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    // Product icons
    iconLibrary.register({
      name: 'coreshop_product',
      component: productIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_product',
      component: productIcon
    })

    iconLibrary.register({
      name: 'coreshop_product_blue',
      component: productBlueIcon
    })

    iconLibrary.register({
      name: 'coreshop_product_green',
      component: productGreenIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_product_list',
      component: productListIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_product_list',
      component: productListIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_product_unit',
      component: productUnitIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_product_unit',
      component: productUnitIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_product_units',
      component: productUnitsIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_product_units',
      component: productUnitsIcon
    })

    // Price rules
    iconLibrary.register({
      name: 'coreshop_price_rule',
      component: priceRulesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_price_rule',
      component: priceRulesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_product_specific_price_rules',
      component: priceRulesIcon
    })

    // Categories
    iconLibrary.register({
      name: 'coreshop_category',
      component: categoryIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_categories',
      component: categoryIcon
    })

    // Cart
    iconLibrary.register({
      name: 'coreshop_cart',
      component: cartIcon
    })

    // Conditions
    iconLibrary.register({
      name: 'coreshop_product_condition',
      component: conditionsIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_condition_products',
      component: productIcon
    })

    // Currencies
    iconLibrary.register({
      name: 'coreshop_product_currencies',
      component: currenciesIcon
    })

    // Not discountable
    iconLibrary.register({
      name: 'coreshop_not_discountable',
      component: notDiscountableIcon
    })

    // Time span
    iconLibrary.register({
      name: 'coreshop_time_span',
      component: timeSpanIcon
    })
  }
}
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
import { type IconLibrary } from '@pimcore/studio-ui-bundle/modules/icon-library'

// @ts-ignore
import cartIcon from '../../assets/cart.svg?react'
// @ts-ignore
import categoryIcon from '../../assets/category.svg?react'
// @ts-ignore
import conditionsIcon from '../../assets/conditions.svg?react'
// @ts-ignore
import currenciesIcon from '../../assets/currencies.svg?react'
// @ts-ignore
import notDiscountableIcon from '../../assets/not_discountable.svg?react'
// @ts-ignore
import priceRulesIcon from '../../assets/price-rules.svg?react'
// @ts-ignore
import productIcon from '../../assets/product.svg?react'
// @ts-ignore
import productBlueIcon from '../../assets/product_blue.svg?react'
// @ts-ignore
import productGreenIcon from '../../assets/product_green.svg?react'
// @ts-ignore
import productListIcon from '../../assets/product-list.svg?react'
// @ts-ignore
import productUnitIcon from '../../assets/product-unit.svg?react'
// @ts-ignore
import productUnitsIcon from '../../assets/product-units.svg?react'
// @ts-ignore
import timeSpanIcon from '../../assets/time-span.svg?react'

export const ProductBundleIconModule: AbstractModule = {
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
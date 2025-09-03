/**
 * CoreShop ShippingBundle Icon Library
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

import carriersIcon from '@CoreShopShipping/assets/carriers.svg?react'
import conditionsIcon from '@CoreShopShipping/assets/conditions.svg?react'
import currenciesIcon from '@CoreShopShipping/assets/currencies.svg?react'
import dimensionIcon from '@CoreShopShipping/assets/dimension.svg?react'
import postcodeIcon from '@CoreShopShipping/assets/postcode.svg?react'
import shippingIcon from '@CoreShopShipping/assets/shipping.svg?react'
import shippingRulesIcon from '@CoreShopShipping/assets/shippingrules.svg?react'
import weightIcon from '@CoreShopShipping/assets/weight.svg?react'

export const ShippingBundleIconExtension: AbstractModule = {
  name: 'coreshop-shipping-icon-extension',

  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    // Main shipping icons
    iconLibrary.register({
      name: 'coreshop_shipping',
      component: shippingIcon
    })

    iconLibrary.register({
      name: 'coreshop_nav_icon_shipping',
      component: shippingIcon
    })

    iconLibrary.register({
      name: 'coreshop_shipping_rules',
      component: shippingRulesIcon
    })

    iconLibrary.register({
      name: 'coreshop_nav_icon_carrier_shipping_rule',
      component: shippingRulesIcon
    })

    iconLibrary.register({
      name: 'coreshop_carriers',
      component: carriersIcon
    })

    iconLibrary.register({
      name: 'coreshop_nav_icon_carriers',
      component: carriersIcon
    })

    // Shipping conditions
    iconLibrary.register({
      name: 'coreshop_shipping_condition',
      component: conditionsIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_shipping_conditions',
      component: conditionsIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_shipping_dimension',
      component: dimensionIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_shipping_weight',
      component: weightIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_shipping_postcode',
      component: postcodeIcon
    })

    // Currency for shipping
    iconLibrary.register({
      name: 'coreshop_shipping_currencies',
      component: currenciesIcon
    })
  }
}
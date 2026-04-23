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
import { type IconLibrary } from '@pimcore/studio-ui-bundle/modules/icon-library'

// @ts-ignore
import carriersIcon from '../../assets/carriers.svg?react'
// @ts-ignore
import conditionsIcon from '../../assets/conditions.svg?react'
// @ts-ignore
import currenciesIcon from '../../assets/currencies.svg?react'
// @ts-ignore
import dimensionIcon from '../../assets/dimension.svg?react'
// @ts-ignore
import postcodeIcon from '../../assets/postcode.svg?react'
// @ts-ignore
import shippingIcon from '../../assets/shipping.svg?react'
// @ts-ignore
import shippingRulesIcon from '../../assets/shippingrules.svg?react'
// @ts-ignore
import weightIcon from '../../assets/weight.svg?react'

export const ShippingBundleIconModule: AbstractModule = {
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
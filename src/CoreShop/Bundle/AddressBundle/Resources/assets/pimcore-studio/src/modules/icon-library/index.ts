/**
 * CoreShop AddressBundle Icon Library
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
import addressIcon from '../../assets/address.svg?react'
// @ts-ignore
import addressIdentifierIcon from '../../assets/address_identifier.svg?react'
// @ts-ignore
import countriesIcon from '../../assets/countries.svg?react'
// @ts-ignore
import stateIcon from '../../assets/state.svg?react'
// @ts-ignore
import zonesIcon from '../../assets/zones.svg?react'

export const AddressBundleIconModule: AbstractModule = {
  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    iconLibrary.register({
      name: 'coreshop_country',
      component: countriesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_country',
      component: countriesIcon
    })

    iconLibrary.register({
      name: 'coreshop_zone',
      component: zonesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_zone',
      component: zonesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_state',
      component: stateIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_state',
      component: stateIcon
    })

    iconLibrary.register({
      name: 'coreshop_address',
      component: addressIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_address_identifier',
      component: addressIdentifierIcon
    })
  }
}
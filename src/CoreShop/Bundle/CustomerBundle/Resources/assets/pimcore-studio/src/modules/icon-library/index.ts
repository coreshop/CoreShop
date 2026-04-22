/**
 * CoreShop CustomerBundle Icon Library
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
import customerIcon from '../../assets/customer.svg?react'
// @ts-ignore
import customerGroupIcon from '../../assets/customer-group.svg?react'

export const CustomerBundleIconModule: AbstractModule = {
  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    iconLibrary.register({
      name: 'coreshop_icon_customer',
      component: customerIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_icon_customers',
      component: customerIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_customer',
      component: customerIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_customers',
      component: customerIcon
    })

    iconLibrary.register({
      name: 'coreshop_icon_customer_group',
      component: customerGroupIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_icon_customer_groups',
      component: customerGroupIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_customer_groups',
      component: customerGroupIcon
    })
  }
}
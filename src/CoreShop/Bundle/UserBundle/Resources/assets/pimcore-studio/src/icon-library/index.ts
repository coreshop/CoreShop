/**
 * CoreShop UserBundle Icon Library
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

import customerIcon from '@CoreShopUser/assets/customer.svg?react'
import customerGroupIcon from '@CoreShopUser/assets/customer-group.svg?react'

export const UserBundleIconExtension: AbstractModule = {
  name: 'coreshop-user-icon-extension',

  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    iconLibrary.register({
      name: 'coreshop_user',
      component: customerIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_customer',
      component: customerIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_customer_group',
      component: customerGroupIcon
    })
  }
}

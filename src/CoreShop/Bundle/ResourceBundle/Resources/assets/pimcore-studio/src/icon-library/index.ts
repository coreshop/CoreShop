/**
 * CoreShop ResourceBundle Icon Library
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

import cursorIcon from '@CoreShopResource/assets/cursor.svg?react'
import logoIcon from '@CoreShopResource/assets/logo.svg?react'
import servicesIcon from '@CoreShopResource/assets/services.svg?react'

export const ResourceBundleIconExtension: AbstractModule = {
  name: 'coreshop-resource-icon-extension',

  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    // Register icons based on CSS class names from resource.css (without _icon_ and ignoring _white_)
    iconLibrary.register({
      name: 'pimcore_data_group_coreshop',
      component: logoIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_cursor',
      component: cursorIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_settings',
      component: servicesIcon
    })

    iconLibrary.register({
      name: 'coreshop_nav_icon_settings',
      component: servicesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_settings',
      component: servicesIcon
    })
  }
}
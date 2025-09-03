/**
 * CoreShop PimcoreBundle Icon Library
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

import clearFilterIcon from '@CoreShopPimcore/assets/clear-filter.svg?react'
import dynamicDropdownIcon from '@CoreShopPimcore/assets/dynamic-dropdown.svg?react'
import dynamicDropdownMultipleIcon from '@CoreShopPimcore/assets/dynamic-dropdown-multiple.svg?react'
import embeddedClassIcon from '@CoreShopPimcore/assets/embedded_class.svg?react'
import itemSelectorIcon from '@CoreShopPimcore/assets/item-selector.svg?react'
import serializedIcon from '@CoreShopPimcore/assets/serialized.svg?react'
import superBoxSelectIcon from '@CoreShopPimcore/assets/super-box-select.svg?react'

export const PimcoreBundleIconExtension: AbstractModule = {
  name: 'coreshop-pimcore-icon-extension',

  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    iconLibrary.register({
      name: 'coreshop_clear_filter',
      component: clearFilterIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_dynamic_dropdown',
      component: dynamicDropdownIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_dynamic_dropdown_multiple',
      component: dynamicDropdownMultipleIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_embedded_class',
      component: embeddedClassIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_item_selector',
      component: itemSelectorIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_serialized',
      component: serializedIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_super_box_select',
      component: superBoxSelectIcon
    })
  }
}

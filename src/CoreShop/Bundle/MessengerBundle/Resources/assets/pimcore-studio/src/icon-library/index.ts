/**
 * CoreShop MessengerBundle Icon Library
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

import messengerIcon from '@CoreShopMessenger/assets/messenger.svg?react'

export const MessengerBundleIconExtension: AbstractModule = {
  name: 'coreshop-messenger-icon-extension',

  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    iconLibrary.register({
      name: 'coreshop_messenger',
      component: messengerIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_nav_icon_messenger',
      component: messengerIcon
    })
  }
}
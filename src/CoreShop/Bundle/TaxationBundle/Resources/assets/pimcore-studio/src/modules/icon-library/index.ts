/**
 * CoreShop TaxationBundle Icon Library
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
import taxesIcon from '../../assets/taxes.svg?react'

export const TaxationBundleIconModule: AbstractModule = {
  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    iconLibrary.register({
      name: 'coreshop_tax',
      component: taxesIcon
    })

    iconLibrary.register({
      name: 'coreshop_nav_icon_taxes',
      component: taxesIcon
    })

    iconLibrary.register({
      name: 'coreshop_nav_icon_tax_rule_groups',
      component: taxesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_taxes',
      component: taxesIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_taxation',
      component: taxesIcon
    })
  }
}

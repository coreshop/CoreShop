/**
 * CoreShop RuleBundle Icon Library
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
import apiIcon from '../../assets/api.svg?react'
// @ts-ignore
import lightningIcon from '../../assets/lightning.svg?react'
// @ts-ignore
import searchIcon from '../../assets/search.svg?react'
// @ts-ignore
import warningIcon from '../../assets/warning.svg?react'

export const RuleBundleIconModule: AbstractModule = {
  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    iconLibrary.register({
      name: 'coreshop_rule',
      component: lightningIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rules',
      component: lightningIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_api',
      component: apiIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_search',
      component: searchIcon
    })
    
    iconLibrary.register({
      name: 'coreshop_rule_warning',
      component: warningIcon
    })
  }
}

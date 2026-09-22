/**
 * CoreShop PimcoreBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeFieldDefinitionCoreShopDynamicDropdownMultiple } from './DynamicTypeFieldDefinitionCoreShopDynamicDropdownMultiple'

export class DynamicTypeFieldDefinitionCoreShopItemSelector extends DynamicTypeFieldDefinitionCoreShopDynamicDropdownMultiple {
  id: string = 'coreShopItemSelector'

  getIcon(): ElementIcon {
    return { type: 'name', value: 'coreshop_item_selector' }
  }
}

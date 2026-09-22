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

import React from 'react'
import type { FieldDefinitionContext, FieldDefinitionData } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeFieldDefinitionCoreShopAbstract } from './field-definitions'
import { DynamicDropdownFormFields } from './field-definitions/components/DynamicDropdownFormFields'

/**
 * coreShopDynamicDropdownMultiple: multi select whose options are loaded from a
 * DataObject folder. Also the base for coreShopItemSelector and coreShopSuperBoxSelect.
 */
export class DynamicTypeFieldDefinitionCoreShopDynamicDropdownMultiple extends DynamicTypeFieldDefinitionCoreShopAbstract {
  id: string = 'coreShopDynamicDropdownMultiple'

  getIcon(): ElementIcon {
    return { type: 'name', value: 'coreshop_dynamic_dropdown_multiple' }
  }

  getDefaultData(): FieldDefinitionData {
    return {
      ...super.getDefaultData(),
      folderName: '/',
      methodName: 'getKey',
      recursive: false,
      sortBy: 'byid',
      onlyPublished: false
    }
  }

  getFormFields(context: FieldDefinitionContext): React.JSX.Element {
    return super.getFormFields({ ...context, disableIndex: true })
  }

  getSpecificFormFields(context: FieldDefinitionContext): React.JSX.Element {
    return <DynamicDropdownFormFields multiple />
  }
}

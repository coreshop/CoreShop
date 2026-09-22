/**
 * CoreShop ResourceBundle Studio Plugin
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
import {
  DynamicTypeFieldDefinitionManyToMany,
  type FieldDefinitionContext
} from '@pimcore/studio-ui-bundle/modules/field-definitions'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { CORESHOP_FIELD_DEFINITION_GROUP } from '@coreshop/pimcore/src/dynamic-types/field-definitions'
import { StackSelectFormItem } from './components/StackSelectFormItem'

/**
 * coreShopRelations: a Pimcore many-to-many relation restricted to a CoreShop resource stack.
 * Reuses Pimcore's many-to-many form and adds the stack option.
 */
export class DynamicTypeFieldDefinitionCoreShopRelations extends DynamicTypeFieldDefinitionManyToMany {
  id: string = 'coreShopRelations'

  getIcon(): ElementIcon {
    return { type: 'name', value: 'coreshop_field_definition_group' }
  }

  getGroup(): string[] {
    return ['data', CORESHOP_FIELD_DEFINITION_GROUP]
  }

  getSpecificFormFields(context: FieldDefinitionContext): React.JSX.Element {
    return (
      <>
        <StackSelectFormItem />
        {super.getSpecificFormFields(context)}
      </>
    )
  }
}

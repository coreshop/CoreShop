/**
 * CoreShop CoreBundle Studio Plugin
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
import type { FieldDefinitionContext } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeFieldDefinitionCoreShopAbstract, MinMaxFormItems, WidthFormItem } from '@coreshop/pimcore/src/dynamic-types/field-definitions'

export class DynamicTypeFieldDefinitionCoreShopStoreValues extends DynamicTypeFieldDefinitionCoreShopAbstract {
  id: string = 'coreShopStoreValues'

  getIcon(): ElementIcon {
    return { type: 'name', value: 'coreshop_store_values' }
  }

  getSpecificFormFields(context: FieldDefinitionContext): React.JSX.Element {
    return (
      <>
        <WidthFormItem />
        <MinMaxFormItems />
      </>
    )
  }
}

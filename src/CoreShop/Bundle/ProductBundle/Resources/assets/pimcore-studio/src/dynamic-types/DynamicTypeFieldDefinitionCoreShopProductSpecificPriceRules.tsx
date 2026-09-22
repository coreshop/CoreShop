/**
 * CoreShop ProductBundle Studio Plugin
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
import { DynamicTypeFieldDefinitionCoreShopAbstract, HeightFormItem } from '@coreshop/pimcore/src/dynamic-types/field-definitions'

export class DynamicTypeFieldDefinitionCoreShopProductSpecificPriceRules extends DynamicTypeFieldDefinitionCoreShopAbstract {
  id: string = 'coreShopProductSpecificPriceRules'

  getIcon(): ElementIcon {
    return { type: 'name', value: 'coreshop_product_specific_price_rules' }
  }

  getSpecificFormFields(context: FieldDefinitionContext): React.JSX.Element {
    return (
      <>
        <HeightFormItem />
      </>
    )
  }
}

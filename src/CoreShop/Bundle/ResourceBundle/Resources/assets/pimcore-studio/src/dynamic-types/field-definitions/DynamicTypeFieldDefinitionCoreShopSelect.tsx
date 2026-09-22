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
import type { FieldDefinitionContext, FieldDefinitionData } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { DynamicTypeFieldDefinitionCoreShopAbstract } from '@coreshop/pimcore/src/dynamic-types/field-definitions'
import { CoreShopSelectFormFields } from './components/CoreShopSelectFormFields'

/**
 * Class-definition type for every CoreShop single-select that extends
 * CoreShop\Bundle\ResourceBundle\CoreExtension\Select (store, currency, country, ...).
 * Subclasses set the id and icon only.
 */
export abstract class DynamicTypeFieldDefinitionCoreShopSelect extends DynamicTypeFieldDefinitionCoreShopAbstract {
  getDefaultData(): FieldDefinitionData {
    return {
      ...super.getDefaultData(),
      allowEmpty: false
    }
  }

  getSpecificFormFields(context: FieldDefinitionContext): React.JSX.Element {
    const id = this.getId(context)
    const fieldDefinition = context.fieldDefinitions[id]

    return (
      <CoreShopSelectFormFields
        context={context}
        id={fieldDefinition?.name ?? id}
        type={this.id}
      />
    )
  }
}

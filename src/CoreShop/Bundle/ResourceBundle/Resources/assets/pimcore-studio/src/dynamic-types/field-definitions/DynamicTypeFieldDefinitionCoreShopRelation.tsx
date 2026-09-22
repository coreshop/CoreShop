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
import { useTranslation } from 'react-i18next'
import {
  DynamicTypeFieldDefinitionManyToOne,
  type FieldDefinitionContext
} from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { Form, Switch } from '@pimcore/studio-ui-bundle/components'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { CORESHOP_FIELD_DEFINITION_GROUP } from '@coreshop/pimcore/src/dynamic-types/field-definitions'
import { StackSelectFormItem } from './components/StackSelectFormItem'

const CoreShopRelationFormFields = (): React.JSX.Element => {
  const { t } = useTranslation()

  return (
    <>
      <StackSelectFormItem />

      <Form.Item name="returnConcrete">
        <Switch labelRight={t('coreshop_field_definition_return_concrete')} />
      </Form.Item>
    </>
  )
}

/**
 * coreShopRelation: a Pimcore many-to-one relation restricted to a CoreShop resource stack.
 * Reuses Pimcore's many-to-one form and adds the stack + returnConcrete options.
 */
export class DynamicTypeFieldDefinitionCoreShopRelation extends DynamicTypeFieldDefinitionManyToOne {
  id: string = 'coreShopRelation'

  getIcon(): ElementIcon {
    return { type: 'name', value: 'coreshop_field_definition_group' }
  }

  getGroup(): string[] {
    return ['data', CORESHOP_FIELD_DEFINITION_GROUP]
  }

  getSpecificFormFields(context: FieldDefinitionContext): React.JSX.Element {
    return (
      <>
        <CoreShopRelationFormFields />
        {super.getSpecificFormFields(context)}
      </>
    )
  }
}

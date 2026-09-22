/**
 * CoreShop MoneyBundle Studio Plugin
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
import { Form, InputNumber, Switch } from '@pimcore/studio-ui-bundle/components'
import type { FieldDefinitionContext, FieldDefinitionData } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeFieldDefinitionCoreShopAbstract, MinMaxFormItems, WidthFormItem } from '@coreshop/pimcore/src/dynamic-types/field-definitions'

const MoneyFormFields = (): React.JSX.Element => {
  const { t } = useTranslation()

  return (
    <>
      <WidthFormItem />

      {/* Stored as integer (cents), see CLAUDE.md "Pricing" */}
      <Form.Item
        label={t('default-value')}
        name="defaultValue"
        tooltip={t('coreshop_field_definition_money_integer_tooltip')}
      >
        <InputNumber precision={0} />
      </Form.Item>

      <MinMaxFormItems precision={0} />

      <Form.Item name="nullable">
        <Switch labelRight={t('coreshop_field_definition_nullable')} />
      </Form.Item>
    </>
  )
}

export class DynamicTypeFieldDefinitionCoreShopMoney extends DynamicTypeFieldDefinitionCoreShopAbstract {
  id: string = 'coreShopMoney'

  getIcon(): ElementIcon {
    return { type: 'name', value: 'coreshop_money' }
  }

  getDefaultData(): FieldDefinitionData {
    return {
      ...super.getDefaultData(),
      nullable: false
    }
  }

  getSpecificFormFields(context: FieldDefinitionContext): React.JSX.Element {
    return <MoneyFormFields />
  }
}

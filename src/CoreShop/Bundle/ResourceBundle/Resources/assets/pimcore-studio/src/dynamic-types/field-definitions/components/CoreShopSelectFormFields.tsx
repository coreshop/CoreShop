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
import { Form, Switch } from '@pimcore/studio-ui-bundle/components'
import type { FieldDefinitionAbstractFormFieldsProps } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { WidthFormItem } from '@coreshop/pimcore/src/dynamic-types/field-definitions'

/**
 * Specific settings for CoreShop single-selects (CoreShop\Bundle\ResourceBundle\CoreExtension\Select).
 * These types only expose "width" and "allowEmpty".
 */
export const CoreShopSelectFormFields = (props: FieldDefinitionAbstractFormFieldsProps): React.JSX.Element => {
  const { t } = useTranslation()
  const isCustomLayout = props.context.area.includes('custom-layout')

  return (
    <>
      <WidthFormItem />

      {!isCustomLayout && (
        <Form.Item name="allowEmpty">
          <Switch labelRight={t('coreshop_field_definition_allow_empty')} />
        </Form.Item>
      )}
    </>
  )
}

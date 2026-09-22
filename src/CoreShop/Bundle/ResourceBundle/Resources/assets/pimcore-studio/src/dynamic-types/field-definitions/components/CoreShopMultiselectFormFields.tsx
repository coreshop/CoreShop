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
import { Form, InputNumber, Select } from '@pimcore/studio-ui-bundle/components'
import type { FieldDefinitionAbstractFormFieldsProps } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { WidthFormItem, HeightFormItem } from '@coreshop/pimcore/src/dynamic-types/field-definitions'

/**
 * Specific settings for CoreShop multiselects (extend Pimcore's Multiselect):
 * width, height, maxItems and renderType.
 */
export const CoreShopMultiselectFormFields = (props: FieldDefinitionAbstractFormFieldsProps): React.JSX.Element => {
  const { t } = useTranslation()
  const isCustomLayout = props.context.area.includes('custom-layout')

  return (
    <>
      <WidthFormItem />
      <HeightFormItem />

      {!isCustomLayout && (
        <>
          <Form.Item
            label={t('coreshop_field_definition_max_items')}
            name="maxItems"
          >
            <InputNumber
              min={0}
              precision={0}
            />
          </Form.Item>

          <Form.Item
            label={t('coreshop_field_definition_render_type')}
            name="renderType"
          >
            <Select
              options={[
                { label: t('coreshop_field_definition_render_type_list'), value: 'list' },
                { label: t('coreshop_field_definition_render_type_tags'), value: 'tags' }
              ]}
            />
          </Form.Item>
        </>
      )}
    </>
  )
}

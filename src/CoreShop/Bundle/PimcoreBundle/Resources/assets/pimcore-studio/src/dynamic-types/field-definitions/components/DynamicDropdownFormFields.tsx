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

import React, { useMemo } from 'react'
import { useTranslation } from 'react-i18next'
import { Form, Input, InputNumber, Select, Switch } from '@pimcore/studio-ui-bundle/components'
import { useClassDefinitionCollectionQuery } from '@pimcore/studio-ui-bundle/api/class-definition'
import { WidthFormItem } from './WidthFormItem'
import { HeightFormItem } from './HeightFormItem'

interface DynamicDropdownFormFieldsProps {
  /** Multi-value variants additionally expose height and maxItems. */
  multiple: boolean
}

/**
 * Specific settings shared by coreShopDynamicDropdown, coreShopDynamicDropdownMultiple,
 * coreShopItemSelector and coreShopSuperBoxSelect (DynamicDropdownTrait options).
 */
export const DynamicDropdownFormFields = ({ multiple }: DynamicDropdownFormFieldsProps): React.JSX.Element => {
  const { t } = useTranslation()
  const { data: classDefinitions } = useClassDefinitionCollectionQuery()

  const classOptions = useMemo(
    () => (classDefinitions?.items ?? []).map(item => ({ label: item.name, value: item.name })),
    [classDefinitions]
  )

  return (
    <>
      <WidthFormItem />

      {multiple && (
        <>
          <HeightFormItem />

          <Form.Item
            label={t('coreshop_field_definition_max_items')}
            name="maxItems"
          >
            <InputNumber
              min={0}
              precision={0}
            />
          </Form.Item>
        </>
      )}

      <Form.Item
        label={t('coreshop_field_definition_folder_name')}
        name="folderName"
        tooltip={t('coreshop_field_definition_folder_name_tooltip')}
      >
        <Input />
      </Form.Item>

      <Form.Item
        label={t('coreshop_field_definition_class_name')}
        name="className"
      >
        <Select
          allowClear
          options={classOptions}
          showSearch
        />
      </Form.Item>

      <Form.Item
        label={t('coreshop_field_definition_method_name')}
        name="methodName"
        tooltip={t('coreshop_field_definition_method_name_tooltip')}
      >
        <Input />
      </Form.Item>

      <Form.Item
        label={t('coreshop_field_definition_sort_by')}
        name="sortBy"
      >
        <Select
          options={[
            { label: t('coreshop_field_definition_sort_by_id'), value: 'byid' },
            { label: t('coreshop_field_definition_sort_by_value'), value: 'byvalue' }
          ]}
        />
      </Form.Item>

      <Form.Item name="recursive">
        <Switch labelRight={t('coreshop_field_definition_recursive')} />
      </Form.Item>

      <Form.Item name="onlyPublished">
        <Switch labelRight={t('coreshop_field_definition_only_published')} />
      </Form.Item>
    </>
  )
}

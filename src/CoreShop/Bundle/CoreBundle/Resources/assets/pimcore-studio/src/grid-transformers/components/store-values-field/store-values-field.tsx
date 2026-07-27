/**
 * CoreShop CoreBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { useTranslation } from 'react-i18next'
import { Form, Input } from '@pimcore/studio-ui-bundle/components'
import { StoreSelect } from '@coreshop/store/src/components/StoreSelect'

export const StoreValuesFieldTransformerComponent = (): React.JSX.Element => {
  const { t } = useTranslation()

  return (
    <>
      <Form.Item
        label={t('coreshop_grid_transformer_store_values_field_store')}
        name='storeId'
        rules={[{ required: true, message: t('coreshop_grid_transformer_store_values_field_store_required') }]}
      >
        <StoreSelect />
      </Form.Item>
      <Form.Item
        initialValue='price'
        label={t('coreshop_grid_transformer_store_values_field_field')}
        name='field'
        tooltip={t('coreshop_grid_transformer_store_values_field_field_help')}
      >
        <Input placeholder='price' />
      </Form.Item>
    </>
  )
}

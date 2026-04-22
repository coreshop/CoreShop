/**
 * CoreShop OrderBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { useTranslation } from 'react-i18next'
import { Form, Input } from '@pimcore/studio-ui-bundle/components'

export const PriceFormatterTransformerComponent = (): React.JSX.Element => {
  const { t } = useTranslation()

  return (
    <>
      <Form.Item
        label={t('coreshop_grid_transformer_price_formatter_currency_iso_code')}
        name='currencyIsoCode'
        tooltip={t('coreshop_grid_transformer_price_formatter_currency_iso_code_help')}
      >
        <Input placeholder='EUR' />
      </Form.Item>
      <Form.Item
        label={t('coreshop_grid_transformer_price_formatter_currency_field')}
        name='currencyField'
        tooltip={t('coreshop_grid_transformer_price_formatter_currency_field_help')}
      >
        <Input />
      </Form.Item>
    </>
  )
}

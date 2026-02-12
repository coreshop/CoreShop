/**
 * CoreShop CurrencyBundle - Currency Form Builder
 *
 * Base form builder for Currency entities.
 * Extensions can be added via decorators from other bundles.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { FormBuilder } from '@coreshop/studio-form/src/form-builder'
import { Input, InputNumber } from 'antd'
import type { CurrencyDetail } from './api'

/**
 * Create base Currency form builder
 *
 * Contains only fields that CurrencyBundle knows about.
 * Other bundles can extend via decorators.
 */
export const createCurrencyFormBuilder = (): FormBuilder<CurrencyDetail> => {
  const builder = new FormBuilder<CurrencyDetail>({
    fields: [
      {
        name: 'name',
        label: 'coreshop_currency',
        component: Input,
        required: true,
        rules: [
          { required: true, message: 'Name is required' }
        ]
      },
      {
        name: 'isoCode',
        label: 'coreshop_currency_isoCode',
        component: Input,
        componentProps: {
          placeholder: 'e.g., USD, EUR, GBP'
        }
      },
      {
        name: 'numericIsoCode',
        label: 'coreshop_currency_numericIsoCode',
        component: InputNumber,
        componentProps: {
          style: { width: '100%' },
          placeholder: 'e.g., 840, 978'
        }
      },
      {
        name: 'symbol',
        label: 'coreshop_currency_symbol',
        component: Input,
        componentProps: {
          placeholder: 'e.g., $, €, £'
        }
      }
    ]
  })

  return builder
}

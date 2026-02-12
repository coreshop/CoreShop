/**
 * CoreShop TaxationBundle - TaxRate Form Builder
 *
 * Base form builder for TaxRate entities.
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
import { Input, InputNumber, Switch } from 'antd'
import type { TaxRateDetail } from './api'

export const createTaxRateFormBuilder = (): FormBuilder<TaxRateDetail> => {
  const builder = new FormBuilder<TaxRateDetail>({
    fields: [
      {
        name: ['translations', '__LOCALE__', 'name'],
        label: 'Name',
        component: Input,
        required: true,
        rules: [
          { required: true, message: 'Name is required' }
        ],
        localized: true
      },
      {
        name: 'rate',
        label: 'coreshop_tax_rate',
        component: InputNumber,
        required: true,
        rules: [
          { required: true, message: 'Tax rate is required' }
        ],
        componentProps: {
          min: 0,
          max: 100,
          step: 0.01,
          style: { width: '100%' },
          addonAfter: '%'
        }
      },
      {
        name: 'active',
        label: 'active',
        component: Switch,
        valuePropName: 'checked'
      }
    ]
  })

  return builder
}

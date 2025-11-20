/**
 * CoreShop ProductBundle - ProductPriceRule Form Builder
 *
 * Base form builder for ProductPriceRule entities.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { FormBuilder } from '@coreshop/resource/src/entities/form-builder'
import { Input, InputNumber, Checkbox } from 'antd'
import type { ProductPriceRule } from './types'

export const createProductPriceRuleFormBuilder = (): FormBuilder<ProductPriceRule> => {
  const builder = new FormBuilder<ProductPriceRule>({
    fields: [
      {
        name: ['translations', '__LOCALE__', 'label'],
        label: 'Label',
        component: Input,
        localized: true
      },
      {
        name: 'name',
        label: 'Name',
        component: Input,
        required: true,
        rules: [
          { required: true, message: 'Name is required' }
        ]
      },
      {
        name: 'description',
        label: 'Description',
        component: Input.TextArea,
        componentProps: {
          rows: 4
        }
      },
      {
        name: 'active',
        label: 'Active',
        component: Checkbox
      },
      {
        name: 'priority',
        label: 'coreshop_priority',
        component: InputNumber,
        componentProps: {
          style: { width: '100%' }
        }
      }
    ]
  })

  return builder
}

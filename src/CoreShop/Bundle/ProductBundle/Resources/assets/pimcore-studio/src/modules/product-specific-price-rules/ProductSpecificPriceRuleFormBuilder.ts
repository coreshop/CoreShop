/**
 * CoreShop ProductBundle - ProductSpecificPriceRule Form Builder
 *
 * Base form builder for ProductSpecificPriceRule entities.
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
import type { ProductSpecificPriceRule } from './types'

export const createProductSpecificPriceRuleFormBuilder = (): FormBuilder<ProductSpecificPriceRule> => {
  const builder = new FormBuilder<ProductSpecificPriceRule>({
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
      },
      {
        name: 'inherit',
        label: 'Inherit from Parent',
        component: Checkbox
      }
    ]
  })

  return builder
}

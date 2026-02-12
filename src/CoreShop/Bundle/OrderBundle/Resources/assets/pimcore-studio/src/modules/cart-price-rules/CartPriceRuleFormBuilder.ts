/**
 * CoreShop OrderBundle - CartPriceRule Form Builder
 *
 * Base form builder for CartPriceRule entities.
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
import type { CartPriceRule } from './types'

export const createCartPriceRuleFormBuilder = (): FormBuilder<CartPriceRule> => {
  const builder = new FormBuilder<CartPriceRule>({
    fields: [
      {
        name: ['translations', '__LOCALE__', 'label'],
        label: 'Label',
        component: Input,
        componentProps: {
          placeholder: 'Rule label'
        },
        localized: true
      },
      {
        name: 'name',
        label: 'Name',
        component: Input,
        required: true,
        rules: [
          { required: true, message: 'Name is required' }
        ],
        componentProps: {
          placeholder: 'Rule name'
        }
      },
      {
        name: 'description',
        label: 'Description',
        component: Input.TextArea,
        componentProps: {
          placeholder: 'Enter description',
          rows: 4
        }
      },
      {
        name: 'active',
        label: 'Active',
        component: Switch,
        valuePropName: 'checked'
      },
      {
        name: 'priority',
        label: 'Priority',
        component: InputNumber,
        componentProps: {
          style: { width: '100%' }
        }
      },
      {
        name: 'isVoucherRule',
        label: 'Is Voucher Rule',
        component: Switch,
        valuePropName: 'checked'
      }
    ]
  })

  return builder
}

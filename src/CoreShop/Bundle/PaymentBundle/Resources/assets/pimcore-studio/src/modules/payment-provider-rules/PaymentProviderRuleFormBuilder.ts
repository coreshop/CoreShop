/**
 * CoreShop PaymentBundle - PaymentProviderRule Form Builder
 *
 * Base form builder for PaymentProviderRule entities.
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
import { Input, Switch } from 'antd'
import type { PaymentProviderRule } from './types'

export const createPaymentProviderRuleFormBuilder = (): FormBuilder<PaymentProviderRule> => {
  const builder = new FormBuilder<PaymentProviderRule>({
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
        name: 'active',
        label: 'Active',
        component: Switch,
        valuePropName: 'checked'
      }
    ]
  })

  return builder
}

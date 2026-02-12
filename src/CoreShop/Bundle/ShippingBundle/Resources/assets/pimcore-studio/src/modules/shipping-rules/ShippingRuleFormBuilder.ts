/**
 * CoreShop ShippingBundle - ShippingRule Form Builder
 *
 * Base form builder for ShippingRule entities.
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
import type { ShippingRuleDetail } from './api'

export const createShippingRuleFormBuilder = (): FormBuilder<ShippingRuleDetail> => {
  const builder = new FormBuilder<ShippingRuleDetail>({
    fields: [
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

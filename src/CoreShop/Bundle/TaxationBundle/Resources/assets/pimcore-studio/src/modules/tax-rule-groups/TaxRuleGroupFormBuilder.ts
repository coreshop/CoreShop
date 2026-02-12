/**
 * CoreShop TaxationBundle - TaxRuleGroup Form Builder
 *
 * Base form builder for TaxRuleGroup entities.
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
import { Input, Switch } from 'antd'
import type { TaxRuleGroupDetail } from './api'

export const createTaxRuleGroupFormBuilder = (): FormBuilder<TaxRuleGroupDetail> => {
  const builder = new FormBuilder<TaxRuleGroupDetail>({
    fields: [
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
        label: 'active',
        component: Switch,
        valuePropName: 'checked'
      }
    ]
  })

  return builder
}

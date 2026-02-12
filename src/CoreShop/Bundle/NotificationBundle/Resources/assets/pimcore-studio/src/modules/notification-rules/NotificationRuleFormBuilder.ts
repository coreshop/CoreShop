/**
 * CoreShop NotificationBundle - NotificationRule Form Builder
 *
 * Base form builder for NotificationRule entities.
 * Note: The 'type' field is handled separately in SettingsForm due to
 * dynamic options and special onTypeChange behavior.
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
import type { NotificationRule } from './types'

export const createNotificationRuleFormBuilder = (): FormBuilder<NotificationRule> => {
  const builder = new FormBuilder<NotificationRule>({
    fields: [
      {
        name: 'name',
        label: 'coreshop_name',
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
        label: 'coreshop_active',
        component: Switch,
        valuePropName: 'checked'
      },
      {
        name: ['translations', '__LOCALE__', 'label'],
        label: 'coreshop_label',
        component: Input,
        localized: true,
        componentProps: {
          placeholder: 'Rule label'
        }
      }
    ]
  })

  return builder
}

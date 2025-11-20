/**
 * CoreShop AddressBundle - Zone Form Builder
 *
 * Base form builder for Zone entities.
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
import type { ZoneDetail } from './api'
import { CountryMultiSelectField } from '../../components/CountryMultiSelectField'

export const createZoneFormBuilder = (): FormBuilder<ZoneDetail> => {
  const builder = new FormBuilder<ZoneDetail>({
    fields: [
      {
        name: 'name',
        label: 'coreshop_zone',
        component: Input,
        required: true,
        rules: [
          { required: true, message: 'Name is required' }
        ]
      },
      {
        name: 'countries',
        label: 'coreshop_countries',
        component: CountryMultiSelectField
      },
      {
        name: 'active',
        label: 'active',
        component: Switch
      }
    ]
  })

  return builder
}

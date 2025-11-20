/**
 * CoreShop AddressBundle - State Form Builder
 *
 * Base form builder for State entities.
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

import { FormBuilder } from '@coreshop/resource/src/entities/form-builder'
import { Input, Switch } from 'antd'
import type { StateDetail } from './api'
import { CountrySelectField } from '../../components/CountrySelectField'

/**
 * Create base State form builder
 *
 * Contains only fields that AddressBundle knows about.
 * Other bundles can extend via decorators.
 */
export const createStateFormBuilder = (): FormBuilder<StateDetail> => {
  const builder = new FormBuilder<StateDetail>({
    fields: [
      {
        name: 'isoCode',
        label: 'coreshop_state_isoCode',
        component: Input,
        componentProps: {
          placeholder: 'e.g., CA, NY, TX'
        }
      },
      {
        name: 'country',
        label: 'coreshop_state_country',
        component: CountrySelectField
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

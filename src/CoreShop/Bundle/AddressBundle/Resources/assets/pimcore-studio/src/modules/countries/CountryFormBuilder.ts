/**
 * CoreShop AddressBundle - Country Form Builder
 *
 * Base form builder for Country entities.
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

import { FormBuilder } from '@coreshop/studio-form/src/form-builder'
import { Input, Switch, Select } from 'antd'
import type { CountryDetail } from './api'
import { ZoneSelect } from '../zones/ZoneSelect'

/**
 * Create base Country form builder
 *
 * Contains only fields that AddressBundle knows about.
 * Other bundles can extend via decorators.
 *
 * NOTE: No sections used for Country - just flat form fields.
 * Sections are available for other forms that need them.
 */
export const createCountryFormBuilder = (): FormBuilder<CountryDetail> => {
  const builder = new FormBuilder<CountryDetail>({
    fields: [
      {
        name: 'name',
        label: 'coreshop_country',
        component: Input,
        required: true,
        localized: true,
        rules: [
          { required: true, message: 'Name is required' }
        ]
      },
      {
        name: 'isoCode',
        label: 'coreshop_country_isoCode',
        component: Input,
        required: true,
        rules: [
          { required: true, message: 'ISO Code is required' },
        ]
      },
      {
        name: 'active',
        label: 'active',
        component: Switch,
        valuePropName: 'checked'
      },
      {
        name: 'zone',
        label: 'coreshop_zone',
        component: ZoneSelect
      },
      {
        name: 'addressFormat',
        label: 'coreshop_country_addressFormat',
        component: Input.TextArea,
        componentProps: {
          rows: 4,
          placeholder: '{{firstname}} {{lastname}}\n{{street}}\n{{postcode}} {{city}}'
        }
      },
      {
        name: 'salutations',
        label: 'coreshop_country_salutations',
        component: Select,
        componentProps: {
          mode: 'tags',
          placeholder: 'Mr, Mrs, Ms, Dr, etc.'
        }
      }
    ]
  })

  // No sections for Country - keep it simple
  // Sections are still available in the FormBuilder for other forms

  return builder
}

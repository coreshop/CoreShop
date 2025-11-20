/**
 * CoreShop CoreBundle - Country Form Extensions
 *
 * Extends the Country form with CoreBundle-specific fields.
 * CoreBundle has dependencies on all other bundles, so it can add
 * fields that reference entities from other bundles (Currency, etc.)
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import type { FormBuilder } from '@coreshop/resource/src/entities/form-builder'
import { addFieldDecorator } from '@coreshop/resource/src/entities/form-builder'
import type { CountryDetail } from '@coreshop/address/src/modules/countries/api'
import { CurrencySelectField } from '@coreshop/currency/src/components/CurrencySelectField'

/**
 * Country Form Extension Module
 *
 * CoreBundle acts as the "glue" layer and extends forms from other bundles
 * with fields that require cross-bundle dependencies.
 *
 * CoreBundle has dependency on CurrencyBundle, so it can add currency field.
 */
export const CountryFormExtensionModule: AbstractModule = {
  onInit(): void {
    try {
      const builder = container.get<FormBuilder<CountryDetail>>(
        'CoreShop/Address/Country/FormBuilder'
      )

      builder.addDecorator('currency-field', addFieldDecorator({
        name: 'currency',
        label: 'coreshop_country_currency',
        component: CurrencySelectField,
        componentProps: {
          placeholder: 'Select currency...',
          allowClear: true
        }
      }))
    } catch (err) {
      console.error('[CoreBundle] Failed to extend Country form:', err)
    }
  }
}

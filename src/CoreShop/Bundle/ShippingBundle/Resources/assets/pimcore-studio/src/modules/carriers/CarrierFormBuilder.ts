/**
 * CoreShop ShippingBundle - Carrier Form Builder
 *
 * Base form builder for Carrier entities.
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
import { Input, Select, Switch } from 'antd'
import type { CarrierDetail, CarrierConfig } from './api'
import { AssetSelect } from '@coreshop/pimcore/src/components/AssetSelect'

export const createCarrierFormBuilder = (config: CarrierConfig): FormBuilder<CarrierDetail> => {
  const builder = new FormBuilder<CarrierDetail>({
    fields: [
      {
        name: 'identifier',
        label: 'Identifier',
        component: Input,
        required: true,
        rules: [
          { required: true, message: 'Identifier is required' }
        ],
        componentProps: {
          placeholder: 'Carrier identifier'
        }
      },
      {
        name: 'trackingUrl',
        label: 'Tracking URL',
        component: Input,
        componentProps: {
          placeholder: 'e.g., https://tracking.example.com/{tracking_code}'
        }
      },
      {
        name: 'logo',
        label: 'Logo',
        component: AssetSelect,
        componentProps: {
          accept: ['asset', 'asset:image'],
          placeholder: 'Drop image asset here or enter ID'
        }
      },
      {
        name: ['translations', '__LOCALE__', 'title'],
        label: 'Title',
        component: Input,
        required: true,
        rules: [
          { required: true, message: 'Title is required' }
        ],
        componentProps: {
          placeholder: 'Carrier title'
        },
        localized: true
      },
      {
        name: ['translations', '__LOCALE__', 'description'],
        label: 'Description',
        component: Input.TextArea,
        componentProps: {
          rows: 3,
          placeholder: 'Carrier description'
        },
        localized: true
      },
      {
        name: 'taxCalculationStrategy',
        label: 'Tax Calculation Strategy',
        component: Select,
        componentProps: {
          placeholder: 'Select strategy',
          options: config.taxCalculationStrategies.map(s => ({
            value: s.value,
            label: s.label
          }))
        }
      },
      {
        name: 'hideFromCheckout',
        label: 'Hide From Checkout',
        component: Switch,
        valuePropName: 'checked'
      }
    ]
  })

  return builder
}

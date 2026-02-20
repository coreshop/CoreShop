/**
 * CoreShop AddressBundle - Country Form (Schema Form Version)
 *
 * Form component using the SchemaForm pattern.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { SchemaForm, type SchemaFormProps } from '@coreshop/studio-form/src/schema-adapter'
import type { CountryDetail } from './api'

export const CountryForm: React.FC<Omit<SchemaFormProps<CountryDetail>, 'blockPrefix'>> = (props) => {
  return (
    <div style={{ padding: 12 }}>
      <SchemaForm<CountryDetail>
        {...props}
        blockPrefix="coreshop_country"
      />
    </div>
  )
}

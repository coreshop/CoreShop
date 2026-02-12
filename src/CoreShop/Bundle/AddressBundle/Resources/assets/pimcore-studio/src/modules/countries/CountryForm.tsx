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
import { SchemaForm } from '@coreshop/studio-form/src/schema-adapter'
import type { CountryDetail } from './api'

export interface CountryFormProps {
  data?: CountryDetail
  onChange: (draft: Partial<CountryDetail>) => void
  currentLocale?: string
  locales?: string[]
}

/**
 * Country Form Component
 *
 * Uses SchemaForm pattern for composable, extensible form configuration.
 * Base form is defined in AddressBundle, extensions added by CoreBundle and others.
 */
export const CountryForm: React.FC<CountryFormProps> = ({
  data,
  onChange,
  currentLocale,
}) => {
  return (
    <div style={{ padding: 12 }}>
      <SchemaForm<CountryDetail>
        alias="coreshop.country"
        data={data}
        onChange={onChange}
        currentLocale={currentLocale}
      />
    </div>
  )
}

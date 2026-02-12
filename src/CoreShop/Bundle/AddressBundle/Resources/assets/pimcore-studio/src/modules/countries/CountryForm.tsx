/**
 * CoreShop AddressBundle - Country Form (Form Builder Version)
 *
 * Form component using the new FormBuilder pattern.
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
import { container } from '@pimcore/studio-ui-bundle'
import { DynamicForm, type FormBuilder } from '@coreshop/studio-form/src/form-builder'
import type { CountryDetail } from './api'

export interface CountryFormProps {
  data?: CountryDetail
  onChange: (draft: Partial<CountryDetail>) => void
  currentLocale: string
  locales?: string[]
}

/**
 * Country Form Component
 *
 * Uses FormBuilder pattern for composable, extensible form configuration.
 * Base form is defined in AddressBundle, extensions added by CoreBundle and others.
 */
export const CountryForm: React.FC<CountryFormProps> = ({
  data,
  onChange,
  currentLocale,
  locales
}) => {
  // Get the form builder from container
  const builder = container.get<FormBuilder<CountryDetail>>(
    'CoreShop/Address/Country/FormBuilder'
  )

  // Build final config with all decorators applied
  const config = React.useMemo(() => {
    return builder.build({
      data,
      locale: currentLocale,
      locales
    })
  }, [builder, data, currentLocale, locales])

  return (
    <div style={{ padding: 12 }}>
      <DynamicForm
        config={config}
        data={data}
        onChange={onChange}
        currentLocale={currentLocale}
      />
    </div>
  )
}

/**
 * CoreShop AddressBundle - State Form (Schema Form Version)
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
import type { StateDetail } from './api'

export interface StateFormProps {
  data?: StateDetail
  onChange: (draft: Partial<StateDetail>) => void
  currentLocale?: string
  locales?: string[]
}

/**
 * State Form Component
 *
 * Uses SchemaForm pattern for composable, extensible form configuration.
 * Base form is defined in AddressBundle, extensions can be added by other bundles.
 */
export const StateForm: React.FC<StateFormProps> = ({
  data,
  onChange,
  currentLocale,
}) => {
  return (
    <div style={{ padding: 12 }}>
      <SchemaForm<StateDetail>
        alias="coreshop.state"
        data={data}
        onChange={onChange}
        currentLocale={currentLocale}
      />
    </div>
  )
}

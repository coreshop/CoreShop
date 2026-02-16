/**
 * CoreShop IndexBundle Filter Settings Form
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
import type { Filter } from '../types'

interface SettingsFormProps {
  filter: Filter
  onChange: (filter: Filter) => void
}

export const SettingsForm: React.FC<SettingsFormProps> = ({
  filter,
  onChange
}) => {
  return (
    <div style={{ padding: 24 }}>
      <SchemaForm<Filter>
        blockPrefix="coreshop_filter"
        data={filter}
        onChange={(draft) => onChange({ ...filter, ...draft })}
      />
    </div>
  )
}

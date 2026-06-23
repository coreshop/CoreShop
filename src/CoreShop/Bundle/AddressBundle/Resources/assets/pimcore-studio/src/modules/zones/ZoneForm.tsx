/**
 * CoreShop AddressBundle - Zone Form (Schema Form Version)
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
import type { ZoneDetail } from './api'

export const ZoneForm: React.FC<Omit<SchemaFormProps<ZoneDetail>, 'blockPrefix'>> = (props) => {
  return (
    <div style={{ padding: 12 }}>
      <SchemaForm<ZoneDetail>
        {...props}
        blockPrefix="coreshop_zone"
      />
    </div>
  )
}

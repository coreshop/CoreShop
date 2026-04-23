/**
 * CoreShop ProductBundle Studio Plugin
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
import type { ProductUnitDetail } from './api'

export const ProductUnitForm: React.FC<Omit<SchemaFormProps<ProductUnitDetail>, 'blockPrefix'>> = (props) => {
  return (
    <div style={{ padding: 12 }}>
      <SchemaForm<ProductUnitDetail>
        {...props}
        blockPrefix="coreshop_product_unit"
      />
    </div>
  )
}

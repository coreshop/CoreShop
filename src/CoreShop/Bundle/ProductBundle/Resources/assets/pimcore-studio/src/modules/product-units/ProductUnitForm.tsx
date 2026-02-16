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
import { SchemaForm } from '@coreshop/studio-form/src/schema-adapter'
import type { ProductUnitDetail } from './api'
import { renderEntityFormExtensions } from '@coreshop/resource/src/entities'

export interface ProductUnitFormProps {
  data?: ProductUnitDetail
  onChange: (draft: Partial<ProductUnitDetail>) => void
  currentLocale?: string
  locales?: string[]
}

export const ProductUnitForm: React.FC<ProductUnitFormProps> = ({
  data,
  onChange,
  currentLocale,
}) => {
  return (
    <div style={{ padding: 12 }}>
      <SchemaForm<ProductUnitDetail>
        blockPrefix="coreshop_product_unit"
        data={data}
        onChange={onChange}
        currentLocale={currentLocale}
      />
      {renderEntityFormExtensions('coreshop.product.product_unit.form', { data, onChange, currentLocale })}
    </div>
  )
}

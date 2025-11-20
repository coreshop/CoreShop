/**
 * CoreShop StoreBundle Studio Plugin
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
import { DynamicForm, type FormBuilder } from '@coreshop/resource/src/entities/form-builder'
import type { StoreDetail } from './api'

export const StoreForm: React.FC<{
  data?: StoreDetail
  onChange: (draft: Partial<StoreDetail>) => void
}> = ({ data, onChange }) => {
  const builder = container.get<FormBuilder<StoreDetail>>('CoreShop/Store/Store/FormBuilder')
  const config = React.useMemo(() => builder.build({ data }), [builder, data])

  return (
    <div style={{ padding: 12 }}>
      <DynamicForm config={config} data={data} onChange={onChange} />
    </div>
  )
}

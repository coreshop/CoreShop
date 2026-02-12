/**
 * CoreShop OrderBundle Studio Plugin
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
import type { CartPriceRule } from '../types'

interface SettingsFormProps {
  rule: CartPriceRule
  onChange: (rule: CartPriceRule) => void
  currentLocale: string
  locales?: string[]
}

export const SettingsForm: React.FC<SettingsFormProps> = ({
  rule,
  onChange,
  currentLocale,
  locales
}) => {
  const builder = container.get<FormBuilder<CartPriceRule>>('CoreShop/Order/CartPriceRule/FormBuilder')
  const config = React.useMemo(() => builder.build({ data: rule, locale: currentLocale, locales }), [builder, rule, currentLocale, locales])

  return (
    <div style={{ padding: 12 }}>
      <DynamicForm config={config} data={rule} onChange={onChange} currentLocale={currentLocale} />
    </div>
  )
}

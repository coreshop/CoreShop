/**
 * CoreShop TaxationBundle Studio Plugin
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
import type { TaxRuleGroupDetail } from './api'
import { renderEntityFormExtensions } from '@coreshop/resource/src/entities'

export interface TaxRuleGroupFormProps {
  data?: TaxRuleGroupDetail
  onChange: (draft: Partial<TaxRuleGroupDetail>) => void
  currentLocale?: string
  locales?: string[]
}

export const TaxRuleGroupForm: React.FC<TaxRuleGroupFormProps> = ({
  data,
  onChange,
  currentLocale,
}) => {
  return (
    <div style={{ padding: 12 }}>
      <SchemaForm<TaxRuleGroupDetail>
        blockPrefix="coreshop_taxation_tax_rule_group"
        data={data}
        onChange={onChange}
        currentLocale={currentLocale}
      />
      {renderEntityFormExtensions('coreshop.taxation.tax_rule_group.form', { data, onChange, currentLocale })}
    </div>
  )
}

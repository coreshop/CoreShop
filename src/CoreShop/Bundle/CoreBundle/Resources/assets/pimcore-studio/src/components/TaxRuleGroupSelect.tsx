/**
 * CoreShop CoreBundle Studio Plugin
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
import type { SelectProps } from 'antd'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'
import { createOptionsLoader } from '@coreshop/resource/src/utils/createOptionsLoader'
import { EntitySelect } from '@coreshop/resource/src/components/EntitySelect'
import { taxRuleGroupApi } from '@coreshop/taxation/src/modules/tax-rule-groups/api'

const { load: loadTaxRuleGroups, getCache: getTaxRuleGroupCache, clearCache: clearTaxRuleGroupCache } = createOptionsLoader(async () => {
  const groups = await taxRuleGroupApi.list()
  return groups.map(group => ({
    value: group.id!,
    label: group.name ?? `#${group.id}`
  }))
})

export { clearTaxRuleGroupCache }

export const TaxRuleGroupSelect: React.FC<SelectProps> = (props) => {
  return (
    <DroppableEntity
      accept='coreshop:tax_rule_group'
      isValidData={(info) => typeof info?.data?.id === 'number'}
      onDrop={(info) => {
        if (props.onChange && info?.data?.id) {
          const event = { target: { value: info.data.id } } as any
          props.onChange(info.data.id, event)
        }
      }}
    >
      <EntitySelect
        {...props}
        loadOptions={loadTaxRuleGroups}
        getCachedOptions={getTaxRuleGroupCache}
      />
    </DroppableEntity>
  )
}

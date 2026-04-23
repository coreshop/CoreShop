import React from 'react'
import type { SelectProps } from 'antd'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'
import { createOptionsLoader } from '@coreshop/resource/src/utils/createOptionsLoader'
import { EntitySelect } from '@coreshop/resource/src/components/EntitySelect'
import { taxRuleGroupApi } from '../modules/tax-rule-groups/api'

const { load: loadTaxRuleGroups, getCache: getTaxRuleGroupCache, clearCache: clearTaxRuleGroupCache } = createOptionsLoader(async () => {
  const rows = await taxRuleGroupApi.list()
  return (Array.isArray(rows) ? rows : [])
    .map((r: any) => ({ value: r.id, label: r.name ?? String(r.id) }))
    .filter((o: any) => o.value != null && o.label)
})

export { loadTaxRuleGroups, getTaxRuleGroupCache, clearTaxRuleGroupCache }

export const TaxRuleGroupSelect: React.FC<SelectProps> = (props) => {
  return (
    <DroppableEntity
      accept="coreshop:tax_rule_group"
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

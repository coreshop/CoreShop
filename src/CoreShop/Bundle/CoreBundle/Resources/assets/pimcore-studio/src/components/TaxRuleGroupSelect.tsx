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
import { Select, SelectProps } from 'antd'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'

interface TaxRuleGroup {
  id: number
  name: string
}

interface TaxRuleGroupSelectProps extends Omit<SelectProps, 'options'> {
  value?: number
  onChange?: (value: number | undefined) => void
}

export const TaxRuleGroupSelect: React.FC<TaxRuleGroupSelectProps> = ({ 
  value, 
  onChange,
  ...selectProps 
}) => {
  const [taxRuleGroups, setTaxRuleGroups] = React.useState<TaxRuleGroup[]>([])
  const [loading, setLoading] = React.useState(false)

  React.useEffect(() => {
    const loadTaxRuleGroups = async () => {
      setLoading(true)
      try {
        // This would need to be imported from TaxationBundle API
        const response = await fetch('/pimcore-studio/api/coreshop/tax_rule_groups')
        const data = await response.json()
        setTaxRuleGroups(data.data || [])
      } catch (error) {
        console.error('Failed to load tax rule groups:', error)
      } finally {
        setLoading(false)
      }
    }
    void loadTaxRuleGroups()
  }, [])

  return (
    <DroppableEntity
      accept='coreshop:tax_rule_group'
      isValidData={(info) => typeof info?.data?.id === 'number'}
      onDrop={(info) => {
        const id = info?.data?.id
        if (typeof id === 'number' && onChange) {
          onChange(id)
        }
      }}
    >
      <Select
        {...selectProps}
        value={value}
        onChange={onChange}
        loading={loading}
        options={taxRuleGroups.map(group => ({
          value: group.id,
          label: group.name
        }))}
        placeholder="Select or drop a tax rule group"
        allowClear
      />
    </DroppableEntity>
  )
}
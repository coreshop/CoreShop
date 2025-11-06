/**
 * Comprehensive Extension Example
 *
 * This module demonstrates ALL extension types available in CoreShop Studio v2:
 * 1. Form Extensions - Add fields to forms
 * 2. Table Column Extensions - Add columns to tables
 * 3. Save Decorator Extensions - Transform save payloads
 * 4. Tab Extensions - Add custom tabs
 * 5. Action Extensions - Add toolbar/context menu buttons
 * 6. Validation Extensions - Add custom validation
 * 7. Lifecycle Hooks - Hook into entity lifecycle events
 *
 * This example extends the Country entity from AddressBundle
 */

import React from 'react'
import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { Form, Input, Button, message } from 'antd'
import {
  entityFormExtensionsServiceId,
  entityTableColumnExtensionsServiceId,
  entitySaveDecoratorsServiceId,
  entityTabExtensionsServiceId,
  entityActionExtensionsServiceId,
  entityValidationExtensionsServiceId,
  entityLifecycleHooksServiceId,
  type EntityFormExtensionRegistry,
  type EntityTableColumnExtensionRegistry,
  type EntitySaveDecoratorRegistry,
  type EntityTabExtensionRegistry,
  type EntityActionExtensionRegistry,
  type EntityValidationExtensionRegistry,
  type EntityLifecycleHookRegistry
} from '@coreshop/resource/src/entities'

export const ComprehensiveExtensionExample: AbstractModule = {
  onInit(): void {
    registerFormExtensions()
    registerTableColumnExtensions()
    registerSaveDecorators()
    registerTabExtensions()
    registerActionExtensions()
    registerValidationExtensions()
    registerLifecycleHooks()
  }
}

// ==============================================
// 1. FORM EXTENSIONS
// ==============================================

function registerFormExtensions(): void {
  const registry = container.get<EntityFormExtensionRegistry>(entityFormExtensionsServiceId)

  // Add custom field to Country form
  registry.add('coreshop.address.country.form', ({ data, onChange, form }) => {
    return (
      <Form.Item
        label="Custom Field (Example)"
        name="customField"
        help="This field was added via Form Extension"
      >
        <Input
          placeholder="Enter custom data"
          onChange={(e) => onChange({ customField: e.target.value })}
        />
      </Form.Item>
    )
  })

  console.log('[Extension Example] Form extensions registered')
}

// ==============================================
// 2. TABLE COLUMN EXTENSIONS
// ==============================================

function registerTableColumnExtensions(): void {
  const registry = container.get<EntityTableColumnExtensionRegistry>(entityTableColumnExtensionsServiceId)

  // Add custom column to a nested table (example: could be used in any table)
  registry.add('coreshop.taxation.tax_rule_group.tax_rules', ({ updateRecord }) => [
    {
      title: 'Custom Column',
      dataIndex: 'customColumn',
      width: 150,
      render: (value: string | undefined, record: any, index: number) => (
        <Input
          value={value}
          onChange={(e) => updateRecord(index, 'customColumn', e.target.value)}
          placeholder="Custom data"
          size="small"
        />
      )
    }
  ])

  console.log('[Extension Example] Table column extensions registered')
}

// ==============================================
// 3. SAVE DECORATOR EXTENSIONS
// ==============================================

function registerSaveDecorators(): void {
  const registry = container.get<EntitySaveDecoratorRegistry>(entitySaveDecoratorsServiceId)

  // Transform Country save payload
  registry.add('coreshop.address.country', (payload, data) => {
    console.log('[Extension Example] Save decorator: transforming payload', payload)

    // Add computed fields or modify payload before save
    return {
      ...payload,
      // Example: Add timestamp
      lastModifiedByExtension: new Date().toISOString(),
      // Example: Transform custom field
      customFieldProcessed: payload.customField ? payload.customField.toUpperCase() : undefined
    }
  })

  console.log('[Extension Example] Save decorators registered')
}

// ==============================================
// 4. TAB EXTENSIONS
// ==============================================

function registerTabExtensions(): void {
  const registry = container.get<EntityTabExtensionRegistry>(entityTabExtensionsServiceId)

  // Add custom tab to Country manager
  registry.add('coreshop.address.country', ({ data }) => ({
    key: 'custom-tab',
    label: 'Custom Tab',
    icon: 'settings',
    render: (tabData, onChange, ctx) => (
      <div style={{ padding: 20 }}>
        <h3>Custom Tab Content</h3>
        <p>This tab was added via Tab Extension</p>
        <p>Current country: {tabData?.name || 'New'}</p>

        <Form layout="vertical">
          <Form.Item label="Custom Setting 1">
            <Input
              value={(tabData as any)?.customSetting1}
              onChange={(e) => onChange({ customSetting1: e.target.value })}
              placeholder="Enter custom setting"
            />
          </Form.Item>

          <Form.Item label="Custom Setting 2">
            <Input
              value={(tabData as any)?.customSetting2}
              onChange={(e) => onChange({ customSetting2: e.target.value })}
              placeholder="Enter another setting"
            />
          </Form.Item>
        </Form>
      </div>
    )
  }))

  console.log('[Extension Example] Tab extensions registered')
}

// ==============================================
// 5. ACTION EXTENSIONS
// ==============================================

function registerActionExtensions(): void {
  const registry = container.get<EntityActionExtensionRegistry>(entityActionExtensionsServiceId)

  // Add toolbar action
  registry.add('coreshop.address.country', ({ data, position }) => {
    if (position !== 'toolbar') return null

    return {
      key: 'custom-export',
      label: 'Export',
      type: 'default',
      onClick: async (entityData) => {
        console.log('[Extension Example] Exporting country:', entityData)
        await message.info(`Exporting ${entityData?.name || 'entity'}...`)
        // Implement actual export logic here
      }
    }
  })

  // Add context menu action
  registry.add('coreshop.address.country', ({ data, position }) => {
    if (position !== 'context-menu') return null

    return {
      key: 'custom-duplicate',
      label: 'Duplicate',
      onClick: async (entityData) => {
        console.log('[Extension Example] Duplicating country:', entityData)
        await message.info(`Duplicating ${entityData?.name || 'entity'}...`)
        // Implement actual duplicate logic here
      }
    }
  })

  console.log('[Extension Example] Action extensions registered')
}

// ==============================================
// 6. VALIDATION EXTENSIONS
// ==============================================

function registerValidationExtensions(): void {
  const registry = container.get<EntityValidationExtensionRegistry>(entityValidationExtensionsServiceId)

  // Add custom validation for Country
  registry.add('coreshop.address.country', async (data, context) => {
    const errors: Record<string, string[]> = {}

    // Example: Check if ISO code is uppercase
    if (data.isoCode && data.isoCode !== data.isoCode.toUpperCase()) {
      errors.isoCode = ['ISO code must be uppercase']
    }

    // Example: Async validation (e.g., check uniqueness)
    if (data.name) {
      const isDuplicate = false // await checkDuplicateName(data.name)
      if (isDuplicate) {
        errors.name = ['Country name already exists']
      }
    }

    // Example: Cross-field validation
    if (data.customField && data.customSetting1) {
      if (data.customField === data.customSetting1) {
        errors.customField = ['Custom field and custom setting 1 cannot be the same']
      }
    }

    const valid = Object.keys(errors).length === 0

    if (!valid) {
      console.log('[Extension Example] Validation failed:', errors)
    }

    return { valid, errors: valid ? undefined : errors }
  })

  console.log('[Extension Example] Validation extensions registered')
}

// ==============================================
// 7. LIFECYCLE HOOKS
// ==============================================

function registerLifecycleHooks(): void {
  const registry = container.get<EntityLifecycleHookRegistry>(entityLifecycleHooksServiceId)

  // Before load - can modify data before it's displayed
  registry.add('coreshop.address.country', 'beforeLoad', (data, context) => {
    console.log('[Extension Example] beforeLoad hook:', context?.id)
    return data
  })

  // After load - can enrich data after loading
  registry.add('coreshop.address.country', 'afterLoad', (data, context) => {
    console.log('[Extension Example] afterLoad hook:', data)

    // Example: Add computed properties
    return {
      ...data,
      _loadedAt: new Date().toISOString(),
      _loadedBy: 'extension-example'
    }
  })

  // Before save - last chance to modify data before saving
  registry.add('coreshop.address.country', 'beforeSave', (data, context) => {
    console.log('[Extension Example] beforeSave hook:', data)

    // Example: Clean up data
    return {
      ...data,
      // Remove temporary fields
      _loadedAt: undefined,
      _loadedBy: undefined
    }
  })

  // After save - react to successful save
  registry.add('coreshop.address.country', 'afterSave', (data, context) => {
    console.log('[Extension Example] afterSave hook:', context?.id)

    // Example: Trigger side effects
    // notifyOtherSystems(data)
    // clearCache()

    return data
  })

  // Before delete - can prevent deletion or clean up
  registry.add('coreshop.address.country', 'beforeDelete', (data, context) => {
    console.log('[Extension Example] beforeDelete hook:', context?.id)

    // Example: Check if deletion is allowed
    // if (hasRelatedRecords(context?.id)) {
    //   throw new Error('Cannot delete country with related records')
    // }

    return data
  })

  // After delete - react to successful deletion
  registry.add('coreshop.address.country', 'afterDelete', (data, context) => {
    console.log('[Extension Example] afterDelete hook:', context?.id)

    // Example: Clean up related data
    // deleteRelatedRecords(context?.id)

    return data
  })

  console.log('[Extension Example] Lifecycle hooks registered')
}

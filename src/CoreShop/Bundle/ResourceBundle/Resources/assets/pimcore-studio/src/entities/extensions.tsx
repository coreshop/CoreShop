import React from 'react'
import { container } from '@pimcore/studio-ui-bundle'

export const entityFormExtensionsServiceId = 'CoreShop/Studio/EntityFormExtensions'
export const entityTableColumnExtensionsServiceId = 'CoreShop/Studio/EntityTableColumnExtensions'

export type EntityFormExtensionRenderer<T = any> = (ctx: {
  slotId: string
  data?: T
  onChange: (draft: Partial<T>) => void
  currentLocale?: string
  locales?: string[]
  // optional form instance for advanced extensions
  form?: any
}) => React.ReactNode

export type TableColumnExtension = {
  title: string
  dataIndex: string
  width?: number
  render: (value: any, record: any, index: number) => React.ReactNode
}

export type EntityTableColumnExtensionProvider<T = any> = (ctx: {
  slotId: string
  updateRecord: (index: number, field: string, value: any) => void
}) => TableColumnExtension[]

export class EntityTableColumnExtensionRegistry {
  private slots = new Map<string, EntityTableColumnExtensionProvider[]>()

  add(slotId: string, provider: EntityTableColumnExtensionProvider): void {
    const list = this.slots.get(slotId) ?? []
    list.push(provider)
    this.slots.set(slotId, list)
  }

  list(slotId: string): EntityTableColumnExtensionProvider[] {
    return (this.slots.get(slotId) ?? []).slice()
  }

  getColumns(slotId: string, updateRecord: (index: number, field: string, value: any) => void): TableColumnExtension[] {
    const providers = this.list(slotId)
    return providers.flatMap(provider => provider({ slotId, updateRecord }))
  }
}

export class EntityFormExtensionRegistry {
  private slots = new Map<string, EntityFormExtensionRenderer[]>()

  add(slotId: string, renderer: EntityFormExtensionRenderer): void {
    const list = this.slots.get(slotId) ?? []
    list.push(renderer)
    this.slots.set(slotId, list)
  }

  list(slotId: string): EntityFormExtensionRenderer[] {
    return (this.slots.get(slotId) ?? []).slice()
  }
}

export function getEntityFormExtensionRegistry(): EntityFormExtensionRegistry | undefined {
  try {
    return container.get<EntityFormExtensionRegistry>(entityFormExtensionsServiceId)
  } catch (e) {
    return undefined
  }
}

export function getEntityTableColumnExtensionRegistry(): EntityTableColumnExtensionRegistry | undefined {
  try {
    return container.get<EntityTableColumnExtensionRegistry>(entityTableColumnExtensionsServiceId)
  } catch (e) {
    return undefined
  }
}

export function getEntityTableColumnExtensions(slotId: string, updateRecord: (index: number, field: string, value: any) => void): TableColumnExtension[] {
  const registry = getEntityTableColumnExtensionRegistry()
  if (!registry) return []
  return registry.getColumns(slotId, updateRecord)
}

export function renderEntityFormExtensions<T = any>(slotId: string, ctx: {
  data?: T
  onChange: (draft: Partial<T>) => void
  currentLocale?: string
  locales?: string[]
  form?: any
}): React.ReactNode[] {
  const registry = getEntityFormExtensionRegistry()
  if (!registry) return []
  return registry.list(slotId).map((r, idx) => (
    <React.Fragment key={`${slotId}-${idx}`}>{r({ slotId, ...ctx })}</React.Fragment>
  ))
}

// ==========================================
// NOTE: Save Decorator Registry is in separate file: save-decorators.ts
// ==========================================
// Tab Extension Registry
// ==========================================

export const entityTabExtensionsServiceId = 'CoreShop/Studio/EntityTabExtensions'

export type TabExtension<T = any> = {
  key: string
  label: string
  icon?: string
  render: (data: T | undefined, onChange: (draft: Partial<T>) => void, ctx?: {
    currentLocale?: string
    locales?: string[]
  }) => React.ReactNode
}

export type TabExtensionProvider<T = any> = (ctx: {
  slotId: string
  data?: T
}) => TabExtension<T> | TabExtension<T>[] | null

export class EntityTabExtensionRegistry {
  private slots = new Map<string, TabExtensionProvider[]>()

  /**
   * Register a tab extension for a specific entity manager
   * @param slotId - e.g., 'coreshop.address.country', 'coreshop.taxation.tax_rate'
   * @param provider - Function that returns tab configuration(s)
   */
  add(slotId: string, provider: TabExtensionProvider): void {
    const list = this.slots.get(slotId) ?? []
    list.push(provider)
    this.slots.set(slotId, list)
  }

  /**
   * Get all tabs for a slot
   */
  getTabs<T = any>(slotId: string, data?: T): TabExtension<T>[] {
    const providers = this.slots.get(slotId) ?? []
    const tabs: TabExtension<T>[] = []

    for (const provider of providers) {
      const result = provider({ slotId, data })
      if (result) {
        tabs.push(...(Array.isArray(result) ? result : [result]))
      }
    }

    return tabs
  }
}

export function getEntityTabExtensionRegistry(): EntityTabExtensionRegistry | undefined {
  try {
    return container.get<EntityTabExtensionRegistry>(entityTabExtensionsServiceId)
  } catch (e) {
    return undefined
  }
}

// ==========================================
// Action Extension Registry
// ==========================================

export const entityActionExtensionsServiceId = 'CoreShop/Studio/EntityActionExtensions'

export type ActionExtension<T = any> = {
  key: string
  label: string
  icon?: React.ReactNode
  type?: 'primary' | 'default' | 'dashed' | 'link' | 'text'
  danger?: boolean
  disabled?: boolean
  onClick: (data?: T) => void | Promise<void>
}

export type ActionExtensionProvider<T = any> = (ctx: {
  slotId: string
  data?: T
  position: 'toolbar' | 'context-menu' | 'footer'
}) => ActionExtension<T> | ActionExtension<T>[] | null

export class EntityActionExtensionRegistry {
  private slots = new Map<string, ActionExtensionProvider[]>()

  /**
   * Register an action extension
   * @param slotId - e.g., 'coreshop.address.country.toolbar', 'coreshop.taxation.tax_rate.context'
   * @param provider - Function that returns action configuration(s)
   */
  add(slotId: string, provider: ActionExtensionProvider): void {
    const list = this.slots.get(slotId) ?? []
    list.push(provider)
    this.slots.set(slotId, list)
  }

  /**
   * Get all actions for a slot and position
   */
  getActions<T = any>(
    slotId: string,
    position: 'toolbar' | 'context-menu' | 'footer',
    data?: T
  ): ActionExtension<T>[] {
    const providers = this.slots.get(slotId) ?? []
    const actions: ActionExtension<T>[] = []

    for (const provider of providers) {
      const result = provider({ slotId, data, position })
      if (result) {
        actions.push(...(Array.isArray(result) ? result : [result]))
      }
    }

    return actions
  }
}

export function getEntityActionExtensionRegistry(): EntityActionExtensionRegistry | undefined {
  try {
    return container.get<EntityActionExtensionRegistry>(entityActionExtensionsServiceId)
  } catch (e) {
    return undefined
  }
}

// ==========================================
// Validation Extension Registry
// ==========================================

export const entityValidationExtensionsServiceId = 'CoreShop/Studio/EntityValidationExtensions'

export type ValidationResult = {
  valid: boolean
  errors?: Record<string, string[]> // field -> error messages
}

export type ValidationFunction<T = any> = (
  data: T,
  context?: { currentLocale?: string }
) => ValidationResult | Promise<ValidationResult>

export class EntityValidationExtensionRegistry {
  private validators = new Map<string, ValidationFunction[]>()

  /**
   * Register a validation function
   * @param slotId - e.g., 'coreshop.address.country', 'coreshop.taxation.tax_rate'
   * @param validator - Validation function
   */
  add(slotId: string, validator: ValidationFunction): void {
    const list = this.validators.get(slotId) ?? []
    list.push(validator)
    this.validators.set(slotId, list)
  }

  /**
   * Run all validators for a slot
   */
  async validate<T = any>(
    slotId: string,
    data: T,
    context?: { currentLocale?: string }
  ): Promise<ValidationResult> {
    const validators = this.validators.get(slotId) ?? []
    const errors: Record<string, string[]> = {}
    let valid = true

    for (const validator of validators) {
      const result = await validator(data, context)
      if (!result.valid) {
        valid = false
        if (result.errors) {
          for (const [field, messages] of Object.entries(result.errors)) {
            errors[field] = [...(errors[field] ?? []), ...messages]
          }
        }
      }
    }

    return { valid, errors: valid ? undefined : errors }
  }
}

export function getEntityValidationExtensionRegistry(): EntityValidationExtensionRegistry | undefined {
  try {
    return container.get<EntityValidationExtensionRegistry>(entityValidationExtensionsServiceId)
  } catch (e) {
    return undefined
  }
}

// ==========================================
// Lifecycle Hook Registry
// ==========================================

export const entityLifecycleHooksServiceId = 'CoreShop/Studio/EntityLifecycleHooks'

export type LifecycleHookType =
  | 'beforeLoad'
  | 'afterLoad'
  | 'beforeSave'
  | 'afterSave'
  | 'beforeDelete'
  | 'afterDelete'

export type LifecycleHookFunction<T = any> = (
  data: T,
  context?: { id?: number, locale?: string }
) => T | Promise<T> | void | Promise<void>

export class EntityLifecycleHookRegistry {
  private hooks = new Map<string, Map<LifecycleHookType, LifecycleHookFunction[]>>()

  /**
   * Register a lifecycle hook
   * @param slotId - e.g., 'coreshop.address.country', 'coreshop.taxation.tax_rate'
   * @param hookType - Type of lifecycle event
   * @param hook - Hook function
   */
  add(slotId: string, hookType: LifecycleHookType, hook: LifecycleHookFunction): void {
    if (!this.hooks.has(slotId)) {
      this.hooks.set(slotId, new Map())
    }
    const slotHooks = this.hooks.get(slotId)!
    const list = slotHooks.get(hookType) ?? []
    list.push(hook)
    slotHooks.set(hookType, list)
  }

  /**
   * Execute all hooks for a specific lifecycle event
   */
  async execute<T = any>(
    slotId: string,
    hookType: LifecycleHookType,
    data: T,
    context?: { id?: number, locale?: string }
  ): Promise<T> {
    const slotHooks = this.hooks.get(slotId)
    if (!slotHooks) return data

    const hooks = slotHooks.get(hookType) ?? []
    let currentData = data

    for (const hook of hooks) {
      const result = await hook(currentData, context)
      if (result !== undefined && result !== null) {
        currentData = result as T
      }
    }

    return currentData
  }
}

export function getEntityLifecycleHookRegistry(): EntityLifecycleHookRegistry | undefined {
  try {
    return container.get<EntityLifecycleHookRegistry>(entityLifecycleHooksServiceId)
  } catch (e) {
    return undefined
  }
}

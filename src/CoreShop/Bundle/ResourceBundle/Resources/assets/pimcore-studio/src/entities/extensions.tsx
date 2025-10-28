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

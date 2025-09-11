import React from 'react'
import { container } from '@pimcore/studio-ui-bundle'

export const entityFormExtensionsServiceId = 'CoreShop/Studio/EntityFormExtensions'

export type EntityFormExtensionRenderer<T = any> = (ctx: {
  slotId: string
  data?: T
  onChange: (draft: Partial<T>) => void
  currentLocale?: string
  locales?: string[]
  // optional form instance for advanced extensions
  form?: any
}) => React.ReactNode

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

/**
 * CoreShop Menu Bundle - Pimcore Studio Plugin
 *
 * Main entry point that registers the CoreShop Menu Extension module
 * following the Pimcore Studio plugin pattern
 */


import { injectable } from 'inversify'
import { container } from '@pimcore/studio-ui-bundle'
import { type ButtonConfig } from '../types/ButtonConfig'

const MENU_BUTTON_REGISTRY_SERVICE_ID = 'CoreShopMenuButtons'
const MENU_BUTTON_QUEUE_KEY = '__coreshopMenuButtonQueue'

declare global {
  interface Window {
    __coreshopMenuButtonQueue?: ButtonConfig[]
  }
}

const upsertButtonConfig = (items: ButtonConfig[], item: ButtonConfig): void => {
  const existingIndex = items.findIndex((entry) => entry.name === item.name)
  if (existingIndex >= 0) {
    items[existingIndex] = item
    return
  }

  items.push(item)
}

@injectable()
export class MenuButtonRegistry {
  private readonly items: ButtonConfig[] = []

  add (item: ButtonConfig): void {
    upsertButtonConfig(this.items, item)
  }

  get (name: string): ButtonConfig | undefined {
    return this.items.find((item) => item.name === name)
  }

  all (): ButtonConfig[] {
    return this.items
  }
}

export const registerMenuButton = (item: ButtonConfig): void => {
  if (container.isBound(MENU_BUTTON_REGISTRY_SERVICE_ID)) {
    const registry = container.get<MenuButtonRegistry>(MENU_BUTTON_REGISTRY_SERVICE_ID)
    registry.add(item)
    return
  }

  if (window[MENU_BUTTON_QUEUE_KEY] == null) {
    window[MENU_BUTTON_QUEUE_KEY] = []
  }

  upsertButtonConfig(window[MENU_BUTTON_QUEUE_KEY]!, item)
}

export const consumeQueuedMenuButtons = (): ButtonConfig[] => {
  const items = window[MENU_BUTTON_QUEUE_KEY] ?? []
  window[MENU_BUTTON_QUEUE_KEY] = []
  return items
}

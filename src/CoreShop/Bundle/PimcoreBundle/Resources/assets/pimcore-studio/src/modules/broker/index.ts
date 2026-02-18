/**
 * CoreShop PimcoreBundle Studio Plugin
 *
 * Event Broker for cross-component communication.
 * Mirrors the ExtJS coreshop.broker API from broker.js
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

export type ListenerCallback = (...args: any[]) => void

interface Listener {
  callback: ListenerCallback
  scope?: any
  once: boolean
  priority: number
}

// Extend window type for global broker
declare global {
  interface Window {
    __CORESHOP_BROKER__?: CoreShopBroker
  }
}

/**
 * CoreShop Event Broker
 *
 * Provides a pub/sub system for communication between Dynamic Types
 * and other components. API mirrors ExtJS coreshop.broker.
 *
 * Usage:
 *   coreshopBroker.fireEvent('eventName', data)
 *   coreshopBroker.addListener('eventName', callback)
 *   coreshopBroker.removeListener('eventName', callback)
 */
class CoreShopBroker {
  private listeners = new Map<string, Listener[]>()

  /**
   * Add a listener for an event
   */
  addListener(
    name: string,
    callback: ListenerCallback,
    scope?: any,
    once = false,
    priority = 0
  ): void {
    if (!this.listeners.has(name)) {
      this.listeners.set(name, [])
    }
    this.listeners.get(name)!.push({ callback, scope, once, priority })
  }

  /**
   * Add a listener that will be automatically removed after first execution
   */
  addListenerOnce(
    name: string,
    callback: ListenerCallback,
    scope?: any
  ): void {
    this.addListener(name, callback, scope, true, 0)
  }

  /**
   * Remove a listener
   */
  removeListener(name: string, callback: ListenerCallback): void {
    const list = this.listeners.get(name)
    if (!list) return

    const idx = list.findIndex((l) => l.callback === callback)
    if (idx >= 0) {
      list.splice(idx, 1)
    }

    if (list.length === 0) {
      this.listeners.delete(name)
    }
  }

  /**
   * Fire an event to all registered listeners
   */
  fireEvent(name: string, ...args: any[]): void {
    const list = this.listeners.get(name)
    if (!list) return

    // Sort by priority (lower priority executes first, like ExtJS)
    const sorted = [...list].sort((a, b) => a.priority - b.priority)

    for (const listener of sorted) {
      listener.callback.apply(listener.scope, args)

      if (listener.once) {
        this.removeListener(name, listener.callback)
      }
    }
  }

  /**
   * Check if there are listeners for an event
   */
  hasListeners(name: string): boolean {
    return (this.listeners.get(name)?.length ?? 0) > 0
  }
}

// Use global window singleton to ensure all bundles share the same instance
// This bypasses module federation isolation issues
function getGlobalBroker(): CoreShopBroker {
  if (!window.__CORESHOP_BROKER__) {
    window.__CORESHOP_BROKER__ = new CoreShopBroker()
  }
  return window.__CORESHOP_BROKER__
}

export const coreshopBroker = getGlobalBroker()

// Event name constants for type safety
export const CORESHOP_EVENTS = {
  UNIT_DEFINITIONS_CHANGE: 'pimcore.object.tags.coreShopProductUnitDefinitions.change',

  // Grid events (also exported from @coreshop/pimcore via GRID_EVENTS)
  GRID_FILTER_CHANGED: 'coreshop.grid.filter.changed',
  GRID_ACTION_EXECUTED: 'coreshop.grid.action.executed',
  GRID_TOOLBAR_ENHANCING: 'coreshop.grid.toolbar.enhancing',
  GRID_CONTEXT_MENU_BUILDING: 'coreshop.grid.contextmenu.building'
} as const

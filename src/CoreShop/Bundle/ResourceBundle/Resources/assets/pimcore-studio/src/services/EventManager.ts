/**
 * CoreShop Event Manager
 * 
 * Handles event management and broker functionality
 * This replaces the ExtJS event system with a modern TypeScript implementation
 */

import { CoreShopEvents, EventListener } from '@/types'

export class EventManager {
  private listeners: Map<keyof CoreShopEvents, EventListener[]> = new Map()
  
  /**
   * Add an event listener
   */
  addListener<K extends keyof CoreShopEvents>(
    event: K, 
    listener: EventListener<CoreShopEvents[K]>
  ): void {
    if (!this.listeners.has(event)) {
      this.listeners.set(event, [])
    }
    
    this.listeners.get(event)!.push(listener)
  }

  /**
   * Remove an event listener
   */
  removeListener<K extends keyof CoreShopEvents>(
    event: K, 
    listener: EventListener<CoreShopEvents[K]>
  ): void {
    const eventListeners = this.listeners.get(event)
    if (eventListeners) {
      const index = eventListeners.indexOf(listener)
      if (index > -1) {
        eventListeners.splice(index, 1)
      }
    }
  }

  /**
   * Fire an event to all listeners
   */
  fireEvent<K extends keyof CoreShopEvents>(
    event: K, 
    ...args: CoreShopEvents[K] extends undefined ? [] : [CoreShopEvents[K]]
  ): void {
    const eventListeners = this.listeners.get(event)
    if (eventListeners) {
      eventListeners.forEach(listener => {
        try {
          listener(args[0] as any)
        } catch (error) {
          console.error(`Error in event listener for ${String(event)}:`, error)
        }
      })
    }
  }

  /**
   * Check if there are listeners for an event
   */
  hasListeners<K extends keyof CoreShopEvents>(event: K): boolean {
    const eventListeners = this.listeners.get(event)
    return Boolean(eventListeners && eventListeners.length > 0)
  }

  /**
   * Get the number of listeners for an event
   */
  getListenerCount<K extends keyof CoreShopEvents>(event: K): number {
    const eventListeners = this.listeners.get(event)
    return eventListeners ? eventListeners.length : 0
  }

  /**
   * Clear all listeners for an event, or all events if no event specified
   */
  clearListeners<K extends keyof CoreShopEvents>(event?: K): void {
    if (event) {
      this.listeners.delete(event)
    } else {
      this.listeners.clear()
    }
  }
}

// Global event manager instance (singleton)
export const eventManager = new EventManager()

// Legacy compatibility helpers
export const coreshop = {
  broker: eventManager,
  global: {} as any,
  class_map: {} as Record<string, string>,
  stack: [] as string[],
  full_stack: [] as string[]
}
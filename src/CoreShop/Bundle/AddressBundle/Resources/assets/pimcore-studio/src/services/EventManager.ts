/**
 * CoreShop Event Manager (AddressBundle copy)
 * 
 * Simplified version for AddressBundle usage
 */

export interface EventListener<T = any> {
  (detail: T): void
}

export interface CoreShopEvents {
  'resource.register': { name: string; resource: any }
  'menu.open': { item?: any; type?: string }
  'pimcore.ready': any
}

export class EventManager {
  private listeners: Map<keyof CoreShopEvents, EventListener[]> = new Map()
  
  addListener<K extends keyof CoreShopEvents>(
    event: K, 
    listener: EventListener<CoreShopEvents[K]>
  ): void {
    if (!this.listeners.has(event)) {
      this.listeners.set(event, [])
    }
    this.listeners.get(event)!.push(listener)
  }

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
}

export const eventManager = new EventManager()

export const coreshop = {
  broker: eventManager,
  global: {} as any,
  address: {} as any
}
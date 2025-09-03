/**
 * CoreShop Resource Plugin
 * 
 * Main plugin class that initializes the resource management system
 * Replaces the ExtJS plugin system with React-based components
 */

import { ResourceService } from './services/ResourceService'
import { eventManager, coreshop } from './services/EventManager'
import { CoreShopResource } from './types'

export class ResourcePlugin {
  private resourceService: ResourceService
  private resources: Map<string, any> = new Map()

  constructor() {
    this.resourceService = new ResourceService()
    this.initialize()
  }

  private async initialize() {
    // Initialize class map (equivalent to the original ExtJS initialization)
    try {
      const classMapData = await this.resourceService.getClassMap()
      coreshop.class_map = classMapData.classMap
      coreshop.stack = classMapData.stack
      coreshop.full_stack = classMapData.full_stack

      eventManager.fireEvent('afterClassMap', coreshop.class_map)
    } catch (error) {
      console.error('Failed to load class map:', error)
    }

    // Set up global resource manager
    coreshop.global.resource = {
      open: this.openResource.bind(this),
      register: this.registerResource.bind(this)
    }

    // Listen for menu open events
    eventManager.addListener('menu.open', (detail) => {
      if (detail.item?.attributes?.resource) {
        this.openResource(detail.item.attributes.resource, detail.item.attributes.function)
      }
    })

    // Register resource event listener
    eventManager.addListener('resource.register', ({ name, resource }) => {
      this.registerResource(name, resource)
    })

    console.log('[CoreShop Resource Plugin] Initialized successfully')
  }

  /**
   * Register a resource handler
   */
  registerResource(name: string, resource: any): void {
    this.resources.set(name, resource)
    console.log(`[CoreShop Resource Plugin] Registered resource: ${name}`)
  }

  /**
   * Open a resource
   */
  openResource(module: string, resource?: string): void {
    const resourceHandler = this.resources.get(module)
    
    if (resourceHandler && typeof resourceHandler.openResource === 'function') {
      resourceHandler.openResource(resource)
    } else {
      console.warn(`[CoreShop Resource Plugin] No handler found for resource: ${module}`)
    }
  }

  /**
   * Get all registered resources
   */
  getRegisteredResources(): string[] {
    return Array.from(this.resources.keys())
  }

  /**
   * Deep clone utility for stores (equivalent to the original coreshop.deepCloneStore)
   */
  static deepCloneStore<T = CoreShopResource>(source: T[]): T[] {
    return source.map(item => ({ ...item }))
  }
}

// Legacy compatibility
declare global {
  interface Window {
    coreshop: typeof coreshop
  }
}

// Expose coreshop globally for backward compatibility
if (typeof window !== 'undefined') {
  window.coreshop = coreshop
}
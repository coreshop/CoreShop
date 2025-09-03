/**
 * CoreShop Menu Service
 * 
 * Handles menu data loading and navigation actions
 */

import { CoreShopMenuItem, MenuContext } from '@/types'

export class MenuService {
  /**
   * Load all CoreShop menu structures from backend API
   */
  async getAllMenuStructures(context?: MenuContext): Promise<CoreShopMenuItem[]> {
    try {
      // Call API without type parameter to get all menus
      const response = await fetch(`/admin/coreshop/menus`, {
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })

      if (!response.ok) {
        throw new Error(`Failed to load menus: ${response.statusText}`)
      }

      const data = await response.json()
      
      if (!data.success) {
        throw new Error(data.message || 'Failed to load menus')
      }

      return data.items || []
    } catch (error) {
      console.error('Error loading menu structures:', error)
      return []
    }
  }

  /**
   * Load specific menu structure from backend API (for backwards compatibility)
   */
  async getMenuStructure(type: string = 'coreshop.main', context?: MenuContext): Promise<CoreShopMenuItem[]> {
    try {
      const params = new URLSearchParams({ type })
      const response = await fetch(`${this.baseUrl}/menu/json?${params}`, {
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })

      if (!response.ok) {
        throw new Error(`Failed to load menu: ${response.statusText}`)
      }

      const data = await response.json()
      
      if (!data.success) {
        throw new Error(data.message || 'Failed to load menu')
      }

      return data.items || []
    } catch (error) {
      console.error('Error loading menu structure:', error)
      return []
    }
  }

  /**
   * Transform KnpMenu items to CoreShopMenuItem format
   */
  private transformKnpMenuItems(items: any[]): CoreShopMenuItem[] {
    return items.map(item => {
      const transformed: CoreShopMenuItem = {
        id: item.id || item.name || 'unnamed',
        label: item.label || item.name || 'Unnamed'
      }

      if (item.path || item.uri) {
        transformed.path = item.path || item.uri
      }

      if (item.iconCls) {
        transformed.icon = item.iconCls
      }

      if (item.attributes) {
        // Copy custom attributes
        if (item.attributes.permission) {
          transformed.permission = item.attributes.permission
        }
        if (item.attributes.badge) {
          transformed.badge = item.attributes.badge
        }
        if (item.attributes.disabled) {
          transformed.disabled = item.attributes.disabled
        }
      }

      if (item.children && item.children.length > 0) {
        transformed.children = this.transformKnpMenuItems(item.children)
      }

      return transformed
    })
  }

  /**
   * Handle navigation to a CoreShop resource
   */
  async navigate(item: CoreShopMenuItem): Promise<void> {
    if (item.path) {
      // Use Studio UI navigation API
      if (window.pimcore?.studio?.navigate) {
        await window.pimcore.studio.navigate(item.path)
      } else {
        // Fallback to window.location
        window.location.hash = item.path
      }
    } else if (item.onClick) {
      item.onClick()
    }
  }

  /**
   * Check if user has permission for menu item
   */
  hasPermission(item: CoreShopMenuItem, userPermissions: string[]): boolean {
    if (!item.permission) {
      return true
    }

    return userPermissions.includes(item.permission)
  }

  /**
   * Get menu item by ID
   */
  findMenuItem(items: CoreShopMenuItem[], id: string): CoreShopMenuItem | null {
    for (const item of items) {
      if (item.id === id) {
        return item
      }
      
      if (item.children) {
        const found = this.findMenuItem(item.children, id)
        if (found) return found
      }
    }
    
    return null
  }
}

// Global menu service instance
export const menuService = new MenuService()

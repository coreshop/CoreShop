/**
 * CoreShop Menu Service
 * 
 * Handles menu data loading and navigation actions
 */

import { CoreShopMenuItem, MenuContext } from '../types'

export class MenuService {
  /**
   * Load all CoreShop menu structures from backend API
   */
  async getAllMenuStructures(context?: MenuContext): Promise<Array<CoreShopMenuItem[]>> {
    try {
      // Call API without type parameter to get all menus
      const response = await fetch(`/pimcore-studio/api/coreshop/menus`, {
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
}

// Global menu service instance
export const menuService = new MenuService()

/**
 * CoreShop Menu Bundle Types
 * 
 * TypeScript type definitions for menu functionality
 */

export interface CoreShopMenuItem {
  id: string
  label: string
  icon?: string
  content?: string
  path?: string
  children?: CoreShopMenuItem[]
  onClick?: () => void
  badge?: {
    count: number
    color?: string
  }
  disabled?: boolean
  permission?: string
  widgetId?: string
  widgetEvent?: string
  widgetButton?: string
}

export interface MenuCategory {
  id: string
  label: string
  order?: number
  icon?: string | React.ReactNode
  items: CoreShopMenuItem[]
}

export interface MenuConfig {
  categories: MenuCategory[]
  defaultCategory?: string
}

// Menu items based on Classic Admin structure
export interface CoreShopMenuStructure {
  sales: CoreShopMenuItem[]
  catalog: CoreShopMenuItem[]
  customers: CoreShopMenuItem[]
  marketing: CoreShopMenuItem[]
  localization: CoreShopMenuItem[]
  system: CoreShopMenuItem[]
  reports: CoreShopMenuItem[]
}

// Navigation event types
export interface NavigationEvent {
  type: 'navigate' | 'open-modal' | 'open-tab'
  target: string
  params?: Record<string, any>
}

export interface MenuPermission {
  resource: string
  permission: string
}

export interface MenuContext {
  user: {
    id: number
    name: string
    permissions: string[]
  }
  language: string
  workspace?: string
}
/**
 * CoreShop Resource Bundle Types
 * 
 * TypeScript type definitions for the Resource Bundle functionality
 */

export interface CoreShopResource {
  id: number
  name: string
  [key: string]: any
}

export interface ResourceListResponse<T = CoreShopResource> {
  success: boolean
  data: T[]
  total?: number
}

export interface ResourceResponse<T = CoreShopResource> {
  success: boolean
  data: T
  message?: string
}

export interface ResourcePanelConfig {
  layoutId: string
  storeId: string
  iconCls: string
  type: string
  title: string
  routing: {
    add: string
    delete: string
    get: string
    list: string
  }
}

export interface ResourceItemConfig extends ResourcePanelConfig {
  data: CoreShopResource
  panelKey: string
}

export interface GridColumn {
  dataIndex: string
  title: string
  width?: number
  render?: (value: any, record: CoreShopResource) => React.ReactNode
}

export interface EventDetail {
  item?: any
  type?: string
  object?: any
  params?: any
  broker?: any
}

// Event listener types matching the original ExtJS events
export type EventListener<T = any> = (detail: T) => void

export interface CoreShopEvents {
  'menu.open': EventDetail
  'pimcore.ready': EventDetail
  'pimcore.preOpenObject': EventDetail
  'pimcore.postOpenObject': EventDetail
  'pimcore.preOpenAsset': EventDetail
  'pimcore.postOpenAsset': EventDetail
  'pimcore.preOpenDocument': EventDetail
  'pimcore.postOpenDocument': EventDetail
  'resource.register': { name: string; resource: any }
  'afterClassMap': any
}
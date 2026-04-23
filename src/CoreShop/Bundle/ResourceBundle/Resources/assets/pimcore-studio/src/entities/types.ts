export interface EntityListItem {
  id: number
  name: string
  identifier?: string
  active?: boolean
}

export interface EntityGetResponse<T> {
  data: T
  success: boolean
}

export interface EntityListResponse<T extends EntityListItem = EntityListItem> extends Array<T> {}

export interface EntityApiConfig {
  basePath: string // e.g. '/pimcore-studio/api'
  resourcePath: string // e.g. '/coreshop/zones'
  routes?: {
    list: string
    get: string
    add: string
    save: string
    delete: string
  }
}

export type Id = number | string


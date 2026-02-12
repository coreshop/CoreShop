import { type EntityApiConfig, type EntityGetResponse, type EntityListItem, type EntityListResponse, type Id } from './types'

const buildUrl = (base: string, resource: string, route: string): string => {
  return `${base}${resource}${route}`
}

export class EntityApi<TDetail extends Record<string, any> = any> {
  protected readonly cfg: Required<EntityApiConfig>

  constructor(config: EntityApiConfig) {
    this.cfg = {
      ...config,
      // ensure routes are filled with defaults
      routes: {
        list: config.routes?.list ?? '/list',
        get: config.routes?.get ?? '/get',
        add: config.routes?.add ?? '/add',
        save: config.routes?.save ?? '/save',
        delete: config.routes?.delete ?? '/delete'
      }
    }
  }

  async list(): Promise<EntityListResponse> {
    const url = buildUrl(this.cfg.basePath, this.cfg.resourcePath, this.cfg.routes.list)
    const res = await fetch(url, { credentials: 'same-origin' })
    if (!res.ok) throw new Error(`List request failed: ${res.status}`)
    const data = await res.json()
    return data as EntityListResponse
  }

  async get(id: Id): Promise<EntityGetResponse<TDetail>> {
    const url = buildUrl(this.cfg.basePath, this.cfg.resourcePath, this.cfg.routes.get) + `?id=${encodeURIComponent(String(id))}`
    const res = await fetch(url, { credentials: 'same-origin' })
    if (!res.ok) throw new Error(`Get request failed: ${res.status}`)
    return await res.json() as EntityGetResponse<TDetail>
  }

  async add(payload: Record<string, any>): Promise<EntityGetResponse<TDetail>> {
    const url = buildUrl(this.cfg.basePath, this.cfg.resourcePath, this.cfg.routes.add)
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(payload)
    })
    if (!res.ok) throw new Error(`Add request failed: ${res.status}`)
    const data = await res.json() as EntityGetResponse<TDetail>

    // Check if response indicates failure
    if ('success' in data && data.success === false) {
      const message = (data as any).message || 'Add request failed'
      throw new Error(message)
    }

    return data
  }

  async save(payload: Record<string, any>): Promise<EntityGetResponse<TDetail>> {
    const url = buildUrl(this.cfg.basePath, this.cfg.resourcePath, this.cfg.routes.save)
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(payload)
    })
    if (!res.ok) throw new Error(`Save request failed: ${res.status}`)
    const data = await res.json() as EntityGetResponse<TDetail>

    // Check if response indicates failure
    if ('success' in data && data.success === false) {
      const message = (data as any).message || 'Save request failed'
      throw new Error(message)
    }

    return data
  }

  async delete(id: Id): Promise<{ success: boolean }> {
    const url = buildUrl(this.cfg.basePath, this.cfg.resourcePath, this.cfg.routes.delete)
    const body = `id=${encodeURIComponent(String(id))}`

    const res = await fetch(url, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin',
      body
    })
    if (!res.ok) throw new Error(`Delete request failed: ${res.status}`)
    return await res.json()
  }
}
